<?php
include_once(__DIR__ . "/../config/connection.php");
class mdl_itempreservationpricelistdetail 
{                        
public $item_preservation_price_list_detail_id;     
                  
    public $item_preservation_price_list_id;     
                  
    public $packing_unit_id;     
                  
    public $rent_per_qty_month;     
                  
    public $rent_per_qty_season;     
                  
    public $detailtransactionmode;
}

class bll_itempreservationpricelistdetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_itempreservationpricelistdetail(); 
        $this->_dal =new dal_itempreservationpricelistdetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
    }
    public function getDetailsByMasterId($masterId) {
    global $_dbh;
    $details = [];
    
    try {
        $sql = "SELECT * FROM tbl_item_preservation_price_list_detail 
                WHERE item_preservation_price_list_id = ?";
        $stmt = $_dbh->prepare($sql);
        $stmt->execute([$masterId]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching details: " . $e->getMessage());
    }
    
    return $details;
}
    public function pageSearch()
{
    global $_dbh;

    $_grid = "
    <div id=\"gridContainer\" class=\"table-responsive\" style=\"width: 100%; display: block;\">
        <table id=\"dataGrid\" class=\"table table-bordered table-striped text-center align-middle\">
            <thead class=\"thead-dark\">
                <tr>
                    <th>Packing Unit Name</th>
                    <th>Rent/Month/Qty</th>
                    <th>Rent/Season/Qty</th>
                </tr>
            </thead>
            <tbody id=\"gridBody\">";

    $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;

    if ($item_id <= 0) {
        $_grid .= "<tr><td colspan=\"4\">No data available. Please select an item.</td></tr>";
        $_grid .= "</tbody></table></div>";
        echo $_grid;
        return;
    }

    try {
        $sql = "SELECT 
                    pum.packing_unit_id, 
                    pum.packing_unit_name, 
                    COALESCE(ippl.rent_per_qty_month, '0.00') AS rent_per_qty_month, 
                    COALESCE(ippl.rent_per_qty_season, '0.00') AS rent_per_qty_season,
                    ippl.item_preservation_price_list_detail_id
                FROM 
                    tbl_packing_unit_master pum
                LEFT JOIN (
                    SELECT 
                        d.packing_unit_id, 
                        d.rent_per_qty_month, 
                        d.rent_per_qty_season,
                        d.item_preservation_price_list_detail_id
                    FROM 
                        tbl_item_preservation_price_list_detail d
                    INNER JOIN 
                        tbl_item_preservation_price_list_master m 
                        ON d.item_preservation_price_list_id = m.item_preservation_price_list_id
                    WHERE m.item_id = :item_id
                ) ippl ON pum.packing_unit_id = ippl.packing_unit_id
                WHERE pum.status = 1";

        $stmt = $_dbh->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            foreach ($result as $_rs) {
                $_grid .= "
                    <tr 
                        data-id=\"{$_rs['packing_unit_id']}\" 
                        data-detail-id=\"" . ($_rs['item_preservation_price_list_detail_id'] ?? '') . "\">
                        <td style=\"background-color: #f0f0f0;\">{$_rs['packing_unit_name']}</td>
                        <td contenteditable=\"true\" class=\"editable rent-monthly\" 
                            data-field=\"rent_per_qty_month\" 
                            data-original=\"{$_rs['rent_per_qty_month']}\">
                            {$_rs['rent_per_qty_month']}
                        </td>
                        <td contenteditable=\"true\" class=\"editable rent-seasonal\" 
                            data-field=\"rent_per_qty_season\" 
                            data-original=\"{$_rs['rent_per_qty_season']}\">
                            {$_rs['rent_per_qty_season']}
                        </td>
                    </tr>";
            }
        } else {
            $_grid .= "<tr><td colspan=\"4\">No packing units found for the selected item.</td></tr>";
        }

    } catch (PDOException $e) {
        error_log("Error in pageSearch: " . $e->getMessage());
        echo "<div class='alert alert-danger'>Error fetching data.</div>";
        return;
    }

    $_grid .= "</tbody></table></div>";
    echo $_grid;
}
}
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    // Fetch units for an item
    if ($action === 'fetch_units' && isset($_POST['item_id'])) {
        $bll = new bll_itempreservationpricelistdetail();
        $bll->pageSearch();
        exit;
    }
}
class dal_itempreservationpricelistdetail                         
{
public function dbTransaction($mdl)
{
    global $_dbh;

    try {
        if ($mdl->detailtransactionmode === 'U') {
       
            $stmt = $_dbh->prepare("SELECT packing_unit_id FROM tbl_item_preservation_price_list_detail 
                                   WHERE item_preservation_price_list_detail_id = ?");
            $stmt->bindParam(1, $mdl->item_preservation_price_list_detail_id, PDO::PARAM_INT);
            $stmt->execute();
            $currentData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($currentData && $currentData['packing_unit_id'] != $mdl->packing_unit_id) {
             
                $_dbh->exec("DELETE FROM tbl_item_preservation_price_list_detail 
                            WHERE item_preservation_price_list_detail_id = " . $mdl->item_preservation_price_list_detail_id);
                $mdl->detailtransactionmode = 'I';
                $mdl->item_preservation_price_list_detail_id = null;
           
                $_dbh->exec("SET @p_item_preservation_price_list_detail_id = NULL");
                $stmt = $_dbh->prepare("CALL item_preservation_price_list_detail_transaction(
                    @p_item_preservation_price_list_detail_id, 
                    ?, 
                    ?, 
                    ?, 
                    ?, 
                    ?
                )");
            } else {
               
                $stmt = $_dbh->prepare("UPDATE tbl_item_preservation_price_list_detail
                                       SET rent_per_qty_month = ?, 
                                           rent_per_qty_season = ?
                                       WHERE item_preservation_price_list_detail_id = ?");
                $stmt->bindParam(1, $mdl->rent_per_qty_month, PDO::PARAM_STR);
                $stmt->bindParam(2, $mdl->rent_per_qty_season, PDO::PARAM_STR);
                $stmt->bindParam(3, $mdl->item_preservation_price_list_detail_id, PDO::PARAM_INT);
                $stmt->execute();
                return;
            }
        } 
        
        if ($mdl->detailtransactionmode === 'I') {
            $_dbh->exec("SET @p_item_preservation_price_list_detail_id = " . 
                       ($mdl->item_preservation_price_list_detail_id ?? 'NULL'));

            $stmt = $_dbh->prepare("CALL item_preservation_price_list_detail_transaction(
                @p_item_preservation_price_list_detail_id, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?
            )");

            $stmt->bindParam(1, $mdl->item_preservation_price_list_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $mdl->packing_unit_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $mdl->rent_per_qty_month, PDO::PARAM_STR);
            $stmt->bindParam(4, $mdl->rent_per_qty_season, PDO::PARAM_STR);
            $stmt->bindParam(5, $mdl->detailtransactionmode, PDO::PARAM_STR);
            $stmt->execute();
            $result = $_dbh->query("SELECT @p_item_preservation_price_list_detail_id AS new_id");
            $newId = $result->fetchColumn();
            $mdl->item_preservation_price_list_detail_id = $newId;
        }

    } catch (PDOException $e) {
        error_log("Error in detail transaction: " . $e->getMessage());
        throw $e;
    }
}
}