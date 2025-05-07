<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_itempreservationpricelistmaster 
{                        
public $_item_preservation_price_list_id;          
    public $_item_id;          
    public $_packing_unit_id;          
    public $_rent_kg_per_month;          
    public $_season_rent_per_kg;          
    public $_created_date;          
    public $_created_by;          
    public $_modified_date;          
    public $_modified_by;          
    public $_company_id;          
    public $_transactionmode;
    
}

class bll_itempreservationpricelistmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_itempreservationpricelistmaster(); 
        $this->_dal =new dal_itempreservationpricelistmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_item_preservation_price_list_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_item_preservation_price_list_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_item_preservation_price_list_master.php");
       }

    }
 
   public function fillModel()
        {
            $this->_dal->fillModel($this->_mdl);
        }

        public function fetchPackingUnits($item_id)
        {
            return $this->_dal->fetchPackingUnits($item_id);
        }

        public function savePackingUnits($data)
        {
            return $this->_dal->savePackingUnits($data);
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
            //echo "Error: " . $e->getMessage();
            return 0;
        }
        return 0;
    }
}
 class dal_itempreservationpricelistmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_item_preservation_price_list_id);
        $_pre=$_dbh->prepare("CALL item_preservation_price_list_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_item_id);
        $_pre->bindParam(2,$_mdl->_packing_unit_id);
        $_pre->bindParam(3,$_mdl->_rent_kg_per_month);
        $_pre->bindParam(4,$_mdl->_season_rent_per_kg);
        $_pre->bindParam(5,$_mdl->_created_date);
        $_pre->bindParam(6,$_mdl->_created_by);
        $_pre->bindParam(7,$_mdl->_modified_date);
        $_pre->bindParam(8,$_mdl->_modified_by);
        $_pre->bindParam(9,$_mdl->_company_id);
        $_pre->bindParam(10,$_mdl->_transactionmode);
        $_pre->execute();
        
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL item_preservation_price_list_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["item_preservation_price_list_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_item_preservation_price_list_id=$_rs[0]["item_preservation_price_list_id"];
        $_mdl->_item_id=$_rs[0]["item_id"];
        $_mdl->_packing_unit_id=$_rs[0]["packing_unit_id"];
        $_mdl->_rent_kg_per_month=$_rs[0]["rent_kg_per_month"];
        $_mdl->_season_rent_per_kg=$_rs[0]["season_rent_per_kg"];
        $_mdl->_created_date=$_rs[0]["created_date"];
        $_mdl->_created_by=$_rs[0]["created_by"];
        $_mdl->_modified_date=$_rs[0]["modified_date"];
        $_mdl->_modified_by=$_rs[0]["modified_by"];
        $_mdl->_company_id=$_rs[0]["company_id"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
      public function fetchPackingUnits($item_id)
    {
        global $_dbh;

        try {
        
            $columns = "pum.packing_unit_id, pum.packing_unit_name, 
                        COALESCE(ippl.rent_kg_per_month, '0.00') AS rent_kg_per_month, 
                        COALESCE(ippl.season_rent_per_kg, '0.00') AS season_rent_per_kg";
            $tableName = "tbl_packing_unit_master pum 
                          LEFT JOIN tbl_item_preservation_price_list_master ippl 
                          ON pum.packing_unit_id = ippl.packing_unit_id 
                          AND ippl.item_id = " . intval($item_id);
            $whereCondition = "pum.packing_unit_id IS NOT NULL AND pum.status = 1";

            // Prepare the stored procedure call
            $stmt = $_dbh->prepare("CALL csms1_search_detail(:columns, :tableName, :whereCondition)");
            $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
            $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);
            $stmt->bindParam(':whereCondition', $whereCondition, PDO::PARAM_STR);
            $stmt->execute();

            // Fetch and return the results
            $packingUnits = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $packingUnits ?: [];

        } catch (PDOException $e) {
            error_log("Database Error (fetchPackingUnits): " . $e->getMessage());
            return ['error' => 'Error fetching data.'];
        }
    }

public function savePackingUnits($dataList)
{
    global $_dbh;
    $totalUpdated = 0;

    try {
        foreach ($dataList as $data) {
            
            $packing_unit_id = $data['packing_unit_id'];
            $item_id = $data['item_id'];
            $rent_kg_per_month = $data['rent_kg_per_month'];
            $season_rent_per_kg = $data['season_rent_per_kg'];
            $company_id = isset($data['company_id']) ? $data['company_id'] : 1; // set default if not passed

            // Check if record exists
            $checkQuery = "
                SELECT COUNT(*) 
                FROM tbl_item_preservation_price_list_master 
                WHERE packing_unit_id = :packing_unit_id AND item_id = :item_id
            ";
            $checkStmt = $_dbh->prepare($checkQuery);
            $checkStmt->bindParam(':packing_unit_id', $packing_unit_id, PDO::PARAM_INT);
            $checkStmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $checkStmt->execute();
            $recordExists = $checkStmt->fetchColumn();
            
            if ($recordExists) {
                $updateQuery = "
                    UPDATE tbl_item_preservation_price_list_master
                    SET rent_kg_per_month = :rent_kg_per_month,
                        season_rent_per_kg = :season_rent_per_kg,
                         company_id=:company_id
                    WHERE packing_unit_id = :packing_unit_id AND item_id = :item_id
                ";
                $stmt = $_dbh->prepare($updateQuery);
            } else {
                $insertQuery = "
                    INSERT INTO tbl_item_preservation_price_list_master 
                    (packing_unit_id, item_id, rent_kg_per_month, season_rent_per_kg, company_id)
                    VALUES (:packing_unit_id, :item_id, :rent_kg_per_month, :season_rent_per_kg, :company_id)
                ";
                $stmt = $_dbh->prepare($insertQuery);
            }

            $stmt->bindParam(':packing_unit_id', $packing_unit_id, PDO::PARAM_INT);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->bindParam(':rent_kg_per_month', $rent_kg_per_month, PDO::PARAM_STR);
            $stmt->bindParam(':season_rent_per_kg', $season_rent_per_kg, PDO::PARAM_STR);
            $stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
//            if (!$recordExists) {
//                
//            }
            $stmt->execute();

            $totalUpdated++;
        }

        return [
            'success' => true,
            'message' => 'All records processed.',
            'updated_count' => $totalUpdated
        ];

    } catch (PDOException $e) {
        error_log("Database Error (savePackingUnits): " . $e->getMessage());
        return ['success' => false, 'error' => 'Error saving data.', 'updated_count' => $totalUpdated];
    }
}


}
  $_bll=new bll_itempreservationpricelistmaster();
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];

    if ($action === 'fetch_units' && isset($_REQUEST['item_id'])) {
        echo json_encode($_bll->fetchPackingUnits($_REQUEST['item_id']));
        exit;
    }

    if ($action === 'save_unit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $results = [];
        $success = true;

        foreach ($data as $unit) {
            $result = $_bll->savePackingUnits($unit);
            $results[] = $result;
            if (!$result['success']) {
                $success = false;
            }
        }

        echo json_encode([
            'success' => $success,
            'details' => $results 
        ]);
        exit;
    }
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


                if(isset($_REQUEST["item_preservation_price_list_id"]) && !empty($_REQUEST["item_preservation_price_list_id"]))
                    $field=trim($_REQUEST["item_preservation_price_list_id"]);
                else {
                        $field=0;
               }
        $_bll->_mdl->_item_preservation_price_list_id=$field;


                if(isset($_REQUEST["item_id"]) && !empty($_REQUEST["item_id"]))
                    $field=trim($_REQUEST["item_id"]);
                else {
                        $field=null;
               }
        $_bll->_mdl->_item_id=$field;


                if(isset($_REQUEST["packing_unit_id"]) && !empty($_REQUEST["packing_unit_id"]))
                    $field=trim($_REQUEST["packing_unit_id"]);
                else {
                        $field=null;
               }
        $_bll->_mdl->_packing_unit_id=$field;


                if(isset($_REQUEST["rent_kg_per_month"]) && !empty($_REQUEST["rent_kg_per_month"]))
                    $field=trim($_REQUEST["rent_kg_per_month"]);
                else {
                        $field=null;
               }
        $_bll->_mdl->_rent_kg_per_month=$field;


                if(isset($_REQUEST["season_rent_per_kg"]) && !empty($_REQUEST["season_rent_per_kg"]))
                    $field=trim($_REQUEST["season_rent_per_kg"]);
                else {
                        $field=null;
               }
        $_bll->_mdl->_season_rent_per_kg=$field;


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
            $_bll->dbTransaction();
    }

    if(isset($_REQUEST["transactionmode"]) && $_REQUEST["transactionmode"]=="D")       
    {   
         $_bll->fillModel();
         $_bll->dbTransaction();
    }

