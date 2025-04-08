<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_usermaster 
{                        
public $_user_id;          
    public $_login_id;          
    public $_login_pass;          
    public $_person_name;          
    public $_is_enable;          
    public $_created_date;          
    public $_created_by;          
    public $_modified_date;          
    public $_modified_by;          
    public $_transactionmode;
    
}

class bll_usermaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_usermaster(); 
        $this->_dal =new dal_usermaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_user_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_user_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_user_master.php");
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

        $sql="CAll csms1_search('t.login_id, t.login_pass, t.person_name, t.is_enable, t.user_id','tbl_user_master t')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> User Name <br><input type=\"text\" data-index=\"1\" placeholder=\"Search User Name\" /></th> 
                         <th> Password <br><input type=\"text\" data-index=\"2\" placeholder=\"Search Password\" /></th> 
                         <th> Person Name <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Person Name\" /></th> 
                         <th> Want to Enable <br><input type=\"text\" data-index=\"4\" placeholder=\"Search Want to Enable\" /></th> 
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
            <form  method=\"post\" action=\"frm_user_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"user_id\" value=\"".$_rs["user_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_user_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"user_id\" value=\"".$_rs["user_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["login_id"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["login_pass"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["person_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["is_enable"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"8\">No records available.</td>";
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
            $sql="CAll cs_check_duplicate('".$column_name."','".$column_value."','".$id_name."','".$id_value."','".$table_name."',@is_duplicate)";
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
 class dal_usermaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_user_id);
        $_pre=$_dbh->prepare("CALL user_master_transaction (@p0,?,?,?,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_login_id);
        $_pre->bindParam(2,$_mdl->_login_pass);
        $_pre->bindParam(3,$_mdl->_person_name);
        $_pre->bindParam(4,$_mdl->_is_enable);
        $_pre->bindParam(5,$_mdl->_created_date);
        $_pre->bindParam(6,$_mdl->_created_by);
        $_pre->bindParam(7,$_mdl->_modified_date);
        $_pre->bindParam(8,$_mdl->_modified_by);
        $_pre->bindParam(9,$_mdl->_transactionmode);
        $_pre->execute();
        
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL user_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["user_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_user_id=$_rs[0]["user_id"];
        $_mdl->_login_id=$_rs[0]["login_id"];
        $_mdl->_login_pass=$_rs[0]["login_pass"];
        $_mdl->_person_name=$_rs[0]["person_name"];
        $_mdl->_is_enable=$_rs[0]["is_enable"];
        $_mdl->_created_date=$_rs[0]["created_date"];
        $_mdl->_created_by=$_rs[0]["created_by"];
        $_mdl->_modified_date=$_rs[0]["modified_date"];
        $_mdl->_modified_by=$_rs[0]["modified_by"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_usermaster();

if(isset($_REQUEST["action"]))
{
    $action=$_REQUEST["action"];
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
 
            
            if(isset($_REQUEST["user_id"]) && !empty($_REQUEST["user_id"]))
                $field=trim($_REQUEST["user_id"]);
            else {
                    $field=0;
           }
            $_bll->_mdl->_user_id=$field;

            
            if(isset($_REQUEST["login_id"]) && !empty($_REQUEST["login_id"]))
                $field=trim($_REQUEST["login_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_login_id=$field;

            
            if(isset($_REQUEST["login_pass"]) && !empty($_REQUEST["login_pass"]))
                $field=trim($_REQUEST["login_pass"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_login_pass=$field;

            
            if(isset($_REQUEST["person_name"]) && !empty($_REQUEST["person_name"]))
                $field=trim($_REQUEST["person_name"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_person_name=$field;

            
            if(isset($_REQUEST["is_enable"]) && !empty($_REQUEST["is_enable"]))
                $field=trim($_REQUEST["is_enable"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_is_enable=$field;

            
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
