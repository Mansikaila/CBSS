<?php
    include("classes/cls_user_right_master.php");
    include("include/header.php");
?>
 <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/select2.min.css">
<?php
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
try {
    $stmtusers = $_dbh->prepare("SELECT user_id, login_id FROM tbl_user_master ORDER BY login_id ASC");
    $stmtusers->execute();
    $users = $stmtusers->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading users: " . $e->getMessage();
}
try {
    $stmtmenus = $_dbh->prepare("SELECT menu_right_id, right_name FROM tbl_menu_right_master ORDER BY right_name ASC");
    $stmtmenus->execute();
    $menus = $stmtmenus->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading menus: " . $e->getMessage();
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
          Edit User Right
        </h1>
      </section>
    <section class="content">
    <div class="col-md-12" style="padding:0;">
       <div class="box box-info">
            <!-- form start -->
            <form id="masterForm" action="classes/cls_user_right_master.php"  method="post" class="form-horizontal needs-validation" enctype="multipart/form-data" novalidate>
            <div class="box-body">
               <div class="form-group">
                    <!-- User Dropdown -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-sm-2 col-form-label">User ID*</label>
                        <div class="col-sm-3">
                            <select class="form-select" name="user_id" id="user_id" required onchange="loadUserRights(this.value)">
                                <option value="">Select User</option>
                                <?php
                                foreach ($users as $user) {
                                    echo "<option value='{$user['user_id']}'>" . htmlspecialchars($user['login_id']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-sm-2 col-form-label">Module Type*</label>
                        <div class="col-sm-3">
                            <select class="form-select" onchange="showMenus(this.value)">
                                <option value="">Select Module Type</option>
                                <option value="Master">Master</option>
                                <option value="Transaction">Transaction</option>
                                <option value="Utility">Utility</option>
                                <option value="Report">Report</option>
                                <option value="Account">Account</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                    </div>
                </div>
              </div>
           <div class="row">
    <div class="col-md-12 px-4">
        <div id="menuContainer"></div>
    </div>
</div>
   
<script>
function showMenus(module) {
    let menuContainer = document.getElementById('menuContainer');
    let menus = {
        'Master': [
            'Country',
            'State',
            'City',
            'Currency',
            'Bank ',
            'Customer Account Group',
            'Customer',
            'Packing Unit',
            'Item',
            'Chamber',
            'Floor',
            'Item Preservation Price List',
            'Customer wise Item Preservation Price List',
            'HSN Code'
        ],
        'Transaction': [
            'Inward',
            'Outward',
            'Invoice'
        ],
        'Utility': [
            'Generate Rent Invoice',
            'Multi Invoice Print',
            'Inward Lock / Unlock',
            'Change Location'
        ],
        'Report': [
            'Inward Summary',
            'Outward Summary',
            'Invoice Summary',
            'Invoice GST Summary',
            'Inward Outward Summary',
            'Rent Valuation ',
            'Party wise Inward Balance',
            'Item Stock',
            'Item Stock Statement',
            'Lot Statement',
            'Lot Transfer History',
            'Location Detail View',
            'Item Preservation Charges List',
            'Yearly Stock Report',
            'Location Change History'
        ],
        'Account': [
            'Receipt',
            'Payment',
            'Contra',
            'Journal',
            'Day Book',
            'Account Ledger',
            'Net Payable Outstanding',
            'Net Receivable Outstanding'
        ],
        'Admin': [
            'Company',
            'Company Year',
            'Menu',
            'Module',
            'User Master',
            'User Right'
        ]
    };

     let html = `<h5 class="mb-3">${module}</h5><ul class="list-group">`;
    menus[module].forEach(menu => {
        html += `
            <li class="list-group-item d-flex align-items-center">
                <span style="width: 150px; background-color: #367fa9; color: white; padding: 4px 8px; border-radius: 4px;">${menu}</span>

                <label class="me-4 mb-0 d-flex align-items-center" style="width: 90px; padding-left: 25px;">
                    View <input type="checkbox" name="rights[${menu}][view]" value="view" class="ms-2">
                </label>
                <label class="me-4 mb-0 d-flex align-items-center" style="width: 80px;">
                    Add <input type="checkbox" name="rights[${menu}][add]" value="add" class="ms-2">
                </label>
                <label class="me-4 mb-0 d-flex align-items-center" style="width: 80px;">
                    Edit <input type="checkbox" name="rights[${menu}][edit]" value="edit" class="ms-2">
                </label>
                <label class="mb-0 d-flex align-items-center" style="width: 90px;">
                    Delete <input type="checkbox" name="rights[${menu}][delete]" value="delete" class="ms-2">
                </label>
            </li>
        `;
    });
    html += '</ul>';
    menuContainer.innerHTML = html;
}
</script>
              <div class="box-footer">
                <input type="hidden" id="user_right_id" name="user_right_id" value= "<?php if(isset($_bll->_mdl))  echo $_bll->_mdl->_user_right_id; else echo 0; ?>">
                <input type="hidden" id="transactionmode" name="transactionmode" value= "<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                <input type="hidden" id="modified_by" name="modified_by" value= "1">
                <input type="hidden" id="modified_date" name="modified_date" value= "<?php echo date('Y-m-d H:i:s')?>">
                <input type="hidden" id="detail_records" name="detail_records" />
                <input type="hidden" id="deleted_records" name="deleted_records" />
                <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value= "Save">
                <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_user_right_master.php'">
                <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();" >
                <input type="button" class="btn btn-dark" id="btn_cancel" name="btn_cancel" value="Cancel"  onclick="window.location=window.history.back();">
              </div>
        </form>
          </div>
          </div>
      </section>
    </div>
    </div>
</div>

<?php include('include/footer.php'); ?>