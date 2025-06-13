<?php
    class mdl_inwarddetail 
{                        
public $inward_detail_id;     
                  
    public $inward_id;     
                  
    public $lot_no;     
                  
    public $item;     
                  
    public $gst_type;     
                  
    public $variety;     
                  
    public $packing_unit;     
                  
    public $inward_qty;     
                  
    public $inward_wt;     
                  
    public $avg_wt_per_bag;     
                  
    public $location;     
                  
    public $moisture;     
                  
    public $storage_duration;     
                  
    public $rent_per_month;     
                  
    public $rent_per_storage_duration;     
                  
    public $seasonal_start_date;     
                  
    public $seasonal_end_date;     
                  
    public $rent_per;  
        
    public $unloading_charge;     
                  
    public $remark;     
                  
    public $detailtransactionmode;
}

class bll_inwarddetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_inwarddetail(); 
        $this->_dal =new dal_inwarddetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
    }
public function pageSearch()
{
    global $_dbh;
    $_grid = "
    <table  id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
    <thead id=\"tableHead\">
        <tr>
        <th>Action</th>
        <th> Lot No </th>
        <th> Item </th>
        <th> Gst Type </th>
        <th> Variety </th>
        <th> Packing Unit </th>
        <th> Inward Qty </th>
        <th> Inward Wt </th>
        <th> Avg Wt Per Bag </th>
        <th> Location </th>
        <th> Moisture </th>
        <th> Storage Duration </th>
        <th> Rent Per Month </th>
        <th> Rent Per Storage Duration </th>
        </tr>
    </thead>";
    $i = 0;
    $result = array();
    $main_id_name = "inward_id";
    if (isset($_POST[$main_id_name]))
        $main_id = $_POST[$main_id_name];
    else
        $main_id = $this->_mdl->$main_id_name;

    if ($main_id) {
        // In the pageSearch() method, modify the SQL query:
$sql = "CALL csms1_search_detail(
    't.*, t3.item_name, t.item, t6.packing_unit_name, t.packing_unit, t7.value as gst_type_value',
    'tbl_inward_detail t 
     INNER JOIN tbl_item_master t3 ON t.item = t3.item_id 
     INNER JOIN tbl_packing_unit_master t6 ON t.packing_unit = t6.packing_unit_id
     LEFT JOIN view_item_gst_type t7 ON t.gst_type = t7.id',
    't." . $main_id_name . " = " . $main_id . "')";
        $result = $_dbh->query($sql, PDO::FETCH_ASSOC);
    }

    $_grid .= "<tbody id=\"tableBody\">";
    if (!empty($result)) {
        foreach ($result as $_rs) {
            $detail_id_label = "inward_detail_id";
            $detail_id = $_rs[$detail_id_label];
            $_grid .= "<tr data-label=\"" . $detail_id_label . "\" data-id=\"" . $detail_id . "\" id=\"row" . $i . "\">";
            $_grid .= "
            <td data-label=\"Action\" class=\"actions\"> 
                <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"" . $detail_id . "\" data-index=\"" . $i . "\">Edit</button>
                <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"" . $detail_id . "\" data-index=\"" . $i . "\">Delete</button>
            </td>";

            $_grid .= "<td data-label=\"inward_id\" style=\"display:none\">" . $_rs['inward_id'] . "</td>";
            $_grid .= "<td data-label=\"lot_no\"> " . $_rs['lot_no'] . " </td>";
            $_grid .= "<td data-label=\"item_name\" data-item=\"" . $_rs['item'] . "\">" . $_rs['item_name'] . "</td>";
$_grid .= "<td data-label=\"gst_type_value\" data-gst_type=\"" . $_rs['gst_type'] . "\">" . ($_rs['gst_type_value'] ?? $_rs['gst_type']) . "</td>";            $_grid .= "<td data-label=\"variety\"> " . $_rs['variety'] . " </td>";
            $_grid .= "<td data-label=\"packing_unit_name\" data-packing_unit=\"" . $_rs['packing_unit'] . "\">" . $_rs['packing_unit_name'] . "</td>";
            $_grid .= "<td data-label=\"inward_qty\"> " . $_rs['inward_qty'] . " </td>";
            $_grid .= "<td data-label=\"inward_wt\"> " . $_rs['inward_wt'] . " </td>";
            $_grid .= "<td data-label=\"avg_wt_per_bag\"> " . $_rs['avg_wt_per_bag'] . " </td>";
            $_grid .= "<td data-label=\"location\"> " . $_rs['location'] . " </td>";
            $_grid .= "<td data-label=\"moisture\"> " . $_rs['moisture'] . " </td>";
            $_grid .= "<td data-label=\"storage_duration\"> " . $_rs['storage_duration'] . " </td>";
            $_grid .= "<td data-label=\"rent_per_month\"> " . $_rs['rent_per_month'] . " </td>";
            $_grid .= "<td data-label=\"rent_per_storage_duration\"> " . $_rs['rent_per_storage_duration'] . " </td>";
            $_grid .= "<td data-label=\"seasonal_start_date\" style=\"display:none\">" . ($_rs['seasonal_start_date'] ?? '') . "</td>";
           $_grid .= "<td data-label=\"seasonal_end_date\" style=\"display:none\">" . ($_rs['seasonal_end_date'] ?? '') . "</td>"; 
            $_grid .= "<td data-label=\"rent_per\" style=\"display:none\">" . $_rs['rent_per'] . "</td>";
            $_grid .= "<td data-label=\"unloading_charge\" style=\"display:none\">" . $_rs['unloading_charge'] . "</td>";
            $_grid .= "<td data-label=\"remark\" style=\"display:none\">" . $_rs['remark'] . "</td>";
            $_grid .= "</tr>\n";
            $i++;
        }
        if ($i == 0) {
            $_grid .= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid .= "<td colspan=\"20\">No records available.</td>";
            for ($j = 0; $j < 14; $j++) $_grid .= "<td style=\"display:none\">&nbsp;</td>";
            $_grid .= "</tr>";
        }
    } else {
        $_grid .= "<tr id=\"norecords\" class=\"norecords\">";
        $_grid .= "<td colspan=\"20\">No records available.</td>";
        for ($j = 0; $j < 14; $j++) $_grid .= "<td style=\"display:none\">&nbsp;</td>";
        $_grid .= "</tr>";
    }
    $_grid .= "</tbody>
    </table> ";
    echo $_grid;
}
}
 class dal_inwarddetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
   
        $_dbh->exec("set @p0 = ".$_mdl->inward_detail_id);
        $_pre=$_dbh->prepare("CALL inward_detail_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->inward_id);
        $_pre->bindParam(2,$_mdl->lot_no);
        $_pre->bindParam(3,$_mdl->item);
        $_pre->bindParam(4,$_mdl->gst_type);
        $_pre->bindParam(5,$_mdl->variety);
        $_pre->bindParam(6,$_mdl->packing_unit);
        $_pre->bindParam(7,$_mdl->inward_qty);
        $_pre->bindParam(8,$_mdl->inward_wt);
        $_pre->bindParam(9,$_mdl->avg_wt_per_bag);
        $_pre->bindParam(10,$_mdl->location);
        $_pre->bindParam(11,$_mdl->moisture);
        $_pre->bindParam(12,$_mdl->storage_duration);
        $_pre->bindParam(13,$_mdl->rent_per_month);
        $_pre->bindParam(14,$_mdl->rent_per_storage_duration);
        $_pre->bindParam(15,$_mdl->seasonal_start_date);
        $_pre->bindParam(16,$_mdl->seasonal_end_date);
        $_pre->bindParam(17,$_mdl->rent_per);
        $_pre->bindParam(18,$_mdl->unloading_charge);
        $_pre->bindParam(19,$_mdl->remark);
        $_pre->bindParam(20,$_mdl->detailtransactionmode);
        $_pre->execute();
        
    }
}