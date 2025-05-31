<?php
    include("classes/cls_inward_master.php");
    include("include/header.php");
    include("include/theme_styles.php");
    include("include/header_close.php");
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


$stmt = $_dbh->prepare("SELECT packing_unit_id, packing_unit_name FROM tbl_packing_unit_master WHERE status = 1");
$stmt->execute();
$packingUnits = $stmt->fetchAll(PDO::FETCH_ASSOC);

global $_dbh;
$next_inward_sequence = 1;
$inward_no_formatted = '';
$finYear = '';
try {
    $companyYearId = $_SESSION['sess_company_year_id'] ?? null;

    if ($companyYearId) {
        $stmt = $_dbh->prepare("
            SELECT 
                CONCAT(LPAD(YEAR(start_date) % 100, 2, '0'), '-', LPAD(YEAR(end_date) % 100, 2, '0')) AS short_range,
                start_date, end_date
            FROM tbl_company_year_master 
            WHERE company_year_id = ?
        ");
        $stmt->execute([$companyYearId]);
        $yearRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($yearRow) {
            $finYear = $yearRow['short_range'];
            $startDate = $yearRow['start_date'];
            $endDate = $yearRow['end_date'];
            $stmt2 = $_dbh->prepare("
                SELECT MAX(inward_sequence) AS max_seq
                FROM tbl_inward_master 
                WHERE inward_date BETWEEN ? AND ?
            ");
            $stmt2->execute([$startDate, $endDate]);
            $seqRow = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($seqRow && is_numeric($seqRow['max_seq'])) {
                $next_inward_sequence = $seqRow['max_seq'] + 1;
            }
            $sequence_padded = str_pad($next_inward_sequence, 4, '0', STR_PAD_LEFT);
            $inward_no_formatted = $sequence_padded . '/' . $finYear;
        }
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
        
    }
$stmt = $_dbh->prepare("SELECT id, value, Lable FROM view_storage_duration");
$stmt->execute();
$durations = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        <li><a href="srh_inward_master.php"><i class="fa fa-dashboard"></i> Inward Master</a></li>
          <li class="active"><?php echo $label; ?></li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_inward_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
                <div class="form-group row gy-2">
    <?php
            global $database_name;
            global $_dbh;
            $hidden_str="";
            $table_name="tbl_inward_master";
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

                         $old_table_layout=$table_layout;
                        if($table_layout=="horizontal") {
                            $label_layout_classes="col-4 col-sm-2 col-md-1 col-lg-1 control-label";
                            $field_layout_classes="col-8 col-sm-4 col-md-3 col-lg-2";
                        } else {
                            $label_layout_classes="col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                            $field_layout_classes="col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                        }
                        
                        if(is_array($fields_names) && !empty($fields_names)) {
                            for($i=0;$i<count($fields_names);$i++) {
                               if($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") {
                                    $table_layout="horizontal";
                                } else{
                                    $table_layout=$old_table_layout;
                                } 
                               $table_layout = ($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") ? "horizontal" : $old_table_layout;
                                if ($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") {
                                    if ($fields_names[$i] == "inward_sequence") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-1 mt-3";
                                    }
                                    else if ($fields_names[$i] == "inward_no") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-4 col-md-2 col-lg-2 col-xxl-1 mt-3";
                                    }
                                } else {
                                    if ($table_layout == "horizontal") {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-8 col-md-9 col-lg-10";
                                    } else {
                                        $label_layout_classes = "col-12 col-sm-3 col-md-2 col-lg-2 col-xl-2 col-xxl-1 col-form-label";
                                        $field_layout_classes = "col-12 col-sm-6 col-md-4 col-lg-4 col-xl-3 col-xxl-2";
                                    }
                                }
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
                                                 if (($fields_names[$i] == "inward_sequence" || $fields_names[$i] == "inward_no") && $transactionmode != "U") {
                                                if ($fields_names[$i] == "inward_sequence") {
                                                    $value = $next_inward_sequence;
                                                } else {
                                                    $value = $inward_no_formatted;
                                                }
                                                $readonly_str = "readonly";
                                            } else {
                                                $value = isset($_bll->_mdl) ? $_bll->_mdl->$cls_field_name : "";
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
                                  if(!empty($value) && ($fields_types[$i]=="date" || $fields_types[$i]=="datetime-local" || $fields_types[$i]=="date         time" || $fields_types[$i]=="timestamp")) {
                                                $value=date("Y-m-d",strtotime($value));
                                         }
                                    if ($fields_names[$i] == 'inward_date' && empty($value)) {
                                        $value = '';  
                                    }
                                    if ($fields_names[$i] == 'billing_starts_from' && empty($value)) {
                                        $value = ''; 
                                    }
                                        if ($fields_names[$i] == 'inward_date') {
                                            $error_container = '<div id="inward_date_error" class="invalid-feedback"></div>';
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
                                    }
                                    echo $lbl_str;
                                    if($field_str) {
                                        $extra_margin_class = ($fields_names[$i] == 'inward_date') ? ' mt-3' : '';
                                    ?>
                                    <div class="<?php echo $field_layout_classes." ".$cls_err.$extra_margin_class; ?>">
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
                    <div class="box-detail">
                        <?php
                            if(isset($_blldetail))
                                $_blldetail->pageSearch(); 
                        ?>
                        <button type="button" name="detailBtn" id="detailBtn" class="btn btn-primary add" data-bs-toggle="modal" data-bs-target="#modalDialog"  onclick="openModal()">Add Detail Record</button>
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
        for ($j = 0; $j < count($field_array); $j++) {
            echo $lbl_array[$j];
            if ($field_array[$j]) {
    ?>
            <div class="col-8 col-sm-4 col-md-3 col-lg-2 <?php echo $clserr_array[$j]; ?>">
                <?php
                    echo $field_array[$j];
                    echo $err_array[$j];
                ?>
            </div>
    <?php
            }
        }
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
                <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date("Y-m-d H:i:s"); ?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                                        <input type="hidden" id="deleted_records" name="deleted_records" />
                    <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_inward_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
                <input type="button" class="btn btn-dark" id="btn_cancel" name="btn_cancel" value="Cancel"  onclick="window.location=window.history.back();">
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
<!-- Modal -->
<div class="detail-modal">
    <div id="modalDialog" class="modal" tabindex="-1" aria-hidden="true" aria-labelledby="modalToggleLabel">
      <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
        <form id="popupForm" method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
          <div class="modal-header">
              <h4 class="modal-title" id="modalToggleLabel">Add Inward Details</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="box-body container-fluid">     
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-12 col-lg-6">
                        <div class="row mb-2">
                            <label for="lot_no" class="col-12 col-sm-4 control-label">Lot No *</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="lot_no" name="lot_no" placeholder="Enter Lot No" required />
                                <div class="invalid-feedback">Please enter the Lot No.</div>
                            </div>
                        </div>
                        
                        <!-- Item Dropdown -->
                        <div class="row mb-2">
                            <label for="item_name" class="col-12 col-sm-4 control-label">Item *</label>
                            <div class="col-12 col-sm-8">
                                <select class="form-select" id="item_name" name="item_name" required>
                                    <option value="">Select Item</option>
                                    <?php 
                                    $items = $_dbh->query("SELECT item_id, item_name, item_gst FROM tbl_item_master")->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($items as $item): ?>
                                        <option value="<?= htmlspecialchars($item['item_id']) ?>" 
                                                data-gst="<?= htmlspecialchars($item['item_gst']) ?>">
                                            <?= htmlspecialchars($item['item_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select an item.</div>
                            </div>
                        </div>
                        
                        <!-- GST Type Radio Buttons -->
                        <div class="row mb-2">
                            <label class="col-12 col-sm-4 control-label">GST Type</label>
                            <div class="col-12 col-sm-8 pt-2 d-flex">
                                <div class="form-check form-check-inline">
                                    <label class="form-check-label" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="item_gst" value="1" checked
                                               style="width: 16px; height: 16px; border-radius: 50%; margin-right: 10px;">
                                        GST Applicable
                                    </label>
                                </div>
                                <div class="form-check form-check-inline" style="margin-left: 20px;">
                                    <label class="form-check-label" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="item_gst" value="2"
                                               style="width: 16px; height: 16px; border-radius: 50%; margin-right: 10px;">
                                        GST Exempted
                                    </label>
                                </div>
                                <div class="form-check form-check-inline" style="margin-left: 20px;">
                                    <label class="form-check-label" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="item_gst" value="3"
                                               style="width: 16px; height: 16px; border-radius: 50%; margin-right: 10px;">
                                        GST Not Applicable
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="variety" class="col-12 col-sm-4 control-label">Variety</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="variety" name="variety" placeholder="Enter Variety">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="packing_unit_name" class="col-12 col-sm-4 control-label">Packing Unit *</label>
                            <div class="col-12 col-sm-8">
                                <select class="form-select" id="packing_unit_name" name="packing_unit_name" required>
                                    <option value="">Select Unit</option>
                                    <?php 
                                    foreach ($packingUnits as $unit): ?>
                                        <option value="<?php echo htmlspecialchars($unit['packing_unit_id']) ?>">
                                            <?php echo htmlspecialchars($unit['packing_unit_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a packing unit.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="inward_qty" class="col-12 col-sm-4 control-label">Inward Qty *</label>
                            <div class="col-12 col-sm-8">
                                <input type="number" class="form-control" id="inward_qty" name="inward_qty" placeholder="Enter Inward Qty" required>
                                <div class="invalid-feedback">Please enter the inward quantity.</div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="inward_wt" class="col-12 col-sm-4 control-label">Inward Wt</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="inward_wt" name="inward_wt" placeholder="Enter Inward Wt">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="avg_wt_per_bag" class="col-12 col-sm-4 control-label">Avg Wt Per Bag</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="avg_wt_per_bag" name="avg_wt_per_bag" placeholder="Enter Avg Wt Per Bag">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label class="col-12 col-sm-4 control-label">Location *</label>
                            <div class="col-12 col-sm-8">
                                <div class="row g-0">
                                    <!-- Chamber Dropdown -->
                                    <div class="col-4">
                                        <select class="form-select rounded-0" id="chamber" name="chamber" required>
                                            <option value="">Chamber</option>
                                            <?php 
                                            $chambers = $_dbh->query("SELECT chamber_id, chamber_name FROM tbl_chamber_master")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($chambers as $chamber): ?>
                                                <option value="<?= htmlspecialchars($chamber['chamber_id']) ?>">
                                                    <?= htmlspecialchars($chamber['chamber_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a chamber.</div>
                                    </div>
                                    
                                    <!-- Floor Dropdown -->
                                    <div class="col-4">
                                        <select class="form-select rounded-0" id="floor" name="floor" required>
                                            <option value="">Floor</option>
                                            <?php 
                                            $floors = $_dbh->query("SELECT floor_id, floor_name FROM tbl_floor_master")->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($floors as $floor): ?>
                                                <option value="<?= htmlspecialchars($floor['floor_id']) ?>">
                                                    <?= htmlspecialchars($floor['floor_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback">Please select a floor.</div>
                                    </div>
                                    
                                    <!-- Rack Input -->
                                    <div class="col-4">
                                        <input type="text" class="form-control rounded-0" id="rack" name="rack" placeholder="Rack">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="location" class="col-12 col-sm-4 control-label">Location</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="location" name="location" placeholder="Location" disabled>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="moisture" class="col-12 col-sm-4 control-label">Moisture</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="moisture" name="moisture" placeholder="Enter Moisture">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column -->
                    <div class="col-12 col-lg-6">
                        <!-- STORAGE DURATION DROPDOWN -->
                        <div class="row mb-2">
                            <label class="col-12 col-sm-4 control-label">Storage Duration *</label>
                            <div class="col-12 col-sm-8">
                                <div class="row g-0">
                                    <div class="col-6 pe-1">
                                    <select class="form-select rounded-0" id="storageDuration" name="storageDuration" required>
                                        <option value="">Select Duration</option>
                                        <?php foreach ($durations as $duration): ?>
                                            <option value="<?= htmlspecialchars($duration['id']) ?>"
                                                data-value="<?= htmlspecialchars($duration['value']) ?>"
                                                data-label="<?= htmlspecialchars($duration['Lable']) ?>"
                                                <?= $duration['value'] === 'Seasonal' ? 'data-is-seasonal="true"' : '' ?>>
                                                <?= htmlspecialchars($duration['value']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select a storage duration.</div>
                                </div>
                                    
                                    <!-- Rent Per Dropdown -->
                                    <div class="col-6 ps-1">
                                        <select class="form-select rounded-0 seasonal-required" id="rentPer" name="rentPer" required>
                                            <option value="">Rent Per</option>
                                            <option value="Quantity">Total Quantity</option>
                                            <option value="Kg">Total KG</option>
                                        </select>
                                        <div class="invalid-feedback">Please select rent type.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEASONAL FIELDS SECTION -->
                        <div id="seasonalFields" style="display: none;">
                            <div class="row mb-2 justify-content-end align-items-center">
                                <div class="col-sm-4 text-end" style="padding-right: 110px;">
                                    <label for="startDate" class="control-label">Start Date*</label>
                                </div>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control seasonal-required" id="startDate" name="startDate">
                                    <div class="invalid-feedback">Please select a start date.</div>
                                </div>
                                <div class="col-sm-2 text-end">
                                    <label for="endDate" class="control-label">End Date *</label>
                                </div>
                                <div class="col-sm-3">
                                    <input type="date" class="form-control seasonal-required" id="endDate" name="endDate">
                                    <div class="invalid-feedback">Please select an end date.</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-2" id="rentPerMonthRow">
                            <label for="rentPerMonth" class="col-12 col-sm-4 control-label">Rent / Month</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="rentPerMonth" name="rentPerMonth" placeholder="Enter Rent / Month">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="rentStorageDuration" id="rentLabel" class="col-12 col-sm-4 control-label">Rent / Storage Duration</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="rentStorageDuration" name="rentStorageDuration" placeholder="Enter Rent / Storage Duration" disabled>
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="unloading_charge" class="col-12 col-sm-4 control-label">Unloading Charge</label>
                            <div class="col-12 col-sm-8">
                                <input type="text" class="form-control" id="unloading_charge" name="unloading_charge" placeholder="Enter Unloading Charge">
                            </div>
                        </div>
                        
                        <div class="row mb-2">
                            <label for="remark" class="col-12 col-sm-4 control-label">Remark</label>
                            <div class="col-12 col-sm-8">
                                <textarea class="form-control" id="remark" name="remark" rows="2" placeholder="Enter Remark"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <input type="hidden" id="inward_detail_id" name="inward_detail_id" value="0">
            <input type="hidden" id="detailtransactionmode" name="detailtransactionmode" value="I">
            <input class="btn btn-success" type="submit" id="detailbtn_add" name="detailbtn_add" value="Save">
            <input class="btn btn-dark" type="button" id="detailbtn_cancel" name="detailbtn_cancel" value="Cancel" data-bs-dismiss="modal">
          </div>
        </form>
        </div>
      </div>
    </div>
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
const financialYear = "<?php echo $finYear; ?>";
document.addEventListener("DOMContentLoaded", function () {    
    let jsonData = [];
    let editIndex = -1;
    let deleteData = [];
    let detailIdLabel = "inward_detail_id";
    
    const tableBody = document.getElementById("tableBody");
    const form = document.getElementById("popupForm");
    const modalDialog = document.getElementById("modalDialog");
    const modal = new bootstrap.Modal(modalDialog);
    
    document.querySelectorAll("#searchDetail tbody tr").forEach(row => {
        if(!row.classList.contains("norecords")) {
            let rowData = {};
            rowData[row.dataset.label] = row.dataset.id;
            
            row.querySelectorAll("td[data-label]").forEach(td => {
                if(!td.classList.contains("actions")) {
                    rowData[td.dataset.label] = td.innerText;
                }
            });
            
            const hiddenFields = ['inward_detail_id', 'sesonal_start_date', 'seasonal_end_date', 
                                'seasonal_rent', 'seasonal_rent_per', 'unloading_charge', 'remark'];
            
            hiddenFields.forEach(field => {
                const hiddenCell = row.querySelector(`td[data-label="${field}"]`);
                if(hiddenCell) {
                    rowData[field] = hiddenCell.innerText;
                }
            });
            
            rowData["detailtransactionmode"] = "U";
            jsonData.push(rowData);
        }
    });
    modalDialog.addEventListener("hidden.bs.modal", function () {
        clearForm(form);
        editIndex = -1;
        document.getElementById("detailtransactionmode").value = "I";
    });
    window.openModal = function(index = -1) {
        if (index >= 0) {
            editIndex = index;
            const data = jsonData[index];
            
            // Populate form fields
            for (let key in data) {
                const input = form.elements[key];
                if (input) {
                    if (input.type === "radio") {
                        // Handle radio buttons
                        const radio = form.querySelector(`input[name="${key}"][value="${data[key]}"]`);
                        if (radio) radio.checked = true;
                    } else if (input.type !== "hidden") {
                        input.value = data[key];
                    }
                }
            }
            document.getElementById("detailtransactionmode").value = "U";
        } else {
            clearForm(form);
            document.getElementById("detailtransactionmode").value = "I";
        }
        modal.show();
        setTimeout(() => {
            const firstInput = form.querySelector("input:not([type=hidden]), select, textarea");
            if (firstInput) firstInput.focus();
        }, 100);
    } 
    // Save data handler
    function saveData() {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            let firstInvalid = null;
            form.querySelectorAll(":invalid").forEach(input => {
                input.classList.add("is-invalid");
                if (!firstInvalid) firstInvalid = input;
            });
            if (firstInvalid) firstInvalid.focus();
            return false;
        }
        const formData = new FormData(form);
        const newEntry = {};
        // Convert form data to object
        for (const [key, value] of formData.entries()) {
            newEntry[key] = value;
        }
        if (editIndex >= 0) {
            jsonData[editIndex] = newEntry;
            updateTableRow(editIndex, newEntry);
            showSuccessAlert("Updated Successfully", "The record has been updated successfully!");
        } else {
            newEntry["detailtransactionmode"] = "I";
            jsonData.push(newEntry);
            appendTableRow(newEntry, jsonData.length - 1);
            showSuccessAlert("Added Successfully", "The record has been added successfully!");
        }
        modal.hide();
        return false;
    }
    // Append new row to table
    function appendTableRow(rowData, index) {
        const row = document.createElement("tr");
        row.setAttribute("data-id", rowData[detailIdLabel] || "");
        row.setAttribute("data-label", detailIdLabel);  
        addActions(row, index, rowData[detailIdLabel] || "");
        const displayFields = [
            'lot_no', 'item_name', 'gst_type', 'variety', 'packing_unit_name', 
            'inward_qty', 'inward_wt', 'avg_wt_per_bag', 'location', 'moisture',
            'storage_duration', 'rent_per_day', 'rent_per_storage_duration'
        ]; 
        displayFields.forEach(field => {
            const cell = document.createElement("td");
            cell.setAttribute("data-label", field);
            cell.textContent = rowData[field] || "";
            row.appendChild(cell);
        });
        const noRecordsRow = document.getElementById("norecords");
        if (noRecordsRow) {
            noRecordsRow.remove();
        }
        tableBody.appendChild(row);
    }
    // Update existing table row
    function updateTableRow(index, rowData) {
        const row = tableBody.children[index];
        row.innerHTML = "";
        addActions(row, index, rowData[detailIdLabel] || "");
        const displayFields = [
            'lot_no', 'item_name', 'gst_type', 'variety', 'packing_unit_name', 
            'inward_qty', 'inward_wt', 'avg_wt_per_bag', 'location', 'moisture',
            'storage_duration', 'rent_per_day', 'rent_per_storage_duration'
        ];
        displayFields.forEach(field => {
            const cell = document.createElement("td");
            cell.setAttribute("data-label", field);
            cell.textContent = rowData[field] || "";
            row.appendChild(cell);
        });
    }
    // Add action buttons to row
    function addActions(row, index, id) {
        const actionCell = document.createElement("td");
        actionCell.classList.add("actions");
        
        const editButton = document.createElement("button");
        editButton.textContent = "Edit";
        editButton.classList.add("btn", "btn-info", "btn-sm", "me-2", "edit-btn");
        editButton.setAttribute("data-index", index);
        editButton.setAttribute("data-id", id);
        
        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.classList.add("btn", "btn-danger", "btn-sm", "delete-btn");
        deleteButton.setAttribute("data-index", index);
        deleteButton.setAttribute("data-id", id);
        
        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);
        row.appendChild(actionCell);
    }
    // Delete row handler
    function deleteRow(index, id) {
        Swal.fire({
            title: "Are you sure you want to delete this record?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                if (id && id !== "0") {
                    // Mark for deletion if it's an existing record
                    const deletedRecord = jsonData[index];
                    deletedRecord.detailtransactionmode = "D";
                    deleteData.push(deletedRecord);
                }
                
                // Remove from data array
                jsonData.splice(index, 1);
                
                // Rebuild table
                rebuildTable();
                
                Swal.fire("Deleted!", "The record has been deleted.", "success");
            }
        });
    }
    // Rebuild entire table
    function rebuildTable() {
        tableBody.innerHTML = "";
        if (jsonData.length === 0) {
            const noRecordsRow = document.createElement("tr");
            noRecordsRow.id = "norecords";
            noRecordsRow.classList.add("norecords");
            const noRecordsCell = document.createElement("td");
            noRecordsCell.colSpan = 14; 
            noRecordsCell.textContent = "No records available";
            noRecordsRow.appendChild(noRecordsCell);
            tableBody.appendChild(noRecordsRow);
        } else {
            jsonData.forEach((data, index) => {
                appendTableRow(data, index);
            });
        }
    }
    // Clear form
    function clearForm(form) {
        form.reset();
        form.querySelectorAll(".is-invalid").forEach(input => {
            input.classList.remove("is-invalid");
        });
        form.querySelectorAll(".invalid-feedback").forEach(el => {
            el.textContent = "";
        });
    }
    // Show success alert
    function showSuccessAlert(title, text) {
        Swal.fire({
            icon: "success",
            title: title,
            text: text,
            showConfirmButton: true,
            showClass: { popup: "" },
            hideClass: { popup: "" }
        });
    }
    // Event delegation for edit/delete buttons
    document.addEventListener("click", function(event) {
        if (event.target.classList.contains("edit-btn")) {
            event.preventDefault();
            const index = event.target.getAttribute("data-index");
            openModal(index);
        }
        if (event.target.classList.contains("delete-btn")) {
            event.preventDefault();
            const index = event.target.getAttribute("data-index");
            const id = event.target.getAttribute("data-id");
            deleteRow(index, id);
        }
    });
    // Form submission handler
    form.addEventListener("submit", function(event) {
        event.preventDefault();
        saveData();
    });
    // Save master form handler
    document.getElementById("btn_add").addEventListener("click", function(event) {
        // Convert data to JSON and set in hidden fields
        document.getElementById("detail_records").value = JSON.stringify(jsonData);
        document.getElementById("deleted_records").value = JSON.stringify(deleteData);
        
        // Submit the form (assuming validation is handled elsewhere)
         document.getElementById("masterForm").submit();
    });
    // Additional field interactions
    if (document.getElementById("inward_wt")) {
        document.getElementById("inward_wt").addEventListener("blur", function() {
            if (this.value.trim() !== '') {
                document.getElementById("location").value = this.value;
            }
        });
    }
    //gross-tare
    var grossInput = document.getElementById('gross_wt');
    var tareInput = document.getElementById('tare_wt');
    var netInput = document.getElementById('net_wt');
    var netSpan = document.getElementById('display_net_wt');
    function updateNetWt() {
        var gross = parseFloat(grossInput && grossInput.value) || 0;
        var tare = parseFloat(tareInput && tareInput.value) || 0;
        var net = gross - tare;
        if(net < 0) net = 0;
        if(netInput) netInput.value = net.toFixed(3);
        if(netSpan) netSpan.textContent = net.toFixed(3) + " Kg";
    }
    if (grossInput && tareInput && netInput) {
        grossInput.addEventListener('input', updateNetWt);
        tareInput.addEventListener('input', updateNetWt);
        updateNetWt();
        netInput.readOnly = true;
    }
    //unit 
    const inwardQtyInput = document.getElementById("inward_qty");
    const inwardWeightInput = document.getElementById("inward_wt");
    const packingUnitDropdown = document.getElementById("packing_unit_name");
    const avgWtPerBagInput = document.getElementById("avg_wt_per_bag");
    const totalQtyInput = document.getElementById("total_qty");
    const totalWeightInput = document.getElementById("total_wt");

    let conversionFactor = 0;

    function updateInwardWeight() {
        const qty = parseFloat(inwardQtyInput.value);
        if (!isNaN(qty) && !isNaN(conversionFactor)) {
            const calculatedWeight = (qty * conversionFactor).toFixed(2);
            inwardWeightInput.value = calculatedWeight;
            updateTotalFields(qty, parseFloat(calculatedWeight));
        } else {
            inwardWeightInput.value = "";
            updateTotalFields(null, null);
        }
    }
    function updateTotalFields(inwardQty, inwardWt) {
        totalQtyInput.value = !isNaN(inwardQty) ? inwardQty : "";
        totalWeightInput.value = !isNaN(inwardWt) ? inwardWt : "";
    }
    if (packingUnitDropdown) {
        packingUnitDropdown.addEventListener("change", function () {
            const selectedPackingUnit = packingUnitDropdown.value;
            if (selectedPackingUnit) {
                fetch("classes/cls_inward_master.php?action=fetchConversionFactor&packing_unit=" + encodeURIComponent(selectedPackingUnit))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            conversionFactor = parseFloat(data.conversion_factor);
                            avgWtPerBagInput.value = conversionFactor;
                            updateInwardWeight();
                        } else {
                            avgWtPerBagInput.value = "";
                            conversionFactor = 0;
                            console.warn("Fetch warning:", data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Error fetching conversion factor:", error);
                    });
            } else {
                avgWtPerBagInput.value = "";
                conversionFactor = 0;
                updateInwardWeight();
            }
        });
    }
    if (inwardQtyInput) {
        inwardQtyInput.addEventListener("input", updateInwardWeight);
    }
    //Inward no and inward sequence
    const inwardSequenceInput = document.getElementById("inward_sequence");
    const inwardNoInput = document.getElementById("inward_no");
    if (inwardSequenceInput && inwardNoInput) {
        inwardSequenceInput.addEventListener("input", function () {
            const sequence = this.value.padStart(4, '0');
            inwardNoInput.value = sequence + '/' + financialYear;
        });
    }
     // daily,weekly,fortnightly,monthly not disabled fields - drashti
    var storageDuration = document.getElementById('storageDuration');
  var rentPerMonthRow = document.getElementById('rentPerMonthRow');
  var rentStorageDuration = document.getElementById('rentStorageDuration');

  function toggleFields() {
    var selectedOption = storageDuration.options[storageDuration.selectedIndex];
    var value = selectedOption.getAttribute('data-value') || selectedOption.value;
    var targetValues = ["daily","weekly","fortnightly","monthly"];
    if (targetValues.includes(value.toLowerCase())) {
      rentPerMonthRow.style.display = 'none';
      rentStorageDuration.disabled = false;
    } else {
      rentPerMonthRow.style.display = '';
      rentStorageDuration.disabled = true;
    }
  }
  storageDuration.addEventListener('change', toggleFields);
  toggleFields(); // Initial check
    
 // rent/month hidden fields storage duration - drashti
    var storageDuration = document.getElementById('storageDuration');
  var rentPerMonthRow = document.getElementById('rentPerMonthRow');

  function toggleRentPerMonth() {
    var selectedOption = storageDuration.options[storageDuration.selectedIndex];
    var value = selectedOption.getAttribute('data-value') || selectedOption.value;
    var hideValues = ["daily","weekly","fortnightly","monthly"];
    if (hideValues.includes(value.toLowerCase())) {
      rentPerMonthRow.style.display = 'none';
    } else {
      rentPerMonthRow.style.display = '';
    }
  }
  storageDuration.addEventListener('change', toggleRentPerMonth);
  toggleRentPerMonth(); // Initial check
});
    
    // storage duration lable 
    document.getElementById('storageDuration').addEventListener('change', function() {
  var selectedOption = this.options[this.selectedIndex];
  var valueLabel = selectedOption.getAttribute('data-label');
  var isSeasonal = selectedOption.getAttribute('data-is-seasonal');
  var rentLabel = document.getElementById('rentLabel');
  var seasonalFields = document.getElementById('seasonalFields');

  if (valueLabel) {
    rentLabel.textContent = 'Rent / ' + valueLabel;
  } else {
    rentLabel.textContent = 'Rent / Storage Duration';
  }
        
  if (isSeasonal === "true") {
    seasonalFields.style.display = "block";
  } else {
    seasonalFields.style.display = "none";
  }
});
</script> 
<!--Billing_date/ Inward_date-->
<script>
$(document).ready(function () {
    if ($('#inward_date').val() === '') {
        var today = new Date();
        var formattedToday = today.toISOString().split('T')[0];
        $('#inward_date').val(formattedToday);
        $('#billing_starts_from').val(formattedToday);
    }
    function validateInwardDate() {
        var inwardDate = $('#inward_date').val();
        var errorContainer = $('#inward_date_error');
        if (inwardDate === '') return false;
        var inwardDateParts = inwardDate.split('-');
        if (inwardDateParts.length !== 3) {
            showError('Enter Proper Inward Date');
            return false;
        }
        var year = parseInt(inwardDateParts[0], 10);
        var month = parseInt(inwardDateParts[1], 10);
        var day = parseInt(inwardDateParts[2], 10);
        var validDate = new Date(year, month - 1, day);
        if (
            validDate.getFullYear() !== year ||
            validDate.getMonth() !== (month - 1) ||
            validDate.getDate() !== day
        ) {
            showError('Enter Proper Inward Date');
            return false;
        }
        var currentDate = new Date();
        var todayStr = currentDate.toISOString().split('T')[0];
        var selectedDate = new Date(inwardDate);
        var today = new Date(todayStr);
        selectedDate.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);
        if (selectedDate > today) {
            showError('Date Above Current Period');
            return false;
        }
        var currentMonth = today.getMonth();
        var currentYear = today.getFullYear();
        var prevMonth = currentMonth - 1;
        var prevYear = currentYear;
        if (prevMonth < 0) {
            prevMonth = 11;
            prevYear -= 1;
        }
        var selectedMonth = selectedDate.getMonth();
        var selectedYear = selectedDate.getFullYear();
        if (
            selectedYear < prevYear ||
            (selectedYear === prevYear && selectedMonth < prevMonth)
        ) {
            showError('Date Below Current Period');
            return false;
        }
        $('#billing_starts_from').val(inwardDate);
        return true;

        function showError(message) {
            errorContainer.text(message);
            $('#inward_date').addClass('is-invalid');
        }
    }
    $('#billing_starts_from').one('focus', function () {
        if ($(this).val() === '') {
            var today = new Date();
            var formattedToday = today.toISOString().split('T')[0];
            $(this).val(formattedToday);
        }
    });
    $('#inward_date').on('blur', function () {
        validateInwardDate();
    });
});
</script> 
<!--calculation Rent -->
<script>
function getStorageDurationMultiplier(storageValue) {
    switch(storageValue) {
        case 'Daily':
            return 1/30; 
        case 'Weekly':
            return 7/30;
        case 'Fortnightly':
        case '15 Days':
            return 15/30;
        case 'Monthly':
        case 'Month':
            return 1;
        case '1 Month 1 Day':
            return 1 + (1/30);
        case '1 Month 7 Days':
            return 1 + (7/30);
        case '1 Month 15 Days':
            return 1 + (15/30);
        case '2 Months':
            return 2;
        // Add more as needed
        default:
            return 1;
    }
}
function calculateRentStorageDuration() {
    const storageDurationValue = $('#popupForm #storageDuration option:selected').text().trim();
    let rentPerMonth = parseFloat($('#popupForm #rentPerMonth').val());
    if (isNaN(rentPerMonth)) rentPerMonth = 0;

    let multiplier = getStorageDurationMultiplier(storageDurationValue);

    let rentStorageDuration = 0;
    if (rentPerMonth > 0 && multiplier > 0) {
        rentStorageDuration = (rentPerMonth * multiplier).toFixed(2);
    }
    $('#popupForm #rentStorageDuration').val(rentStorageDuration);
}
$('#popupForm #storageDuration, #popupForm #rentPerMonth').on('change keyup', calculateRentStorageDuration);
$('#popupForm #item_name, #popupForm #packing_unit_name, #popupForm #rentPer').on('change', function () {
    const itemId   = $('#popupForm #item_name').val();
    const unitId   = $('#popupForm #packing_unit_name').val();
    const rentPer  = $('#popupForm #rentPer').val();
    const customerId = $('#customer').val();

    if (customerId && itemId && rentPer) {
        $.ajax({
            url: 'classes/cls_inward_master.php',
            method: 'GET',
            data: {
                action: 'fetchRentPerMonth',
                customer_id: customerId,
                item_id: itemId,
                unit_id: unitId,
                rent_per: rentPer
            },
            success: function (response) {
                if (typeof response === "string") {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        response = {};
                    }
                }
                if (response.success) {
                    $('#popupForm #rentPerMonth').val(response.rent_per_month || '');
                } else {
                    $('#popupForm #rentPerMonth').val('');
                }
                // Calculate for new rent value
                calculateRentStorageDuration();
            }
        });
    }
});
</script>        
<script>
  // seasonal validation - drashti
 document.addEventListener("DOMContentLoaded", function () {
    const storageDuration = document.getElementById("storageDuration");
    const seasonalFields = document.getElementById("seasonalFields");
    const seasonalInputs = seasonalFields.querySelectorAll(".seasonal-required");

    function checkSeasonal() {
      const selectedOption = storageDuration.options[storageDuration.selectedIndex];
      const isSeasonal = selectedOption.getAttribute("data-is-seasonal") === "true";

      if (isSeasonal) {
        seasonalFields.style.display = "block";
        seasonalInputs.forEach(input => input.setAttribute("required", "required"));
      } else {
        seasonalFields.style.display = "none";
        seasonalInputs.forEach(input => input.removeAttribute("required"));
      }
    }
    checkSeasonal();
    storageDuration.addEventListener("change", checkSeasonal);
  });  
      //location-drashti
  document.getElementById('chamber').addEventListener('change', updateLocation);
  document.getElementById('floor').addEventListener('change', updateLocation);
  document.getElementById('rack').addEventListener('input', updateLocation);

  function updateLocation() {
    const chamber = document.getElementById('chamber').selectedOptions.length > 0 
      ? document.getElementById('chamber').selectedOptions[0].text.trim() 
      : '';
    const floor = document.getElementById('floor').selectedOptions.length > 0 
      ? document.getElementById('floor').selectedOptions[0].text.trim() 
      : '';
    const rack = document.getElementById('rack').value.trim() || '';

    const parts = [chamber, floor, rack].filter(part => part !== '');
    const location = parts.join(' - ');
    
    document.getElementById('location').value = location;
  } 
</script>
<?php
    include("include/footer_close.php");
?>