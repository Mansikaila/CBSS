<?php
    class mdl_rentinvoicedetail 
{                        
public $rent_invoice_detail_id;     
                  
    public $rent_invoice_id;     
                  
    public $description;     
                  
    public $qty;     
                  
    public $unit;     
                  
    public $weight;     
                  
    public $rate_per_unit;     
                  
    public $amount;     
                  
    public $remark;     
                  
    public $detailtransactionmode;
}

class bll_rentinvoicedetail                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_rentinvoicedetail(); 
        $this->_dal =new dal_rentinvoicedetail();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
    }
     public function pageSearch()
    {
        global $_dbh;
        $_grid="";
        $_grid="
        <table  id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
        <thead id=\"tableHead\">
            <tr>
            <th>Action</th>";
         $_grid.="<th> Description </th>";
                          $_grid.="<th> Qty </th>";
                          $_grid.="<th> Unit </th>";
                          $_grid.="<th> Rate / Unit </th>";
                          $_grid.="<th> Amount </th>";
                          $_grid.="<th> Remark </th>";
                         $_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="rent_invoice_id";
          if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
            if($main_id) {
                $sql="CAll csms1_search_detail('t.description, t.qty, t.unit, t.rate_per_unit, t.amount, t.remark, t.rent_invoice_detail_id','tbl_rent_invoice_detail t','t.".$main_id_name."=".$main_id."')";
                $result=$_dbh->query($sql, PDO::FETCH_ASSOC);
            }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="rent_invoice_detail_id";
                $detail_id=$_rs[$detail_id_label];
                $_grid.="<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
                $_grid.="
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
                </td>";

            
                $_grid.="
                <td data-label=\"rent_invoice_id\" style=\"display:none\">".$_rs['rent_invoice_id']."</td>"; 
           
                $_grid.="
                <td data-label=\"description\"> ".$_rs['description']." </td>"; 
           
                $_grid.="
                <td data-label=\"qty\"> ".$_rs['qty']." </td>"; 
           
                $_grid.="
                <td data-label=\"unit\"> ".$_rs['unit']." </td>"; 
           
                $_grid.="
                <td data-label=\"weight\" style=\"display:none\">".$_rs['weight']."</td>"; 
           
                $_grid.="
                <td data-label=\"rate_per_unit\"> ".$_rs['rate_per_unit']." </td>"; 
           
                $_grid.="
                <td data-label=\"amount\"> ".$_rs['amount']." </td>"; 
           
                $_grid.="
                <td data-label=\"remark\"> ".$_rs['remark']." </td>"; 
           $_grid.= "</tr>\n";
        $i++;
        }
        if($i==0) {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"8\">No records available.</td>";$_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="</tr>";
        }
    } else {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"8\">No records available.</td>";
            $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="</tr>";
    }
        $_grid.="</tbody>
        </table> ";
        return $_grid; 
    }   
}

//drashti-data grid 
if (isset($_POST['action']) && $_POST['action'] == 'generate_details') {
    $lot_no = $_POST['lot_no'];
    $customer = $_POST['customer'];

$sql = "SELECT 
    NULL AS in_no,
    NULL AS in_date,
    i.lot_no,
    i.item,
    i.variety,
    i.inward_qty AS qty,
    um.packing_unit_name AS unit,
    i.inward_wt AS weight,
    i.storage_duration,
    i.rent_per,
    om.outward_date AS out_date,
    im.inward_date AS charges_from,
    om.outward_date AS charges_to,
    TIMESTAMPDIFF(MONTH, im.inward_date, om.outward_date) AS act_month,
    (DATEDIFF(om.outward_date, im.inward_date) % 30) AS act_day,
    NULL AS invoice_month,
    NULL AS invoice_day,
    NULL AS inv_for,
    NULL AS amount,
    NULL AS invoice_for
FROM tbl_outward_detail o
JOIN tbl_inward_detail i
    ON o.inward_detail_id = i.inward_detail_id
JOIN tbl_outward_master om
    ON o.outward_id = om.outward_id
JOIN tbl_inward_master im
    ON i.inward_id = im.inward_id
LEFT JOIN tbl_packing_unit_master um
    ON i.packing_unit = um.packing_unit_id
WHERE i.lot_no = ?
  AND om.customer = ?";

    $stmt = $_dbh->prepare($sql);
    $stmt->execute([$lot_no, $customer_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
    exit;
}
//end
 class dal_rentinvoicedetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        
        $_dbh->exec("set @p0 = ".$_mdl->rent_invoice_detail_id);
        $_pre=$_dbh->prepare("CALL rent_invoice_detail_transaction (@p0,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->rent_invoice_id);
        $_pre->bindParam(2,$_mdl->description);
        $_pre->bindParam(3,$_mdl->qty);
        $_pre->bindParam(4,$_mdl->unit);
        $_pre->bindParam(5,$_mdl->weight);
        $_pre->bindParam(6,$_mdl->rate_per_unit);
        $_pre->bindParam(7,$_mdl->amount);
        $_pre->bindParam(8,$_mdl->remark);
        $_pre->bindParam(9,$_mdl->detailtransactionmode);
        $_pre->execute();
        
    }
}