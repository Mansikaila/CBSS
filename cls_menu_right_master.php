<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_menurightmaster 
{                        
public $_menu_right_id;          
    public $_menu_id;          
    public $_right_name;          
    public $_right_text;          
    public $_transactionmode;
    
}

class bll_menurightmaster                           
{   
    public $_mdl;
    public $_dal;

    public function __construct()    
    {
        $this->_mdl =new mdl_menurightmaster(); 
        $this->_dal =new dal_menurightmaster();
    }

    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
               
       
            
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_menu_right_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_menu_right_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_menu_right_master.php");
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

        $sql="CAll csms1_search('t.menu_id, t.right_name, t.right_name, t1.menu_text, t.right_text, t.menu_right_id','tbl_menu_right_master t INNER JOIN tbl_menu_master t1 ON t.menu_id=t1.menu_id')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Menu Id <br><input type=\"text\" data-index=\"1\" placeholder=\"Search Menu Id\" /></th> 
                         <th> Right Name <br><input type=\"text\" data-index=\"2\" placeholder=\"Search Right Name\" /></th> 
                         <th> Right Text <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Right Text\" /></th> 
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
            <form  method=\"post\" action=\"frm_menu_right_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"menu_right_id\" value=\"".$_rs["menu_right_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_menu_right_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"menu_right_id\" value=\"".$_rs["menu_right_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["menu_text"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["right_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["right_text"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
           
            
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"3\">No records available.</td>";
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
 class dal_menurightmaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;

        
        $_dbh->exec("set @p0 = ".$_mdl->_menu_right_id);
        $_pre=$_dbh->prepare("CALL menu_right_master_transaction (@p0,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_menu_id);
        $_pre->bindParam(2,$_mdl->_right_name);
        $_pre->bindParam(3,$_mdl->_right_text);
        $_pre->bindParam(4,$_mdl->_transactionmode);
        $_pre->execute();
        
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL menu_right_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["menu_right_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_menu_right_id=$_rs[0]["menu_right_id"];
        $_mdl->_menu_id=$_rs[0]["menu_id"];
        $_mdl->_right_name=$_rs[0]["right_name"];
        $_mdl->_right_text=$_rs[0]["right_text"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_menurightmaster();

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
 
            
            if(isset($_REQUEST["menu_right_id"]) && !empty($_REQUEST["menu_right_id"]))
                $field=trim($_REQUEST["menu_right_id"]);
            else {
                    $field=0;
           }
    $_bll->_mdl->_menu_right_id=$field;

            
            if(isset($_REQUEST["menu_id"]) && !empty($_REQUEST["menu_id"]))
                $field=trim($_REQUEST["menu_id"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_menu_id=$field;

            
            if(isset($_REQUEST["right_name"]) && !empty($_REQUEST["right_name"]))
                $field=trim($_REQUEST["right_name"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_right_name=$field;

            
            if(isset($_REQUEST["right_text"]) && !empty($_REQUEST["right_text"]))
                $field=trim($_REQUEST["right_text"]);
            else {
                    $field=null;
           }
    $_bll->_mdl->_right_text=$field;

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
