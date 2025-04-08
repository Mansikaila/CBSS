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
                <div class="form-group row">
                    <!-- User Dropdown -->
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
                    <label class="col-sm-2 col-form-label">Menu Right ID*</label>
                    <div class="col-sm-3">
                        <select class="form-select" name="menu_right_id" id="menu_right_id" required>
                            <option value="">Select Menu</option>
                            <?php
                            foreach ($menus as $menu) {
                                echo "<option value='{$menu['menu_right_id']}'>" . htmlspecialchars($menu['right_name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                 </div>
              </div>

    <div class="row">
    <div class="col-md-12">
        <form>
            <div class="form-group">
                <select class="form-control" onchange="showMenus(this.value)">
                    <option value="Master">Master</option>
                    <option value="Transaction">Transaction</option>
                    <option value="Report">Report</option>
                    <option value="Account">Account</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
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
            'District',
            'Customer',
            'Account Group',
            'Account',
            'Unit',
            'Item',
            'Chamber',
            'Floor',
            'Rack',
            'Location',
            'Item_unit Mapping',
            'Item_unit',
            'Item Rent Price List',
            'Operator',
            'GST Commodity',
            'Flag List',
            'Help',
            'Switch Year'
        ],
        'Transaction': [
            'Order',
            'Inward',
            'Outward',
            'Issue Slip',
            'Return Slip',
            'Order Acceptance',
            'Order Dispatch',
            'Invoice',
            'Item Stock',
            'Receipt',
            'Payment',
            'Contra',
            'Journal',
            'Change Location'
        ],
        'Report': [
            'Inward Summary',
            'Outward Summary',
            'Inward Outward Summary',
            'Invoice Summary',
            'Invoice GST Summary',
            'Lot No. wise Invoice Summary',
            'Party Label',
            'Party wise Inward Balance',
            'Location Detail View',
            'Lot Statement',
            'Item Statement',
            'Item Stock Statement',
            'Yearly Stock Report',
            'Item Rent Price List Report',
            'Rent Valuation',
            'Cover Print',
            'Customer List'
        ],
        'Account': [
            'Day Book',
            'Cash & Bank',
            'Account Ledger',
            'Trial Balance',
            'Trading Account',
            'Profit & Loss',
            'Balance Sheet',
            'List of Account'
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

    let html = '<ul class="list-group">';
    menus[module].forEach(menu => {
        html += `<li class="list-group-item">${menu}</li>`;
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

