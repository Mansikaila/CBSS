<?php
include("classes/cls_user_right_master.php");
include("include/header.php");
?>
<link rel="stylesheet" href="plugins/select2/select2.min.css">
<link rel="stylesheet" href="dist/css/styleright.css">

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
?>
<?php
if (isset($_POST["user_id"]) && isset($_POST["type"]) && $_POST["type"] == "ajax") {
    header('Content-Type: application/json');
    $user_id = $_POST["user_id"];
    try {
        $stmt = $_dbh->prepare("
            SELECT mm.menu_name, mrm.right_name
            FROM tbl_user_right_master urm
            JOIN tbl_menu_right_master mrm ON urm.menu_right_id = mrm.menu_right_id
            JOIN tbl_menu_master mm ON mrm.menu_id = mm.menu_id
            WHERE urm.user_id = ?
        ");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $rights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userRights = [];
        foreach ($rights as $right) {
            $menu_name = $right["menu_name"];
            $right_name = strtolower($right["right_name"]);

            if (!isset($userRights[$menu_name])) {
                $userRights[$menu_name] = [];
            }

            $userRights[$menu_name][] = $right_name;
        }
        echo json_encode($userRights);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php
include("include/body_open.php");
?>
<div class="wrapper">
<?php
include("include/navigation.php");
?>
<div class="content-wrapper">
    <div class="container-fluid">
        <section class="content-header">
            <h1>Edit User Right</h1>
        </section>
        <section class="content">
            <div class="col-md-12" style="padding:0;">
                <div class="box box-info">
                    <form class="form-horizontal" method="post" >
                        <div class="box-body">
                            
                           <div class="form-group row mb-3">
                                <label for="user_id" class="col-sm-1 col-form-label">User Id*</label>
                                <div class="col-sm-3">
                                    <select class="form-select form-control-sm" name="user_id" id="user_id" required>
                                        <option value="">Select User</option>
                                        <?php
                                        foreach ($users as $user) {
                                            echo "<option value='{$user['user_id']}'>" . htmlspecialchars($user['login_id']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                           <div class="row">
                                <div class="mb-3">
                                    <div id="categoryAccordion">
                                        <div class="card">
                                            <div class="card-header" onclick="showMenus(this, 'Master')">
                                                <h5 class="mb-0">Master</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-header" onclick="showMenus(this, 'Transaction')">
                                            <h5 class="mb-0">Transaction</h5>
                                        </div>
                                    </div>
                                </div>
                               <div class="mb-3">
                                    <div class="card">
                                        <div class="card-header" onclick="showMenus(this, 'Utility')">
                                            <h5 class="mb-0">Utility</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-header" onclick="showMenus(this, 'Report')">
                                            <h5 class="mb-0">Report</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-header" onclick="showMenus(this, 'Account')">
                                            <h5 class="mb-0">Account</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-header" onclick="showMenus(this, 'Admin')">
                                            <h5 class="mb-0">Admin</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div id="menuContainer"></div>
                        </div>
                    </div>
                        </div>
                        <div class="box-footer">
                            <input type="hidden" id="user_right_id" name="user_right_id" value="<?php if(isset($_bll->_mdl))  echo $_bll->_mdl->_user_right_id; else echo 0; ?>">
                            <input type="hidden" id="transactionmode" name="transactionmode" value="<?php if($transactionmode=="U") echo "U"; else echo "I";  ?>">
                            <input type="hidden" id="modified_by" name="modified_by" value="1">
                            <input type="hidden" id="modified_date" name="modified_date" value="<?php echo date('Y-m-d H:i:s'); ?>">
                            <input type="hidden" id="detail_records" name="detail_records" />
                            <input type="hidden" id="deleted_records" name="deleted_records" />
                            <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                            <input class="btn btn-success" type="button" id="btn_add" name="btn_add" value="Save">
                            <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_user_right_master.php'">
                            <input class="btn btn-secondary" type="button" id="btn_reset" name="btn_reset" value="Reset" onclick="reset_data();">
                            <input type="button" class="btn btn-dark" id="btn_cancel" name="btn_cancel" value="Cancel" onclick="window.location=window.history.back();">
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>
</div>
<?php include('include/footer.php'); ?>
<script>
function showMenus(headerElem, category) {
    const allMenuBodies = document.querySelectorAll('.menu-body');

    const isOpen = headerElem.nextElementSibling && headerElem.nextElementSibling.classList.contains('menu-body') && headerElem.nextElementSibling.classList.contains('show');
    
    allMenuBodies.forEach(div => div.classList.remove('show'));

    const icon = headerElem.querySelector('.menu-icon');
    if (icon) {
        icon.textContent = isOpen ? "+" : "-";
    }

    if (!isOpen) {
        let menuBody = headerElem.nextElementSibling;

        if (!menuBody || !menuBody.classList.contains('menu-body')) {
            menuBody = document.createElement('div');
            menuBody.className = 'menu-body';
            headerElem.parentElement.appendChild(menuBody);
        }

        const menus = {
            'Master': [
                'Country', 'State', 'City', 'Currency', 'Bank',
                'Customer Account Group', 'Customer', 'Packing Unit', 'Item',
                'Chamber', 'Floor', 'Item Preservation Price List',
                'Customer wise Item Preservation Price List', 'HSN Code'
            ],
            'Transaction': ['Inward', 'Outward', 'Invoice'],
            'Utility': ['Generate Rent Invoice', 'Multi Invoice Print', 'Inward Lock / Unlock', 'Change Location'],
            'Report': [
                'Inward Summary', 'Outward Summary', 'Invoice Summary',
                'Invoice GST Summary', 'Inward Outward Summary', 'Rent Valuation',
                'Party wise Inward Balance', 'Item Stock', 'Item Stock Statement',
                'Lot Statement', 'Lot Transfer History', 'Location Detail View',
                'Item Preservation Charges List', 'Yearly Stock Report', 'Location Change History'
            ],
            'Account': ['Receipt', 'Payment', 'Contra', 'Journal', 'Day Book', 'Account Ledger', 'Net Payable Outstanding', 'Net Receivable Outstanding'],
            'Admin': ['Company', 'Company Year', 'Menu', 'Module', 'User Master', 'User Right']
        };

        if (menus[category]) {
            let html = `
            <section class="content">
                <div class="col-md-12" style="padding:0;">
                    <form class="form-horizontal" method="post" onsubmit="return validateForm()">    
            `;
            menus[category].forEach(menu => {
                html += `
                <div class="form-group">
                    <label class="custom-badge-container">
                        <span class="custom-badge">${menu}</span>
                    </label>
                    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                        <label>
                            View <input type="checkbox" name="rights[${menu}][view]" value="view">
                        </label>
                        <label>
                            Add <input type="checkbox" name="rights[${menu}][add]" value="add">
                        </label>
                        <label>
                            Edit <input type="checkbox" name="rights[${menu}][edit]" value="edit">
                        </label>
                        <label>
                            Delete <input type="checkbox" name="rights[${menu}][delete]" value="delete">
                        </label>
                    </div>
                </div>
                `;
            });
            html += `
                </form>
            </div>
            </section>
            `;
            menuBody.innerHTML = html;
        } else {
            menuBody.innerHTML = '<p>No menus available for this category.</p>';
        }
        menuBody.classList.add('show');
    }
}
document.querySelectorAll('.card-header').forEach(header => {
    const icon = document.createElement('span');
    icon.classList.add('menu-icon');
    icon.textContent = "+"; 
    icon.style.float = "right"; 
    header.style.display = 'flex';
    header.style.justifyContent = 'space-between'; 
    header.appendChild(icon);
});

function loadUserRights(userId) {
    if (userId === "") return;
    $.ajax({
        type: "POST",
        url: "",
        data: {
            user_id: userId,
            type: "ajax"
        },
        success: function(response) {
            try {
                let userRights = JSON.parse(response);
                console.log("Parsed userRights:", userRights);
                
                let menuContainer = $('#menuContainer');
                let checkboxes = menuContainer.find("input[type=checkbox]");
                checkboxes.prop('checked', false);
                checkboxes.each(function () {
                    let nameAttr = $(this).attr('name');
                    let matches = nameAttr.match(/^rights\[(.+?)\]\[(.+?)\]$/);
                    if (matches && matches.length === 3) {
                        let menu = matches[1];
                        let right = matches[2];

                        if (userRights[menu] && userRights[menu].includes(right)) {
                            $(this).prop('checked', true);
                        }
                    }
                });
            } catch (err) {
                console.error("Error parsing JSON:", err);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        }
    });
}
</script>