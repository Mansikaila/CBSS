<?php  
include_once(__DIR__ . "/../config/connection.php");
class mdl_menumaster 
{                        
public $_menu_id;          
    public $_module_id;          
    public $_menu_name;          
    public $_menu_text;          
    public $_tab_index;          
    public $_is_display;          
    public $_transactionmode;
}
class bll_menumaster                           
{   
    public $_mdl;
    public $_dal;
    public function __construct()    
    {
        $this->_mdl =new mdl_menumaster(); 
        $this->_dal =new dal_menumaster();
    }
    public function dbTransaction()
    {
        $this->_dal->dbTransaction($this->_mdl);
       if($this->_mdl->_transactionmode =="D")
       {
            header("Location:../srh_menu_master.php");
       }
       if($this->_mdl->_transactionmode =="U")
       {
            header("Location:../srh_menu_master.php");
       }
       if($this->_mdl->_transactionmode =="I")
       {
            header("Location:../frm_menu_master.php");
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

        $sql="CAll csms1_search('t.module_id, t.menu_name, t.menu_name, t1.module_name, t.menu_text, t.tab_index, t.is_display, t.menu_id','tbl_menu_master t INNER JOIN tbl_module_master t1 ON t.module_id=t1.module_id')";
        echo "
        <table  id=\"searchMaster\" class=\"ui celled table display\">
        <thead>
            <tr>
            <th>Action</th> 
            <th> Module Name <br><input type=\"text\" data-index=\"1\" placeholder=\"Search Module Name\" /></th> 
                         <th> Menu Name <br><input type=\"text\" data-index=\"2\" placeholder=\"Search Menu Name\" /></th> 
                         <th> Menu Text <br><input type=\"text\" data-index=\"3\" placeholder=\"Search Menu Text\" /></th> 
                         <th> Tab Index <br><input type=\"text\" data-index=\"4\" placeholder=\"Search Tab Index\" /></th> 
                         <th> Is Display <br><input type=\"text\" data-index=\"5\" placeholder=\"Search Is Display\" /></th> 
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
            <form  method=\"post\" action=\"frm_menu_master.php\" style=\"display:inline; margin-rigth:5px;\">
            <i class=\"fa fa-edit update\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"menu_id\" value=\"".$_rs["menu_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"U\"  />
            </form> <form  method=\"post\" action=\"classes/cls_menu_master.php\" style=\"display:inline;\">
            <i class=\"fa fa-trash delete\" style=\"cursor: pointer;\"></i>
            <input type=\"hidden\" name=\"menu_id\" value=\"".$_rs["menu_id"]."\" />
            <input type=\"hidden\" name=\"transactionmode\" value=\"D\"  />
            </form>
            </td>";
        $fieldvalue=$_rs["module_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["menu_name"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["menu_text"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["tab_index"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $fieldvalue=$_rs["is_display"];
                            $_grid.= "
                            <td> ".$fieldvalue." </td>"; 
                       $_grid.= "</tr>\n";
        }   
         if($j==0) {
                $_grid.= "<tr>";
                $_grid.="<td colspan=\"5\">No records available.</td>";
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
 class dal_menumaster                         
{
    public function dbTransaction($_mdl)                     
    {
        global $_dbh;
        $_dbh->exec("set @p0 = ".$_mdl->_menu_id);
        $_pre=$_dbh->prepare("CALL menu_master_transaction (@p0,?,?,?,?,?,?) ");
        $_pre->bindParam(1,$_mdl->_module_id);
        $_pre->bindParam(2,$_mdl->_menu_name);
        $_pre->bindParam(3,$_mdl->_menu_text);
        $_pre->bindParam(4,$_mdl->_tab_index);
        $_pre->bindParam(5,$_mdl->_is_display);
        $_pre->bindParam(6,$_mdl->_transactionmode);
        $_pre->execute();
    }
    public function fillModel($_mdl)
    {
        global $_dbh;
        $_pre=$_dbh->prepare("CALL menu_master_fillmodel (?) ");
        $_pre->bindParam(1,$_REQUEST["menu_id"]);
        $_pre->execute();
        $_rs=$_pre->fetchAll(); 
        if(!empty($_rs)) {

        $_mdl->_menu_id=$_rs[0]["menu_id"];
        $_mdl->_module_id=$_rs[0]["module_id"];
        $_mdl->_menu_name=$_rs[0]["menu_name"];
        $_mdl->_menu_text=$_rs[0]["menu_text"];
        $_mdl->_tab_index=$_rs[0]["tab_index"];
        $_mdl->_is_display=$_rs[0]["is_display"];
        $_mdl->_transactionmode =$_REQUEST["transactionmode"];
        }
    }
}
$_bll=new bll_menumaster();
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
            if(isset($_REQUEST["menu_id"]) && !empty($_REQUEST["menu_id"]))
                $field=trim($_REQUEST["menu_id"]);
            else {
                    $field=0;
           }
            $_bll->_mdl->_menu_id=$field;
            if(isset($_REQUEST["module_id"]) && !empty($_REQUEST["module_id"]))
                $field=trim($_REQUEST["module_id"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_module_id=$field;
            if(isset($_REQUEST["menu_name"]) && !empty($_REQUEST["menu_name"]))
                $field=trim($_REQUEST["menu_name"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_menu_name=$field;
            if(isset($_REQUEST["menu_text"]) && !empty($_REQUEST["menu_text"]))
                $field=trim($_REQUEST["menu_text"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_menu_text=$field;
            if(isset($_REQUEST["tab_index"]) && !empty($_REQUEST["tab_index"]))
                $field=trim($_REQUEST["tab_index"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_tab_index=$field;
            if(isset($_REQUEST["is_display"]) && !empty($_REQUEST["is_display"]))
                $field=trim($_REQUEST["is_display"]);
            else {
                    $field=null;
           }
            $_bll->_mdl->_is_display=$field;
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
