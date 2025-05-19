<?php
    include("classes/cls_item_preservation_price_list_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
    include("form_item_dropdown.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $itemId = intval($_POST['item_id']);

    if ($itemId > 0) {
       // $_bll = new bll_itempreservationpricelistmaster();
        $_bll->_mdl->_item_id = $itemId;
         $_bll->fillModel();
        //$_blldetail = new bll_itempreservationpricelistdetail();
        $_blldetail->_mdl->_item_id = $itemId;
        //$_blldetail->pageSearch(); 
    }
    exit;
}

    $transactionmode="";

    if(isset($_REQUEST["transactionmode"]))       
    {    
        $transactionmode=$_REQUEST["transactionmode"];
    }
    if( $transactionmode=="U")       
    {    
        $_bll->fillModel();
        $label="Update";
    } else {
        $label="Add";
    }
?>
<!-- ADD THE CLASS layout-top-nav TO REMOVE THE SIDEBAR. -->
<body class="hold-transition skin-blue layout-top-nav">
    
<?php
    include("include/body_open.php");
?>
<div class="wrapper">
<?php
    include("include/navigation.php");
?>
  <!-- Full Width Column -->
  <div class="content-wrapper">
    <div class="container-fluid">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          <?php echo $label; ?> Data
        </h1>
        <ol class="breadcrumb">
          <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="srh_item_preservation_price_list_master.php"><i class="fa fa-dashboard"></i> Item Preservation Price List Master</a></li>
          <li class="active"><?php echo $label; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="itemForm"   method="post" class="form-horizontal needs-validation" novalidate>
<div class="box-body">
    
        <div class="row">
            <label for="item_id" class="col-11 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label">Item</label>
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xl-2" >
                <?php 
                $selectedItem = isset($_bll->_mdl->_item_id) ? $_bll->_mdl->_item_id : null;
            $database_name = "csms1";
               $sql = "CALL ".$database_name."_search_detail(?,?,?)";

                echo getDropdown(
                    "tbl_item_master",        
                    "item_id",                
                    "item_name",              
                    "status = 1",           
                    "item_id",                 
                    $selectedItem,             
                    "form-select",           
                    "required"                
                );
                ?>
                <div class="invalid-feedback">Please select an item</div>
            </div>
            
        </div>

</div>

        </form>


    <form id="masterForm" action="classes/cls_item_preservation_price_list_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">
    <?php
            global $database_name;
            global $_dbh;
            $hidden_str="";
            $table_name="tbl_item_preservation_price_list_master";
            $lbl_array=array();
            $field_array=array();
            $err_array=array();
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    if($generator_options) {
                        $table_layout=$generator_options->table_layout;
                        $fields_names=$generator_options->field_name;
                        $fields_types=$generator_options->field_type;
                        $field_scale=$generator_options->field_scale;
                        $dropdown_table=$generator_options->dropdown_table;
                        $label_column=$generator_options->label_column;
                        $value_column=$generator_options->value_column;
                        $where_condition=$generator_options->where_condition;
                        $fields_labels=$generator_options->field_label;
                        $field_display=$generator_options->field_display;
                        $field_required=$generator_options->field_required;
                        $allow_zero=$generator_options->allow_zero;
                        $allow_minus=$generator_options->allow_minus;
                        $chk_duplicate=$generator_options->chk_duplicate;
                        $field_data_type=$generator_options->field_data_type;
                        $field_is_disabled=$generator_options->is_disabled;
                        $after_detail=$generator_options->after_detail;

                        if($table_layout=="horizontal") {
                            $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                            $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
                        } else {
                            $label_layout_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                            $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                        }
                        
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
                                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$duplicate_str="";
                                 $cls_field_name="_".$fields_names[$i];$is_disabled=0;$disabled_str="";
                                 
                                if(!empty($field_required) && in_array($fields_names[$i],$field_required)) {
                                    $required=1;
                                }
                                if(!empty($field_is_disabled) && in_array($fields_names[$i],$field_is_disabled)) {
                                    $is_disabled=1;
                                }
                                if(!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) {
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $duplicate_str="duplicate";
                                }
                                if($fields_labels[$i]) {
                                    $lbl_str='<label for="'.$fields_names[$i].'" class="'.$label_layout_classes.'">'.$fields_labels[$i].'';
                                     if($table_layout=="vertical") {
                                        $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                                    } 
                                } else {
                                    if($table_layout=="vertical") {
                                        $field_layout_classes="col-12";
                                    } 
                                }   
                                if($required) {
                                    $required_str="required";
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $lbl_str.="*";
                                }
                                if($is_disabled) {
                                    $disabled_str="disabled";
                                }
                               
                                $lbl_str.="</label>";
                                switch($fields_types[$i]) {
                                    case "text":
                                    case "email":
                                    case "file":
                                    case "date":
                                    case "datetime-local":
                                    case "radio":
                                    case "checkbox":
                                    case "number":
                                    case "select":
                                        $value="";$field_str="";$cls="";$flag=0;
                                         $table=explode("_",$fields_names[$i]);
                                            $field_name=$table[0]."_name";
                                            $fields=$fields_names[$i].", ".$table[0]."_name";
                                            $tablename="tbl_".$table[0]."_master";
                                            $selected_val="";
                                            if(isset($_bll->_mdl->$cls_field_name)) {
                                                $selected_val=$_bll->_mdl->$cls_field_name;
                                            }
                                            if(!empty($where_condition[$i]))
                                                $where_condition_val=$where_condition[$i];
                                            else {
                                                $where_condition_val=null;
                                            }
                                            if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                             $cls.=$required_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $flag=1;
                                                $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str, $fields_types[$i]).$error_container;
                                            }
                                            else{
                                                    if($transactionmode=="U" && $_bll->_mdl->$cls_field_name==1) {
                                                        $chk_str="checked='checked'";
                                                    }
                                                    $value="1";
                                                    $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                            }
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str;
                                            $chk_str="";
                                             if(isset($_bll->_mdl)) {
                                                    $value=$_bll->_mdl->$cls_field_name; 
                                            }
                                        }
                                         if($fields_types[$i]=="number") {
                                            $step="";
                                            if(!empty($field_scale[$i]) && $field_scale[$i]>0) {
                                                for($k=1;$k<$field_scale[$i];$k++) {
                                                    $step.=0;
                                                }
                                                $step="0.".$step."1";
                                            } else {
                                                $step=1;
                                            }
                                            $step_str='step="'.$step.'"';
                                             $min=1; 
                                             if(!empty($allow_zero) && in_array($fields_names[$i],$allow_zero)) 
                                                 $min=0;
                                             if(!empty($allow_minus) && in_array($fields_names[$i],$allow_minus)) 
                                                $min="";

                                             $min_str='min="'.$min.'"';
                                         }
                                         if(!empty($value) && ($fields_types[$i]=="date" || $fields_types[$i]=="datetime-local" || $fields_types[$i]=="datetime" || $fields_types[$i]=="timestamp")) {
                                                $value=date("Y-m-d",strtotime($value));
                                         }
                                         if($fields_types[$i]=="select") {
                                            $cls="form-select ".$required_str." ".$duplicate_str;
                                           
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i]))
                                                $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str).$error_container;
                                        } else {
                                            if($flag==0) {
                                                $field_str.='<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'" value= "'.$value.'"  '.$min_str.' '.$step_str.' '.$chk_str.'  '.$disabled_str.' '.$required_str.' />
                                                '.$error_container;
                                            }
                                        }
                                        break;
                                    case "hidden":
                                        $lbl_str="";
                                        if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                            $hiddenvalue=0;
                                        else
                                            $hiddenvalue="";
                                        if($fields_names[$i]!="modified_by" && $fields_names[$i]!="modified_date") {
                                            if($fields_names[$i]=="company_id") {
                                                $hiddenvalue=COMPANY_ID;
                                            }
                                            else if($fields_names[$i]=="created_by") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=USER_ID;
                                                }
                                            } else if($fields_names[$i]=="created_date") {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } else {
                                                    $hiddenvalue=date("Y-m-d H:i:s");
                                                }
                                            } else {
                                                if($transactionmode=="U") {
                                                    $hiddenvalue=$_bll->_mdl->$cls_field_name;
                                                } 
                                            }
                                            $hidden_str.='
                                            <input type="'.$fields_types[$i].'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value= "'.$hiddenvalue.'"  />';
                                        }
                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset($_bll->_mdl)){
                                             $value=$_bll->_mdl->$cls_field_name;
                                            }
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" '.$disabled_str.' placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'"  '.$required_str.' >'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                    default:
                                        break;
                                } //switch ends
                                 $cls_err="";
                                    $lbl_err="";
                                   
                                if(empty($after_detail) || (!empty($after_detail) && !in_array($fields_names[$i],$after_detail))) {
                                    if($table_layout=="vertical" && $fields_types[$i]!="hidden") {
                                ?>
                                <div class="row mb-3 align-items-center">
                                <?php
                                    } // verticle condition ends
                                    echo $lbl_str;
                                    if($field_str) {
                                    ?>
                                    <div class="<?php echo $field_layout_classes." ".$cls_err; ?>"  >
                                    <?php
                                            echo $field_str;
                                            echo $lbl_err;
                                    ?>
                                    </div>
                                <?php
                                    }
                                if($table_layout=="vertical" && $fields_types[$i]!="hidden") {
                                ?>
                                </div>
                                <?php
                                    } // verticle condition ends
                                } else {
                                    $lbl_array[]=$lbl_str;
                                    $field_array[]=$field_str;
                                    $err_array[]=$lbl_err;
                                    $clserr_array[]=$cls_err;
                                }
                            } //for loop ends
                        } // field_types if ends
                    }
             } 
            
            ?>
                 </div><!-- /.row -->
              </div>
              <!-- /.box-body -->
            <!-- detail table content-->
                <div class="box-body">
                    <div class="box-detail" id="gridContainer">
                        <?php
                            if(isset($_blldetail))
                                $_blldetail->pageSearch(); 
                        ?>
                        
                </div>
              </div>
              <!-- /.box-body detail table content -->
<?php
    if(!empty($field_array)) {
?>
     <!-- remaining main table content-->
    <div class="box-body">
        <div class="form-group row gy-2">
    <?php
        for($j=0;$j<count($field_array);$j++) {
        if($table_layout=="vertical") {
    ?>
    <div class="row mb-3 align-items-center">
    <?php
            } // verticle condition ends
            echo $lbl_array[$j];
            if($field_array[$j]) {
            ?>
            <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $clserr_array; ?>"  >
            <?php
                    echo $field_array[$j];
                    echo $err_array[$j];
            ?>
            </div>
    <?php
            }
     if($table_layout=="vertical") {
    ?>
    </div>
    <?php
            } // verticle condition ends
        } // after detail for loop ends
    ?>
    </div>
</div>
<?php
    } // empty detail array if ends
?>
<!-- .box-footer -->
              <div class="box-footer">
               <?php echo  $hidden_str; ?>
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="modified_by" name="modified_by" value="<?php echo USER_ID; ?>">
                  <input type="hidden" id="hidden_item_id" name="hidden_item_id" value="">
                <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date("Y-m-d H:i:s"); ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                 <input type="hidden" id="item_preservation_price_list_id" name="item_preservation_price_list_id" 
                 value="<?php echo isset($_bll->_mdl->_item_preservation_price_list_id) ? $_bll->_mdl->_item_preservation_price_list_id : 0; ?>">
                  <input type="hidden" name="item_id" value="<?php echo isset($mdl->_item_id) ? $mdl->_item_id : ''; ?>" <?php echo ($transactionmode == 'U') ? '' : ''; ?>>
                 <input type="hidden" id="deleted_records" name="deleted_records" />
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_item_preservation_price_list_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
              </div>
              <!-- /.box-footer -->
        </form>
        <!-- form end -->
          </div>
          </div>
      </section>
      <!-- /.content -->
    </div>
    
     <!-- Modal -->
    <div class="detail-modal">
        <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
          <div class="modal-dialog  modal-dialog-scrollable modal-xl">
            <div class="modal-content">
            <form id="popupForm"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
              <div class="modal-header">
                  <h4 class="modal-title" id="modalToggleLabel">Add Customer Contact Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="box-body container-fluid">
                    <div class="form-group row" >
    <?php
            $hidden_str="";
            $table_name_detail="tbl_item_preservation_price_list_detail";
            $select = $_dbh->prepare("SELECT `generator_options` FROM `tbl_generator_master` WHERE `table_name` = ?");
            $select->bindParam(1, $table_name_detail);
            $select->execute();
            $row = $select->fetch(PDO::FETCH_ASSOC);
             if($row) {
                    $generator_options=json_decode($row["generator_options"]);
                    if($generator_options) {
                        $fields_names=$generator_options->field_name;
                        $fields_types=$generator_options->field_type;
                        $field_scale=$generator_options->field_scale;
                        $dropdown_table=$generator_options->dropdown_table;
                         $label_column=$generator_options->label_column;
                         $value_column=$generator_options->value_column;
                         $where_condition=$generator_options->where_condition;
                        $fields_labels=$generator_options->field_label;
                        $field_display=$generator_options->field_display;
                        $field_required=$generator_options->field_required;
                        $allow_zero=$generator_options->allow_zero;
                        $allow_minus=$generator_options->allow_minus;
                        $chk_duplicate=$generator_options->chk_duplicate;
                        $field_data_type=$generator_options->field_data_type;
                        $field_is_disabled=$generator_options->is_disabled;
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
                                $required="";$checked="";$field_str="";$lbl_str="";$required_str="";$min_str="";$step_str="";$error_container="";$is_disabled=0;$disabled_str="";$duplicate_str="";
                                $display_str="";
                                $cls_field_name="_".$fields_names[$i];
                                 
                                if(!empty($field_required) && in_array($fields_names[$i],$field_required)) {
                                    $required=1;
                                }
                                if(!empty($field_is_disabled) && in_array($fields_names[$i],$field_is_disabled)) {
                                    $is_disabled=1;
                                }
                                if(!empty($chk_duplicate) && in_array($fields_names[$i],$chk_duplicate)) {
                                    $error_container='<div class="invalid-feedback"></div>';
                                    $duplicate_str="duplicate";
                                }
                                if(!empty($field_display) && in_array($fields_names[$i],$field_display)) {
                                    $display_str="display";
                                }
                                $lbl_str='<label for="'.$fields_names[$i].'" class="col-sm-4 control-label">'.$fields_labels[$i].'';
                                if($required) {
                                    $required_str="required";
                                    $lbl_str.="*";
                                    $error_container='<div class="invalid-feedback"></div>';
                                }
                                if($is_disabled) {
                                    $disabled_str="disabled";
                                }
                                
                                $lbl_str.="</label>";
                                switch($fields_types[$i]) {
                                    case "text":
                                    case "email":
                                    case "file":
                                    case "date":
                                    case "datetime-local":
                                    case "radio":
                                    case "checkbox":
                                    case "number":
                                    case "select":
                                        $value="";
                                        $field_str=""; $cls="";$flag=0;
                                         $table=explode("_",$fields_names[$i]);
                                            $field_name=$table[0]."_name";
                                            $fields=$fields_names[$i].", ".$table[0]."_name";
                                            $tablename="tbl_".$table[0]."_master";
                                            $selected_val="";
                                            if(isset(${"val_$fields_names[$i]"})) {
                                                $selected_val=${"val_$fields_names[$i]"};
                                            }
                                            if(!empty($where_condition[$i]))
                                                $where_condition_val=$where_condition[$i];
                                            else {
                                                $where_condition_val=null;
                                            }
                                        if($fields_types[$i]=="checkbox" || $fields_types[$i]=="radio") {
                                            $cls.=$display_str." ".$required_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i])) {
                                                $flag=1;
                                                $field_str.=getChecboxRadios($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val, $cls, $required_str, $fields_types[$i]).$error_container;
                                            } else {
                                                if(isset(${"val_$fields_names[$i]"}) &&  ${"val_$fields_names[$i]"}==1) {
                                                    $chk_str="checked='checked'";
                                                }
                                                $value="1";
                                                $field_str.='<input type="hidden" name="'.$fields_names[$i].'" value="0" />';
                                                }
                                        } else {
                                            $cls.="form-control ".$required_str." ".$duplicate_str." ".$display_str;
                                            $chk_str="";
                                             if(isset(${"val_$fields_names[$i]"}))  {
                                                $value=$fields_names[$i];
                                             }
                                        }
                                         if($fields_types[$i]=="number") {
                                            $step="";
                                            if(!empty($field_scale[$i]) && $field_scale[$i]>0) {
                                                for($k=1;$k<$field_scale[$i];$k++) {
                                                    $step.=0;
                                                }
                                                $step="0.".$step."1";
                                            } else {
                                                $step=1;
                                            }
                                            $step_str='step="'.$step.'"';
                                             $min=1; 
                                             if(!empty($allow_zero) && in_array($fields_names[$i],$allow_zero)) 
                                                 $min=0;
                                             if(!empty($allow_minus) && in_array($fields_names[$i],$allow_minus)) 
                                                $min="";

                                             $min_str='min="'.$min.'"';
                                         }
                                         if($fields_types[$i]=="select") {
                                            $cls="form-select ".$required_str." ".$duplicate_str." ".$display_str;
                                            if(!empty($dropdown_table[$i]) && !empty($label_column[$i]) && !empty($value_column[$i]))
                                                $field_str.=getDropdown($dropdown_table[$i],$value_column[$i],$label_column[$i],$where_condition_val,$fields_names[$i],$selected_val,$cls,$required_str);
                                                $field_str.=$error_container;
                                                } else {
                                                 if($flag==0) {
                                                        $field_str.='<input type="'.$fields_types[$i].'" class="'.$cls.'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'" value= "'.$value.'"  '.$required_str.' '.$min_str.'  '.$step_str.' '.$chk_str.' '.$disabled_str.' />
                                                        '.$error_container;
                                                }
                                        }
                                        break;
                                    case "hidden":
                                        $lbl_str="";
                                        if($field_data_type[$i]=="int" || $field_data_type[$i]=="bigint"  || $field_data_type[$i]=="tinyint" || $field_data_type[$i]=="decimal")
                                            $hiddenvalue=0;
                                        else
                                            $hiddenvalue="";
                                       
                                            if(isset(${"val_$fields_names[$i]"})) {
                                                $hiddenvalue=${"val_$fields_names[$i]"};
                                            }
                                             if($fields_names[$i]!="item_preservation_price_list_id") {
                                                $hidden_str.='
                                                <input type="'.$fields_types[$i].'" id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" value= "'.$hiddenvalue.'" class="exclude-field"  />';
                                                }

                                        break;
                                    case "textarea":
                                        $value="";
                                        if(isset(${"val_$fields_names[$i]"}))
                                             $value=${"val_$fields_names[$i]"};
                                        $field_str.='<textarea id="'.$fields_names[$i].'" name="'.$fields_names[$i].'" class="'.$cls.'" placeholder="Enter '.ucwords(str_replace("_"," ",$fields_names[$i])).'" '.$required_str.' '.$disabled_str.'>'.$value.'</textarea>
                                        '.$error_container;
                                        break;
                                    default:
                                        break;
                                } //switch ends
                                 if($field_str) {
                            ?>
                                <div class="col-sm-6 row gy-1">
                                  <?php echo $lbl_str; ?>
                                  <div class="col-sm-8">
                                    <?php echo $field_str; ?>
                                  </div>
                                </div>
                        <?php
                        }
                            } //for loop ends
                        } // field_types if ends
                    }
             } 
            ?> 
                    </div>
              </div>
              </div>
              <div class="modal-footer">
                
                <?php echo $hidden_str; ?>
               
              </div>
                </form>
            </div> <!-- /.modal-content -->
          </div>  <!-- /.modal-dialog -->
        </div> <!-- /.modal -->
    </div>
    <!-- /Modal -->
    
    <!-- /.container -->
  </div>
  <!-- /.content-wrapper -->
  <?php
    include("include/footer.php");
?>
</div>
<!-- ./wrapper -->

<?php
    include("include/footer_includes.php");
?>
<script>
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    let detailIdLabel="";
    const duplicateInputs = document.querySelectorAll(".duplicate");
    const masterForm = document.getElementById("masterForm");
    
    const firstInput = masterForm.querySelector("input:not([type=hidden]), select, textarea");
    if (firstInput) {
        firstInput.focus();
    }
function checkDuplicate(input) {
    let column_value = input.value.trim();
    if (column_value === "") return true; // Allow empty values
    
    let id_column = "<?php echo "item_preservation_price_list_id"; ?>";
    let id_value = document.getElementById(id_column).value;
    let column_name = input.name;
    
    return new Promise((resolve) => {
        $.ajax({
            url: "classes/cls_item_preservation_price_list_master.php",
            type: "POST",
            data: { 
                type: "ajax",
                column_name: column_name,
                column_value: column_value,
                id_name: id_column,
                id_value: id_value,
                table_name: "<?php echo "tbl_item_preservation_price_list_master"; ?>"
            },
            success: function(response) {
                if (response == 1) {
                    input.classList.add("is-invalid");
                    let message = input.validationMessage || "Duplicate Value";
                    if(input.nextElementSibling) {
                        input.nextElementSibling.textContent = message;
                    }
                    resolve(false); // Not valid (duplicate)
                } else {
                    input.classList.remove("is-invalid");
                    if(input.nextElementSibling) {
                        input.nextElementSibling.textContent = "";
                    }
                    resolve(true); // Valid (not duplicate)
                }
            },
            error: function() {
                console.log("Error checking duplicate");
                resolve(true); // Assume valid if error occurs
            }
        });
    });
}

// Update the event listener
duplicateInputs.forEach((input) => {
    input.addEventListener("blur", async function() {
        await checkDuplicate(input);
    });
});

         const tableHead = document.getElementById("tableHead");
        const tableBody = document.getElementById("tableBody");
        const form = document.getElementById("popupForm");
        const modalDialog = document.getElementById("modalDialog");
        const modal = new bootstrap.Modal(modalDialog);
    
        document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
            let rowData = {};
            if(!row.classList.contains("norecords")) {
                rowData[row.dataset.label]=row.dataset.id;
                detailIdLabel=row.dataset.label;
                editIndex++;
                row.querySelectorAll("td[data-label]").forEach(td => {
                    if(!td.classList.contains("actions")){
                        rowData[td.dataset.label] = td.innerText;
                    }
                });
                rowData["detailtransactionmode"]="U";
                jsonData[editIndex]=rowData;
            }
        });
    
    modalDialog.addEventListener("hidden.bs.modal", function () {
     clearForm(form);
     setFocustAfterClose();
    });
    
    function openModal(index = -1) {
  
        if (index >= 0) {
            editIndex = index;
            const data = jsonData[index];

            for (let key in data) {
                const inputFields = form.elements[key]; // May return NodeList if multiple inputs exist

                if (!inputFields) continue; // Skip if field not found

                if (inputFields.length) {
                    // If multiple inputs exist (radio, checkbox, hidden with same name)
                    inputFields.forEach(inputField => {
                        if (inputField.type === "checkbox" || inputField.type === "radio") {
                             if (inputField.value === data[key]) {
                                 inputField.checked = true;
                                jQuery("#"+key).attr( "checked", "checked" );
                            } else {
                                $("#"+key).removeAttr("checked");
                            }
                        }
                        else if (inputField.type !== "hidden") {
                            inputField.value = data[key]; // Avoid setting hidden field values
                        }
                    });
                } else {
                        inputFields.value = data[key]; // Avoid hidden fields
                }
            }
        } else {
            editIndex = -1;
            clearForm(form);
        }
        modal.show();

        // Ensure focus on the first visible field
        setTimeout(() => {
            const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close), select, textarea");
            if (firstInput) firstInput.focus();
        }, 10);
    }

    function saveData() {
    
        const formData = new FormData(form);
        const newEntry = {};
        const allEntries= {};

         // Convert form data to object (excluding hidden fields)
          for (const [key, value] of formData.entries()) {
            if (!getHiddenFields().includes(key) && getDisplayFields().includes(key)) {
                newEntry[key] = value;
            } 
            if (editIndex >= 0) {
                if(jsonData[editIndex].hasOwnProperty(key)) {
                    jsonData[editIndex][key] = value;
                } 
            }
            allEntries[key]=value;
          }
        
        if($("#norecords").length>0) {
            $("#norecords").remove();
        }
        
        if (editIndex >= 0) {
            //jsonData[editIndex] = allEntries;
            updateTableRow(editIndex, newEntry);
            modal.hide();
            Swal.fire({
                icon: "success",
                title: "Updated Successfully",
                text: "The record has been updated successfully!",
                showConfirmButton: true,
                showClass: {
                    popup: "" // Disable the popup animation
                },
                hideClass: {
                    popup: "" // Disable the popup hide animation
                }
            }).then((result) => {
                 setFocustAfterClose();
            });
        } else {
            allEntries["detailtransactionmode"]="I";
            jsonData.push(allEntries);
            appendTableRow(newEntry, jsonData.length - 1);
            modal.hide();
            Swal.fire({
                icon: "success",
                title: "Added Successfully",
                text: "The record has been added successfully!",
                showConfirmButton: true,
                showClass: {
                    popup: "" // Disable the popup animation
                },
                hideClass: {
                    popup: "" // Disable the popup hide animation
                }
            }).then((result) => {
                  if (result.isConfirmed) {
                    modal.show();
                    setTimeout(() => {
                        const firstInput = form.querySelector("input:not([type=hidden]), input:not(.btn-close)");
                        if (firstInput) firstInput.focus();
                    }, 100);
                  }
            });
        }
        clearForm(form);
    }
    function getHiddenFields() {
      
        let hiddenFields = Array.from(form.elements)
            .filter(input => input.type === "hidden" && input.classList.contains("exclude-field"))
            .map(input => input.name);

        // Add a static entry
        hiddenFields.push("detailtransactionmode");

        return hiddenFields;
    }
    function getDisplayFields() {
        let displayFields=[];
        let formElements = Array.from(form.elements);
        formElements.forEach(input => {
            if (input.length) { // Handle RadioNodeList
                for (let element of input) {
                    if (element.classList && element.classList.contains("display")) {
                        displayFields.push(input.name);
                        break;
                    }
                }
            } else if (input.classList && input.classList.contains("display")) { 
                displayFields.push(input.name);
            }
        });
      return displayFields;
  }
    function appendTableRow(rowData, index) {
        const row = document.createElement("tr");
        var id=0;
        if(detailIdLabel!=""){
            id=rowData[detailIdLabel];
        } 
        row.setAttribute("data-id", id);
        addActions(row,index,id);       

        Object.keys(rowData).forEach(col => {
            if (!getHiddenFields().includes(col) && getDisplayFields().includes(col))  {
                const cell = document.createElement("td");
                cell.textContent = rowData[col] || "";
                cell.setAttribute("data-label", col);
                row.appendChild(cell);
            }
        });

        tableBody.appendChild(row);
    }

function updateTableRow(index, rowData) {
        const row = tableBody.children[index];
        var id=0;
      if(detailIdLabel!=""){
            id=rowData[detailIdLabel];
        } 
        row.innerHTML = "";
        addActions(row,index,id);

        Object.keys(rowData).forEach(col => {
            const cell = document.createElement("td");
            cell.setAttribute("data-label", col);
            cell.textContent = rowData[col] || "";
            row.appendChild(cell);
        });
    }
    function addActions(row,index,id) {
        const actionCell = document.createElement("td");
        actionCell.classList.add("actions");
        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.classList.add("btn", "btn-info", "btn-sm","me-2", "edit-btn");
        editButton.setAttribute("data-index", index);
        editButton.setAttribute("data-id", id);

        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.classList.add("btn", "btn-danger", "btn-sm","delete-btn");
        deleteButton.setAttribute("data-index", index);
        deleteButton.setAttribute("data-id", id);
        
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);
        row.appendChild(actionCell);
    }
    function setFocustAfterClose() {
        document.getElementById("detailBtn").focus();
    }
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("edit-btn")) {
            event.preventDefault(); // Stops the required field validation trigger
            const index = event.target.getAttribute("data-index");
            openModal(index);
        }
    });
    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("delete-btn")) {
            event.preventDefault(); // Stops the required field validation trigger
            const index = event.target.getAttribute("data-index");
            const id = event.target.getAttribute("data-id");
            deleteRow(index,id);
        }
    });
    function deleteRow(index,id) {
        Swal.fire({
          title: "Are you sure you want to delete this record?",
          text: "You won't be able to revert it!",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
            if(id>0) {
                jsonData[index]["detailtransactionmode"]="D";
                deleteData.push(jsonData[index]);
            }
            // Remove the item from the jsonData array
            jsonData.splice(index, 1);
            tableBody.innerHTML = "";
            const numberOfColumns = document.querySelector("table th") ? document.querySelector("table th").parentElement.children.length : 0;
            // Check if there are any rows left
            if (jsonData.length === 0) {
                // If no rows, add a row saying "No records"
                const noRecordsRow = document.createElement("tr");
                for(var i=1; i< numberOfColumns; i++) {
                    const noRecordsCell = document.createElement("td");
                    if(i==1) {
                        noRecordsCell.colSpan = numberOfColumns;
                        noRecordsCell.textContent = "No records available";
                    }
                    noRecordsRow.appendChild(noRecordsCell);
                }
                noRecordsRow.setAttribute("id","norecords");
                noRecordsRow.classList.add("norecords"); 
                tableBody.appendChild(noRecordsRow);
            } else {
                // If there are rows left, re-populate the table
                jsonData.forEach((data, idx) => appendTableRow(data, idx));
            }
          }
        });
    }
    $("#popupForm" ).on( "submit", function( event ) {
        event.preventDefault();
        if (!this.checkValidity()) {
            event.stopPropagation();
            let i=0;
            let firstelement;
            this.querySelectorAll(":invalid").forEach(function (input) {
              if(i==0) {
                firstelement=input;
              }
              input.classList.add("is-invalid");
              input.nextElementSibling.textContent = input.validationMessage; 
              i++;
            });
            if(firstelement) firstelement.focus(); 
            return false;
          } 
        saveData();
    } );
    // Expose functions globally
    window.openModal = openModal;
    window.saveData = saveData;

document.getElementById("btn_add").addEventListener("click", function (event) {
    event.preventDefault();
    const form = document.getElementById("masterForm");
    const itemId = document.getElementById("item_id").value;
    const masterId = document.getElementById("item_preservation_price_list_id").value;

    checkDuplicate(itemId, masterId).then(isDuplicate => {
        if (isDuplicate) {
            Swal.fire({
                icon: "error",
                title: "Duplicate Item",
                text: "This item already has a price list. Please select a different item.",
                showConfirmButton: true
            });
            return false;
        }

        if (!form.checkValidity()) {
            event.stopPropagation();
            let i=0;
            let firstelement;
            form.querySelectorAll(":invalid").forEach(function (input) {
                if(i==0) {
                    firstelement=input;
                }
                input.classList.add("is-invalid");
                input.nextElementSibling.textContent = input.validationMessage; 
                i++;
            });
            if(firstelement) firstelement.focus(); 
            return false;
        } else {
            form.querySelectorAll(".is-invalid").forEach(function (input) {
                input.classList.remove("is-invalid");
                input.nextElementSibling.textContent = "";
            });
        }
        
        // Proceed with saving if no duplicates and form is valid
        const jsonDataString = JSON.stringify(jsonData);
        document.getElementById("detail_records").value = jsonDataString;
        const deletedDataString = JSON.stringify(deleteData);
        document.getElementById("deleted_records").value = deletedDataString;
        
        let transactionMode = document.getElementById("transactionmode").value;
        let message = "";
        let title = "";
        let icon = "success";

        if (transactionMode === "U") {
            message = "Record updated successfully!";
            title = "Update Successful!";
        } else {
            message = "Record added successfully!";
            title = "Save Successful!";
        }
        
        Swal.fire(title, message, icon).then((result) => {
            if (result.isConfirmed) {
                $("#masterForm").submit();
            }
        });
    });
});


});
</script>
<script>
$(document).ready(function () {
    const masterId = $('#item_preservation_price_list_id').val();
    const itemId = $('#item_id').val();
    
    if (masterId > 0 && itemId) {
         //$('#item_id').prop('disabled', true);
        fetchDetailRecords(itemId, masterId);
    } 

    $('#item_id').on('change', function () {
        const itemId = $(this).val();
        const masterId = $('#item_preservation_price_list_id').val() || 0;
        
        if (itemId) {
          checkMasterRecord(itemId, masterId);
        } else {
            resetForm();
        }
    });

    function checkMasterRecord(itemId, masterId) {
        $.ajax({
            url: 'classes/cls_item_preservation_price_list_master.php',
            type: 'POST',
            dataType: 'json',
            data: { 
                action: 'check_master_record',
                item_id: itemId,
                master_id: masterId
            },
            success: function(response) {
                if (response.exists) {
                    populateMasterForm(response.data);
                    fetchDetailRecords(itemId, response.data.item_preservation_price_list_id);
                } else {
                    resetMasterForm();
                    fetchDetailRecords(itemId, 0);
                }
            },
          
        });
    }

    function populateMasterForm(data) {
        $('#item_preservation_price_list_id').val(data.item_preservation_price_list_id);
        $('#rent_per_kg_month').val(data.rent_per_kg_month);
        $('#rent_per_kg_season').val(data.rent_per_kg_season);
        $('#transactionmode').val('U');
    }

    function resetMasterForm() {
        $('#item_preservation_price_list_id').val(0);
        $('#rent_per_kg_month').val('');
        $('#rent_per_kg_season').val('');
        $('#transactionmode').val('I'); 
    }

    function fetchDetailRecords(itemId, masterId) {
        $.ajax({
            url: 'classes/cls_item_preservation_price_list_detail.php',
            type: 'POST',
            data: { 
                action: 'fetch_units', 
                item_id: itemId,
                master_id: masterId
            },
            success: function(response) {
                $('#gridContainer').html(response);
        
                $('[data-field]').each(function() {
                    $(this).data('original', $(this).text().trim());
                });
           
                if (masterId > 0) {
                    const detailRecords = collectDetailRecords();
                    $('#detail_records').val(JSON.stringify(detailRecords));
                }
            },
            
        });
    }

    $('#btn_add').on('click', function (event) {
        event.preventDefault();

        const form = document.getElementById("masterForm");
        if (!form.checkValidity()) {
            event.stopPropagation();
            form.classList.add('was-validated');
            
            const firstInvalid = $(':invalid').first();
            if (firstInvalid.length) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
                firstInvalid.focus();
            }
            return;
        }

        const itemId = $('#item_id').val();
        const masterId = $('#item_preservation_price_list_id').val() || 0;
        
        if (!itemId) {
            Swal.fire('Error', 'Please select an item first', 'error');
            return;
        }
        
        const detailRecords = collectChangedDetails();

        $('<input>').attr({
            type: 'hidden',
            name: 'item_id',
            value: itemId
        }).appendTo('#masterForm');

        $('<input>').attr({
            type: 'hidden',
            name: 'item_preservation_price_list_id',
            value: masterId
        }).appendTo('#masterForm');

        $('#transactionmode').val(masterId > 0 ? 'U' : 'I');
        $('#detail_records').val(JSON.stringify(detailRecords));
        
        form.submit();
    });

    function collectChangedDetails() {
        const detailRecords = [];
        const gridRows = $('#gridContainer table tbody tr');

        gridRows.each(function() {
            const row = $(this);
            if (row.hasClass('norecords')) return true; 

            const packingUnitId = row.data('id');
            const detailId = row.data('detail-id') || null;
            const masterId = $('#item_preservation_price_list_id').val();

            const monthCell = row.find('[data-field="rent_per_qty_month"]');
            const seasonCell = row.find('[data-field="rent_per_qty_season"]');

            const rentPerQtyMonth = parseFloat(monthCell.text().trim()) || 0;
            const rentPerQtySeason = parseFloat(seasonCell.text().trim()) || 0;

            const originalMonth = parseFloat(monthCell.data('original')) || 0;
            const originalSeason = parseFloat(seasonCell.data('original')) || 0;

            if (rentPerQtyMonth !== originalMonth || 
                rentPerQtySeason !== originalSeason || 
                !detailId || 
                (rentPerQtyMonth > 0 || rentPerQtySeason > 0)) {
                detailRecords.push({
                    item_preservation_price_list_detail_id: detailId,
                    item_preservation_price_list_id: masterId,
                    packing_unit_id: packingUnitId,
                    rent_per_qty_month: rentPerQtyMonth,
                    rent_per_qty_season: rentPerQtySeason,
                    detailtransactionmode: detailId ? 'U' : 'I'
                });
            }
        });

        return detailRecords;
    }

    function resetForm() {
        resetMasterForm();
        $('#item_id').val('');
        $('#detail_records').val('[]');
      
    }
});
</script>
<?php
    include("include/footer_close.php");
?>