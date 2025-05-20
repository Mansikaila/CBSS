<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_outwardmaster 
{             
        public $generator_fields_names;
        public $generator_fields_types;
        public $generator_field_scale;
        public $generator_dropdown_table;
        public $generator_label_column;
        public $generator_value_column;
        public $generator_where_condition;
        public $generator_fields_labels;
        public $generator_field_display;
        public $generator_field_required;
        public $generator_allow_zero;
        public $generator_allow_minus;
        public $generator_chk_duplicate;
        public $generator_field_data_type;
        public $generator_field_is_disabled;
        public $generator_after_detail;
        protected $fields = [];

        public function __get($name) {
            return $this->fields[$name] ?? null;
        }

        public function __set($name, $value) {
            $this->fields[$name] = $value;
        }
        public function __construct() {
            global $_dbh;
            global $database_name;
            global $tbl_generator_master;
            global $tbl_outward_master;
            $select = $_dbh->prepare("SELECT `generator_options` FROM `{$tbl_generator_master}` WHERE `table_name` = ?");
            $select->bindParam(1, $tbl_state_master);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $generator_options = json_decode($row["generator_options"]);
                if ($generator_options) {
                    $this->generator_fields_names=$generator_options->field_name;
                    $this->generator_fields_types=$generator_options->field_type;
                    $this->generator_field_scale=$generator_options->field_scale;
                    $this->generator_dropdown_table=$generator_options->dropdown_table;
                    $this->generator_label_column=$generator_options->label_column;
                    $this->generator_value_column=$generator_options->value_column;
                    $this->generator_where_condition=$generator_options->where_condition;
                    $this->generator_fields_labels=$generator_options->field_label;
                    $this->generator_field_display=$generator_options->field_display;
                    $this->generator_field_required=$generator_options->field_required;
                    $this->generator_allow_zero=$generator_options->allow_zero;
                    $this->generator_allow_minus=$generator_options->allow_minus;
                    $this->generator_chk_duplicate=$generator_options->chk_duplicate;
                    $this->generator_field_data_type=$generator_options->field_data_type;
                    $this->generator_field_is_disabled=$generator_options->is_disabled;
                    $this->generator_after_detail=$generator_options->after_detail;
                }
            }
        }
public $_outward_id;          
    public $_outward_sequence;          
    public $_outward_no;          
    public $_outward_date;          
    public $_customer;          
    public $_total_qty;          
    public $_total_wt;          
    public $_gross_wt;          
    public $_tare_wt;          
    public $_net_wt;          
    public $_loading_expense;          
    public $_other_expense1;          
    public $_other_expense2;          
    public $_outward_order_by;          
    public $_delivery_to;          
    public $_vehicle_no;          
    public $_driver_name;          
    public $_driver_mob_no;          
    public $_transporter;          
    public $_sp_note;          
    public $_created_by;          
    public $_created_date;          
    public $_modified_by;          
    public $_modified_date;          
    public $_company_id;          
    public $_company_year_id;          
    public $_transactionmode;
    
}

class bll_outwardmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_outwardmaster(); 
        $this->_dal =new dal_outwardmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_outward_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_outward_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_outward_master.php");
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
        global $database_name;

        $sql="CAll ".$database_name."_search('*','tbl_outward_master t')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
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
            <form  method=\"post\" action=\"frm_outward_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"outward_id\" value=\"".$_rs["outward_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_outward_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"outward_id\" value=\"".$_rs["outward_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"25\">No records available.</td>";
                $_grid.="</tr>";
            }
        $_grid.="</tbody>
        </table> ";
        echo $_grid; 
    }
    function checkDuplicate($column_name,$column_value,$id_name,$id_value,$table_name) {
        global $_dbh;
        global $database_name;
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
        try {
            $sql="CAll ".$database_name."_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
            $stmt=$_dbh->prepare($sql);
            $stmt->execute();
            $result = $_dbh->query("SELECT @is_duplicate");
            $is_duplicate = $result->fetchColumn();
            echo $is_duplicate;
            exit;
        }
        catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            echo 0;
            exit;
        }
        echo 0;
        exit;
    }
}
 class dal_outwardmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_outward_id);
        $_pre=$_dbh->prepare("CALL outward_master_transaction (@p0,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ");
        
            if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                foreach($_mdl->generator_fields_names as $i=>$fieldname)
                {
                    if($i==0)
                        continue;
                    $field=$_mdl->{"_".$fieldname};
                    $_pre->bindValue($i,$field);
                }
            }
            $_pre->bindValue($i+1,$_mdl->_transactionmode);
            $_pre->execute();
        
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL outward_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["outward_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {
            if(is_array($_mdl->generator_fields_names) && !empty($_mdl->generator_fields_names)){
                foreach($_mdl->generator_fields_names as $i=>$fieldname)
                {
                    if(isset($_rs[0][$fieldname]) && !empty($_rs[0][$fieldname]))
                        $_mdl->{"_".$fieldname}=$_rs[0][$fieldname];
                }
                $_mdl->_transactionmode =$_REQUEST["transactionmode"];
            }
        }
    }
}
$_bll=new bll_outwardmaster();

if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
    $_bll->$action();
}
if(isset($_POST["masterHidden"]) && ($_POST["masterHidden"]=="save"))
{
 
            
            if(isset($_REQUEST["outward_id"]) && !empty($_REQUEST["outward_id"]))
                $field=trim($_REQUEST["outward_id"]);
            else {
                    $field=0;
           }
    $_bll->_mdl->_outward_id=$field;

            
            if(isset($_REQUEST["outward_sequence"]) && !empty($_REQUEST["outward_sequence"]))
                $field=trim($_REQUEST["outward_sequence"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_outward_sequence=$field;

            
            if(isset($_REQUEST["outward_no"]) && !empty($_REQUEST["outward_no"]))
                $field=trim($_REQUEST["outward_no"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_outward_no=$field;

            
            if(isset($_REQUEST["outward_date"]) && !empty($_REQUEST["outward_date"]))
                $field=trim($_REQUEST["outward_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_outward_date=$field;

            
            if(isset($_REQUEST["customer"]) && !empty($_REQUEST["customer"]))
                $field=trim($_REQUEST["customer"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_customer=$field;

            
            if(isset($_REQUEST["total_qty"]) && !empty($_REQUEST["total_qty"]))
                $field=trim($_REQUEST["total_qty"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_total_qty=$field;

            
            if(isset($_REQUEST["total_wt"]) && !empty($_REQUEST["total_wt"]))
                $field=trim($_REQUEST["total_wt"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_total_wt=$field;

            
            if(isset($_REQUEST["gross_wt"]) && !empty($_REQUEST["gross_wt"]))
                $field=trim($_REQUEST["gross_wt"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_gross_wt=$field;

            
            if(isset($_REQUEST["tare_wt"]) && !empty($_REQUEST["tare_wt"]))
                $field=trim($_REQUEST["tare_wt"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_tare_wt=$field;

            
            if(isset($_REQUEST["net_wt"]) && !empty($_REQUEST["net_wt"]))
                $field=trim($_REQUEST["net_wt"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_net_wt=$field;

            
            if(isset($_REQUEST["loading_expense"]) && !empty($_REQUEST["loading_expense"]))
                $field=trim($_REQUEST["loading_expense"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_loading_expense=$field;

            
            if(isset($_REQUEST["other_expense1"]) && !empty($_REQUEST["other_expense1"]))
                $field=trim($_REQUEST["other_expense1"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_other_expense1=$field;

            
            if(isset($_REQUEST["other_expense2"]) && !empty($_REQUEST["other_expense2"]))
                $field=trim($_REQUEST["other_expense2"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_other_expense2=$field;

            
            if(isset($_REQUEST["outward_order_by"]) && !empty($_REQUEST["outward_order_by"]))
                $field=trim($_REQUEST["outward_order_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_outward_order_by=$field;

            
            if(isset($_REQUEST["delivery_to"]) && !empty($_REQUEST["delivery_to"]))
                $field=trim($_REQUEST["delivery_to"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_delivery_to=$field;

            
            if(isset($_REQUEST["vehicle_no"]) && !empty($_REQUEST["vehicle_no"]))
                $field=trim($_REQUEST["vehicle_no"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_vehicle_no=$field;

            
            if(isset($_REQUEST["driver_name"]) && !empty($_REQUEST["driver_name"]))
                $field=trim($_REQUEST["driver_name"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_driver_name=$field;

            
            if(isset($_REQUEST["driver_mob_no"]) && !empty($_REQUEST["driver_mob_no"]))
                $field=trim($_REQUEST["driver_mob_no"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_driver_mob_no=$field;

            
            if(isset($_REQUEST["transporter"]) && !empty($_REQUEST["transporter"]))
                $field=trim($_REQUEST["transporter"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_transporter=$field;

            
            if(isset($_REQUEST["sp_note"]) && !empty($_REQUEST["sp_note"]))
                $field=trim($_REQUEST["sp_note"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_sp_note=$field;

            
            if(isset($_REQUEST["created_by"]) && !empty($_REQUEST["created_by"]))
                $field=trim($_REQUEST["created_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_by=$field;

            
            if(isset($_REQUEST["created_date"]) && !empty($_REQUEST["created_date"]))
                $field=trim($_REQUEST["created_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_created_date=$field;

            
            if(isset($_REQUEST["modified_by"]) && !empty($_REQUEST["modified_by"]))
                $field=trim($_REQUEST["modified_by"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_by=$field;

            
            if(isset($_REQUEST["modified_date"]) && !empty($_REQUEST["modified_date"]))
                $field=trim($_REQUEST["modified_date"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_modified_date=$field;

            
            if(isset($_REQUEST["company_id"]) && !empty($_REQUEST["company_id"]))
                $field=trim($_REQUEST["company_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_company_id=$field;

            
            if(isset($_REQUEST["company_year_id"]) && !empty($_REQUEST["company_year_id"]))
                $field=trim($_REQUEST["company_year_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_company_year_id=$field;

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
