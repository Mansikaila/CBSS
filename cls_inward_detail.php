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
                  
    public $rent_per_day;     
                  
    public $rent_per_storage_duration;     
                  
    public $sesonal_start_date;     
                  
    public $seasonal_end_date;     
                  
    public $seasonal_rent;     
                  
    public $seasonal_rent_per;     
                  
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
        $_grid="";
        $_grid="
        <table  id=\"searchDetail\" class=\"table table-bordered table-striped\" style=\"width:100%;\">
        <thead id=\"tableHead\">
            <tr>
            <th>Action</th>";
         $_grid.="<th> Lot No </th>";
                          $_grid.="<th> Item </th>";
                          $_grid.="<th> Gst Type </th>";
                          $_grid.="<th> Variety </th>";
                          $_grid.="<th> Packing Unit </th>";
                          $_grid.="<th> Inward Qty </th>";
                          $_grid.="<th> Inward Wt </th>";
                          $_grid.="<th> Avg Wt Per Bag </th>";
                          $_grid.="<th> Location </th>";
                          $_grid.="<th> Moisture </th>";
                          $_grid.="<th> Storage Duration </th>";
                          $_grid.="<th> Rent Per Day </th>";
                          $_grid.="<th> Rent Per Storage Duration </th>";
                         $_grid.="</tr>
        </thead>";
        $i=0;
        $result=array();
        $main_id_name="inward_id";
          if(isset($_POST[$main_id_name]))
            $main_id=$_POST[$main_id_name];
        else 
            $main_id=$this->_mdl->$main_id_name;
            
            if($main_id) {
                $sql="CAll csms1_search_detail('t.inward_detail_id, t.inward_id, t.lot_no, t.item, t3.item_name, t.gst_type, t.variety, t.packing_unit, t6.packing_unit_name, t.inward_qty, t.inward_wt, t.avg_wt_per_bag, t.location, t.moisture, t.storage_duration, t.rent_per_day, t.rent_per_storage_duration, t.sesonal_start_date, t.seasonal_end_date, t.seasonal_rent, t.seasonal_rent_per, t.unloading_charge, t.remark, t.inward_detail_id','tbl_inward_detail t INNER JOIN tbl_item_master t3 ON t.item=t3.item_id INNER JOIN tbl_packing_unit_master t6 ON t.packing_unit=t6.packing_unit_id','t.".$main_id_name."=".$main_id."')";
                $result=$_dbh->query($sql, PDO::FETCH_ASSOC);
            }
            
        $_grid.="<tbody id=\"tableBody\">";
        if(!empty($result))
        {
            foreach($result as $_rs)
            {
                $detail_id_label="inward_detail_id";
                $detail_id=$_rs[$detail_id_label];
                $_grid.="<tr data-label=\"".$detail_id_label."\" data-id=\"".$detail_id."\" id=\"row".$i."\">";
                $_grid.="
                <td data-label=\"Action\" class=\"actions\"> 
                    <button class=\"btn btn-info btn-sm me-2 edit-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Edit</button>
                    <button class=\"btn btn-danger btn-sm delete-btn\" data-id=\"".$detail_id."\" data-index=\"".$i."\">Delete</button>
                </td>";

            
                $_grid.="
                <td data-label=\"inward_id\" style=\"display:none\">".$_rs['inward_id']."</td>"; 
           
                $_grid.="
                <td data-label=\"lot_no\"> ".$_rs['lot_no']." </td>"; 
           
                $_grid.="
                <td data-label=\"item_name\"> ".$_rs['item_name']." </td>"; 
           
                $_grid.="
                <td data-label=\"gst_type\"> ".$_rs['gst_type']." </td>"; 
           
                $_grid.="
                <td data-label=\"variety\"> ".$_rs['variety']." </td>"; 
           
                $_grid.="
                <td data-label=\"packing_unit_name\"> ".$_rs['packing_unit_name']." </td>"; 
           
                $_grid.="
                <td data-label=\"inward_qty\"> ".$_rs['inward_qty']." </td>"; 
           
                $_grid.="
                <td data-label=\"inward_wt\"> ".$_rs['inward_wt']." </td>"; 
           
                $_grid.="
                <td data-label=\"avg_wt_per_bag\"> ".$_rs['avg_wt_per_bag']." </td>"; 
           
                $_grid.="
                <td data-label=\"location\"> ".$_rs['location']." </td>"; 
           
                $_grid.="
                <td data-label=\"moisture\"> ".$_rs['moisture']." </td>"; 
           
                $_grid.="
                <td data-label=\"storage_duration\"> ".$_rs['storage_duration']." </td>"; 
           
                $_grid.="
                <td data-label=\"rent_per_day\"> ".$_rs['rent_per_day']." </td>"; 
           
                $_grid.="
                <td data-label=\"rent_per_storage_duration\"> ".$_rs['rent_per_storage_duration']." </td>"; 
           
                $_grid.="
                <td data-label=\"sesonal_start_date\" style=\"display:none\">".$_rs['sesonal_start_date']."</td>"; 
           
                $_grid.="
                <td data-label=\"seasonal_end_date\" style=\"display:none\">".$_rs['seasonal_end_date']."</td>"; 
           
                $_grid.="
                <td data-label=\"seasonal_rent\" style=\"display:none\">".$_rs['seasonal_rent']."</td>"; 
           
                $_grid.="
                <td data-label=\"seasonal_rent_per\" style=\"display:none\">".$_rs['seasonal_rent_per']."</td>"; 
           
                $_grid.="
                <td data-label=\"unloading_charge\" style=\"display:none\">".$_rs['unloading_charge']."</td>"; 
           
                $_grid.="
                <td data-label=\"remark\" style=\"display:none\">".$_rs['remark']."</td>"; 
           $_grid.= "</tr>\n";
        $i++;
        }
        if($i==0) {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"20\">No records available.</td>";$_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="</tr>";
        }
    } else {
            $_grid.= "<tr id=\"norecords\" class=\"norecords\">";
            $_grid.="<td colspan=\"20\">No records available.</td>";
            $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
                     $_grid.="<td style=\"display:none\">&nbsp;</td>";
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
        echo $_grid; 
    }   
}
 class dal_inwarddetail                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        
        $_dbh->exec("set @p0 = ".$_mdl->inward_detail_id);
        $_pre=$_dbh->prepare("CALL inward_detail_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
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
        $_pre->bindParam(13,$_mdl->rent_per_day);
        $_pre->bindParam(14,$_mdl->rent_per_storage_duration);
        $_pre->bindParam(15,$_mdl->sesonal_start_date);
        $_pre->bindParam(16,$_mdl->seasonal_end_date);
        $_pre->bindParam(17,$_mdl->seasonal_rent);
        $_pre->bindParam(18,$_mdl->seasonal_rent_per);
        $_pre->bindParam(19,$_mdl->unloading_charge);
        $_pre->bindParam(20,$_mdl->remark);
        $_pre->bindParam(21,$_mdl->detailtransactionmode);
        $_pre->execute();
        
    }
}