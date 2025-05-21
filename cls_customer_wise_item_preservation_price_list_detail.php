<?php
include_once(__DIR__ . "/../config/connection.php");
class mdl_customerwiseitempreservationpricelistdetail 
{                        
    public $customer_wise_item_preservation_price_list_detail_id;     
    public $customer_wise_item_preservation_price_list_id;     
    public $packing_unit_id;     
    public $rent_per_qty_month;     
    public $rent_per_qty_season;  
    public $detailtransactionmode;
    // Add these two lines to fix deprecated property warnings
    public $_item_id;
    public $_customer_id;
}
class bll_customerwiseitempreservationpricelistdetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl = new mdl_customerwiseitempreservationpricelistdetail(); 
        $this->_dal = new dal_customerwiseitempreservationpricelistdetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
    }
    
    public function getDetailsByMasterId($masterId) {
    global $_dbh;
    $details = [];
    
    try {
        $sql = "SELECT * FROM tbl_customer_wise_item_preservation_price_list_detail 
                WHERE customer_wise_item_preservation_price_list_id = ?";
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
    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;

    if ($item_id > 0 && $customer_id > 0) {
        $sql = "
            SELECT 
                pum.packing_unit_id, 
                pum.packing_unit_name, 
                COALESCE(cwippl.rent_per_qty_month, ippld.rent_per_qty_month, '0.00') AS rent_per_qty_month,
                COALESCE(cwippl.rent_per_qty_season, ippld.rent_per_qty_season, '0.00') AS rent_per_qty_season,
                cwippl.customer_wise_item_preservation_price_list_detail_id
            FROM 
                tbl_packing_unit_master pum
            LEFT JOIN (
                SELECT 
                    d.packing_unit_id, 
                    d.rent_per_qty_month, 
                    d.rent_per_qty_season,
                    d.customer_wise_item_preservation_price_list_detail_id
                FROM 
                    tbl_customer_wise_item_preservation_price_list_detail d
                INNER JOIN 
                    tbl_customer_wise_item_preservation_price_list_master m 
                    ON d.customer_wise_item_preservation_price_list_id = m.customer_wise_item_preservation_price_list_id
                WHERE m.item_id = :item_id AND m.customer_id = :customer_id
            ) cwippl ON pum.packing_unit_id = cwippl.packing_unit_id
            LEFT JOIN (
                SELECT 
                    d.packing_unit_id, 
                    d.rent_per_qty_month, 
                    d.rent_per_qty_season
                FROM 
                    tbl_item_preservation_price_list_detail d
                INNER JOIN 
                    tbl_item_preservation_price_list_master m 
                    ON d.item_preservation_price_list_id = m.item_preservation_price_list_id
                WHERE m.item_id = :item_id
            ) ippld ON pum.packing_unit_id = ippld.packing_unit_id
            WHERE pum.status = 1
            ORDER BY pum.packing_unit_name
        ";

        $stmt = $_dbh->prepare($sql);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($result)) {
            foreach ($result as $row) {
                $detailId = $row['customer_wise_item_preservation_price_list_detail_id'] ?? '';
                $isNew = empty($detailId) ? '1' : '0';

                $_grid .= "
                <tr 
                    data-id=\"{$row['packing_unit_id']}\"
                    data-detail-id=\"{$detailId}\" 
                    data-is-new=\"{$isNew}\">
                    <td>{$row['packing_unit_name']}</td>
                    <td contenteditable=\"true\" 
                        class=\"editable rent-monthly\" 
                        data-field=\"rent_per_qty_month\" 
                        data-original=\"{$row['rent_per_qty_month']}\">
                        {$row['rent_per_qty_month']}
                    </td>
                    <td contenteditable=\"true\" 
                        class=\"editable rent-seasonal\" 
                        data-field=\"rent_per_qty_season\" 
                        data-original=\"{$row['rent_per_qty_season']}\">
                        {$row['rent_per_qty_season']}
                    </td>
                </tr>";
            }
        } else {
            $_grid .= "
            <tr class=\"norecords\">
                <td colspan=\"3\">No packing units found for the selected item and customer.</td>
            </tr>";
        }
    } else {
        $_grid .= "
        <tr class=\"norecords\">
            <td colspan=\"3\">Please select both Customer and Item.</td>
        </tr>";
    }

    $_grid .= "
            </tbody>
        </table>
    </div>";

    echo $_grid;
}



}

if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action === 'fetch_units' && isset($_POST['item_id']) && isset($_POST['customer_id'])) {
        $bll = new bll_customerwiseitempreservationpricelistdetail();
        $bll->pageSearch();
        exit;
    }
}

class dal_customerwiseitempreservationpricelistdetail
{
public function dbTransaction($_mdl)
{
    global $_dbh;

    try {
        $insertRequired = false;

        if ($_mdl->detailtransactionmode === 'U') {

            // Fetch current packing_unit_id
            $stmt = $_dbh->prepare("SELECT packing_unit_id FROM tbl_customer_wise_item_preservation_price_list_detail 
                                    WHERE customer_wise_item_preservation_price_list_detail_id = ?");
            $stmt->bindParam(1, $_mdl->customer_wise_item_preservation_price_list_detail_id, PDO::PARAM_INT);
            $stmt->execute();
            $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($currentData && intval($currentData['packing_unit_id']) !== intval($_mdl->packing_unit_id)) {
                // Packing unit changed — delete and mark for insert
                $stmt = $_dbh->prepare("DELETE FROM tbl_customer_wise_item_preservation_price_list_detail 
                                        WHERE customer_wise_item_preservation_price_list_detail_id = ?");
                $stmt->bindParam(1, $_mdl->customer_wise_item_preservation_price_list_detail_id, PDO::PARAM_INT);
                $stmt->execute();

                $_mdl->detailtransactionmode = 'I';
                $_mdl->customer_wise_item_preservation_price_list_detail_id = null;
                $insertRequired = true;

                $_dbh->exec("SET @p_customer_wise_item_preservation_price_list_detail_id = NULL");
            } else {
                // Packing unit unchanged — update rent values
                $stmt = $_dbh->prepare("UPDATE tbl_customer_wise_item_preservation_price_list_detail
                                        SET rent_per_qty_month = ?, 
                                            rent_per_qty_season = ?
                                        WHERE customer_wise_item_preservation_price_list_detail_id = ?");
                $stmt->bindParam(1, $_mdl->rent_per_qty_month, PDO::PARAM_STR);
                $stmt->bindParam(2, $_mdl->rent_per_qty_season, PDO::PARAM_STR);
                $stmt->bindParam(3, $_mdl->customer_wise_item_preservation_price_list_detail_id, PDO::PARAM_INT);
                $stmt->execute();

                return true;
            }
        }

        // Insert or re-insert
        if ($_mdl->detailtransactionmode === 'I' || $insertRequired) {

            $idToUse = $_mdl->customer_wise_item_preservation_price_list_detail_id ?? 'NULL';
            $_dbh->exec("SET @p_customer_wise_item_preservation_price_list_detail_id = " . $idToUse);

            $stmt = $_dbh->prepare("CALL customer_wise_item_preservation_price_list_detail_transaction(
                @p_customer_wise_item_preservation_price_list_detail_id,
                ?, ?, ?, ?, ?
            )");

            $stmt->bindParam(1, $_mdl->customer_wise_item_preservation_price_list_id, PDO::PARAM_INT);
            $stmt->bindParam(2, $_mdl->packing_unit_id, PDO::PARAM_INT);
            $stmt->bindParam(3, $_mdl->rent_per_qty_month, PDO::PARAM_STR);
            $stmt->bindParam(4, $_mdl->rent_per_qty_season, PDO::PARAM_STR);
            $stmt->bindParam(5, $_mdl->detailtransactionmode, PDO::PARAM_STR);
            $stmt->execute();

            $result = $_dbh->query("SELECT @p_customer_wise_item_preservation_price_list_detail_id AS new_id");
            $newId = $result->fetchColumn();
            $_mdl->customer_wise_item_preservation_price_list_detail_id = $newId;
        }

    } catch (PDOException $e) {
        error_log("Error in customerwise detail transaction: " . $e->getMessage());
        throw $e;
    }

    return true;
}

}





