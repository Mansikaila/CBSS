<?php  
include_once(__DIR__ . "/../config/connection.php");
include("cls_customer_wise_item_preservation_price_list_detail.php"); 
        class mdl_customerwiseitempreservationpricelistmaster 
{                        
public $_customer_wise_item_preservation_price_list_id;          
    public $_customer_id;          
    public $_item_id;          
    public $_rent_per_kg_month;          
    public $_rent_per_kg_season;          
    public $_created_date;          
    public $_created_by;          
    public $_modified_date;          
    public $_modified_by;          
    public $_company_id;          
    public $_transactionmode;
    
                    /** FOR DETAIL **/
                    public $_array_itemdetail;
                     public $_array_itemdelete;
                    /** \FOR DETAIL **/
                    
}

class bll_customerwiseitempreservationpricelistmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_customerwiseitempreservationpricelistmaster(); 
        $this->_dal =new dal_customerwiseitempreservationpricelistmaster();
    }

public function dbTransaction()
{
    global $_dbh;

    try {
        $_dbh->beginTransaction();

        // Process master record first
        $this->_dal->dbTransaction($this->_mdl);

        // If insert, get the inserted master ID
        if ($this->_mdl->_transactionmode == "I") {
            $result = $_dbh->query("SELECT @p0 AS inserted_id");
            $insertedId = $result->fetchColumn();
            $this->_mdl->_customer_wise_item_preservation_price_list_id = $insertedId;
        }

        // Now process detail records
        if (!empty($this->_mdl->_array_itemdetail)) {
            $detailBLL = new bll_customerwiseitempreservationpricelistdetail();

            foreach ($this->_mdl->_array_itemdetail as $detailRecord) {
                $detailModel = new mdl_customerwiseitempreservationpricelistdetail();

                foreach ($detailRecord as $key => $value) {
                    if (property_exists($detailModel, $key)) {
                        $detailModel->$key = $value;
                    }
                }

                $detailModel->_item_id = $this->_mdl->_item_id;
                $detailModel->_customer_id = $this->_mdl->_customer_id;
                $detailModel->customer_wise_item_preservation_price_list_id = $this->_mdl->_customer_wise_item_preservation_price_list_id;
                
                // Preserve the transaction mode from the record
                $detailModel->detailtransactionmode = $detailRecord['detailtransactionmode'];

                $detailBLL->_mdl = $detailModel;
                $detailBLL->dbTransaction();
            }
        }

        $_dbh->commit();
        header("Location:../srh_customer_wise_item_preservation_price_list_master.php");
        exit;

    } catch (Exception $e) {
        if ($_dbh->inTransaction()) {
            $_dbh->rollBack();
        }
        error_log("Transaction failed: " . $e->getMessage());
        throw $e;
    }
}

 
    public function fillModel()
    {
        global $_dbh;
        $this->_dal->fillModel($this->_mdl);
    
    
    }
     public function pageSearch()
    {
         
        global $_dbh;
         
         

        $sql="CAll csms1_search('t.customer_id, t.item_id, t.rent_per_kg_month, t.rent_per_kg_season, t.customer_wise_item_preservation_price_list_id','tbl_customer_wise_item_preservation_price_list_master t')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Customer  <br><input type=\"text\" data-index=\"1\" placeholder=\"Search Customer \" /></th> 
                         <th> Item  <br><input type=\"text\" data-index=\"2\" placeholder=\"Search Item \" /></th> 
                         <th> Rent / Kg./Month <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Rent / Kg./Month\" /></th> 
                         <th> Rent / Kg. <br><input type=\"text\" data-index=\"4\" placeholder=\"Search Rent / Kg.\" /></th> 
                         </tr>
        </thead>
        <tbody>";
         $_grid="";
         $j=0;
        foreach($_dbh-> query($sql) as $_rs)
        {
            $j++;
        
        $_grid.="<tr>
        <td> 
            <form  method=\"post\" action=\"frm_customer_wise_item_preservation_price_list_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"customer_wise_item_preservation_price_list_id\" value=\"".$_rs["customer_wise_item_preservation_price_list_id"]."\" />
       
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_customer_wise_item_preservation_price_list_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"customer_wise_item_preservation_price_list_id\" value=\"".$_rs["customer_wise_item_preservation_price_list_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["customer_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["item_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["rent_per_kg_month"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["rent_per_kg_season"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"9\">No records available.</td>";
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
    function checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name) {
        global $_dbh;
        try {
            $sql="CAll csms1_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_default = $result->fetchColumn();
            return $is_default;
        }
        catch (PDOException $e) {
           
            return 0;
        }
        return 0;
    }
}
 class dal_customerwiseitempreservationpricelistmaster                         
{
public function dbTransaction($_mdl) {
    global $_dbh;

    // Set current date/time if not provided
    $currentDateTime = date('Y-m-d H:i:s');
    if (empty($_mdl->_created_date)) {
        $_mdl->_created_date = $currentDateTime;
    }
    $_mdl->_modified_date = $currentDateTime;

    // Set default created_by/modified_by if not provided
    if (empty($_mdl->_created_by)) {
        $_mdl->_created_by = $_SESSION['user_id'] ?? 1;
    }
    if (empty($_mdl->_modified_by)) {
        $_mdl->_modified_by = $_SESSION['user_id'] ?? 1;
    }

    // For new records, check if combination exists
    if ($_mdl->_transactionmode == "I") {
        $stmt = $_dbh->prepare("
            SELECT customer_wise_item_preservation_price_list_id 
            FROM tbl_customer_wise_item_preservation_price_list_master 
            WHERE customer_id = ? AND item_id = ?
        ");
        $stmt->execute([$_mdl->_customer_id, $_mdl->_item_id]);
        $existingId = $stmt->fetchColumn();

        if ($existingId) {
            // Convert to update mode if record exists
            $_mdl->_customer_wise_item_preservation_price_list_id = $existingId;
            $_mdl->_transactionmode = "U";
        }
    }

    // Delete old detail records if item changed
    if ($_mdl->_transactionmode == "U") {
        $stmt = $_dbh->prepare("
            SELECT item_id 
            FROM tbl_customer_wise_item_preservation_price_list_master 
            WHERE customer_wise_item_preservation_price_list_id = ?
        ");
        $stmt->execute([$_mdl->_customer_wise_item_preservation_price_list_id]);
        $currentItemId = $stmt->fetchColumn();

        if ($currentItemId != $_mdl->_item_id) {
            $deleteStmt = $_dbh->prepare("
                DELETE FROM tbl_customer_wise_item_preservation_price_list_detail 
                WHERE customer_wise_item_preservation_price_list_id = ?
            ");
            $deleteStmt->execute([$_mdl->_customer_wise_item_preservation_price_list_id]);
        }
    }

    // Call the master stored procedure
    $_dbh->exec("SET @p0 = " . $_mdl->_customer_wise_item_preservation_price_list_id);
    $_pre = $_dbh->prepare("CALL customer_wise_item_preservation_price_list_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?)");
    $_pre->bindValue(1, intval($_mdl->_customer_id), PDO::PARAM_INT);
    $_pre->bindValue(2, intval($_mdl->_item_id), PDO::PARAM_INT);
    $_pre->bindValue(3, $_mdl->_rent_per_kg_month ?? 0);
    $_pre->bindValue(4, $_mdl->_rent_per_kg_season ?? 0);
    $_pre->bindValue(5, $_mdl->_created_date);
    $_pre->bindValue(6, $_mdl->_created_by);
    $_pre->bindValue(7, $_mdl->_modified_date);
    $_pre->bindValue(8, $_mdl->_modified_by);
    $_pre->bindValue(9, $_mdl->_company_id ?? 1);
    $_pre->bindValue(10, $_mdl->_transactionmode);
    $_pre->execute();

    if ($_mdl->_transactionmode == "I") {
        $result = $_dbh->query("SELECT @p0 AS inserted_id");
        $insertedId = $result->fetchColumn();
        $_mdl->_customer_wise_item_preservation_price_list_id = $insertedId;
    }
}
public function fillModel($_mdl) {
    global $_dbh;
    $_pre=$_dbh->prepare("CALL customer_wise_item_preservation_price_list_master_fillmodel (?) ");
    $_pre->bindParam(1,$_REQUEST["customer_wise_item_preservation_price_list_id"]);
    $_pre->execute();
    $_rs=$_pre->fetchAll(); 
    if(!empty($_rs)) {
        $_mdl->_customer_wise_item_preservation_price_list_id=$_rs[0]["customer_wise_item_preservation_price_list_id"];
        $_mdl->_customer_id=$_rs[0]["customer_id"];
        $_mdl->_item_id=$_rs[0]["item_id"];
        $_mdl->_rent_per_kg_month=$_rs[0]["rent_per_kg_month"];
        $_mdl->_rent_per_kg_season=$_rs[0]["rent_per_kg_season"];
        $_mdl->_created_date=$_rs[0]["created_date"];
        $_mdl->_created_by=$_rs[0]["created_by"];
        $_mdl->_modified_date=$_rs[0]["modified_date"];
        $_mdl->_modified_by=$_rs[0]["modified_by"];
        $_mdl->_company_id=$_rs[0]["company_id"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        
        // Fetch detail records
        $detailBLL = new bll_customerwiseitempreservationpricelistdetail();
        $_mdl->_array_itemdetail = $detailBLL->getDetailsByMasterId($_mdl->_customer_wise_item_preservation_price_list_id);
    }
}
}
$_bll=new bll_customerwiseitempreservationpricelistmaster();


    /*** FOR DETAIL ***/
    $_blldetail=new bll_customerwiseitempreservationpricelistdetail();
    /*** /FOR DETAIL ***/
if (isset($_REQUEST["action"])) {
    $action = $_REQUEST["action"];

if ($action === 'check_master_record' && isset($_POST['customer_id']) && isset($_POST['item_id'])) {
    $customerId = intval($_POST['customer_id']);
    $itemId = intval($_POST['item_id']);
    $masterId = isset($_POST['master_id']) ? intval($_POST['master_id']) : 0;
    
    $sql = "SELECT * FROM tbl_customer_wise_item_preservation_price_list_master 
            WHERE customer_id = ? AND item_id = ?";
    $stmt = $_dbh->prepare($sql);
    $stmt->execute([$customerId, $itemId]);

    $masterData = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    if ($masterData) {
        // Return the existing record data
        echo json_encode([
            'exists' => true,
            'data' => $masterData,
            'is_current' => ($masterId > 0 && $masterData['customer_wise_item_preservation_price_list_id'] == $masterId)
        ]);
    } else {
        echo json_encode(['exists' => false]);
    }
    exit;
}

    // Default dynamic action execution
    $_bll->$action();
}

if(isset($_POST["type"]) && $_POST["type"]=="ajax") {
    $column_name="";$column_value="";$id_name="";$id_value="";$table_name="";
    if(isset($_POST["column_name"]))
        $column_name=$_POST["column_name"];
    if(isset($_POST["column_value"]))
        $column_value=$_POST["column_value"];
    if(isset($_POST["id_name"]))
        $id_name=$_POST["id_name"];
    if(isset($_POST["id_value"]))
        $id_value=$_POST["id_value"];
    if(isset($_POST["table_name"]))
        $table_name=$_POST["table_name"];
    echo $_bll->checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name);
    exit;
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
//
// echo"<pre>";
//    print_r($_POST);
//    echo"</pre>";
            
            if(isset($_REQUEST["customer_wise_item_preservation_price_list_id"]) && !empty($_REQUEST["customer_wise_item_preservation_price_list_id"]))
                $field=trim($_REQUEST["customer_wise_item_preservation_price_list_id"]);
            else {
                    $field=0;
           }
    $_bll->_mdl->_customer_wise_item_preservation_price_list_id=$field;

            
            if(isset($_REQUEST["customer_id"]) && !empty($_REQUEST["customer_id"]))
                 $field = intval(trim($_REQUEST["customer_id"]));
            else {
                    $field=null;
           }
    $_bll->_mdl->_customer_id=$field;

            
            if(isset($_REQUEST["item_id"]) && !empty($_REQUEST["item_id"]))
                $field = intval(trim($_REQUEST["item_id"]));
            else {
                    $field=null;
           }
    $_bll->_mdl->_item_id=$field;

            
            if(isset($_REQUEST["rent_per_kg_month"]) && !empty($_REQUEST["rent_per_kg_month"]))
                $field=trim($_REQUEST["rent_per_kg_month"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_rent_per_kg_month=$field;

            
            if(isset($_REQUEST["rent_per_kg_season"]) && !empty($_REQUEST["rent_per_kg_season"]))
                $field=trim($_REQUEST["rent_per_kg_season"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_rent_per_kg_season=$field;

            
            if(isset($_REQUEST["created_date"]) && !empty($_REQUEST["created_date"]))
                $field=trim($_REQUEST["created_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_date=$field;

            
            if(isset($_REQUEST["created_by"]) && !empty($_REQUEST["created_by"]))
                $field=trim($_REQUEST["created_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_by=$field;

            
            if(isset($_REQUEST["modified_date"]) && !empty($_REQUEST["modified_date"]))
                $field=trim($_REQUEST["modified_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_date=$field;

            
            if(isset($_REQUEST["modified_by"]) && !empty($_REQUEST["modified_by"]))
                $field=trim($_REQUEST["modified_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_by=$field;

            
            if(isset($_REQUEST["company_id"]) && !empty($_REQUEST["company_id"]))
                $field=trim($_REQUEST["company_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_company_id=$field;

         if(isset($_REQUEST["transactionmode"]))
                $tmode=$_REQUEST["transactionmode"];
            else
                $tmode="I";
            $_bll->_mdl->_transactionmode =$tmode;

    // Process detail records
$_bll->_mdl->_array_itemdetail = array();
if (isset($_POST['detail_records']) && !empty($_POST['detail_records'])) {
    $detailRecords = json_decode($_POST['detail_records'], true);
    
    // Filter out records with both values as 0 (except they have an ID)
    $_bll->_mdl->_array_itemdetail = array_filter($detailRecords, function($record) {
        return ($record['rent_per_qty_month'] != 0 || 
                $record['rent_per_qty_season'] != 0 || 
                $record['customer_wise_item_preservation_price_list_detail_id'] > 0);
    });
}

//    if (isset($_POST["deleted_records"])) {
//        $deleted_records = json_decode($_POST["deleted_records"], true);
//        if (!empty($deleted_records)) {
//            $_bll->_mdl->_array_itemdelete = $deleted_records;
//        }
//    }


    // Perform database transaction
    $_bll->dbTransaction();
}

if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
{   
     $_bll->fillModel();
     $_bll->dbTransaction();
}
