<?php
include("config/connection.php");
include("include/header.php");
?>
<link rel="stylesheet" href="plugins/select2/select2.min.css">
<link rel="stylesheet" href="dist/css/styleright.css">
<?php
include("include/theme_styles.php");
include("include/header_close.php");

// Fetch users from the database
try {
    $stmtusers = $_dbh->prepare("SELECT user_id, login_id FROM tbl_user_master ORDER BY login_id ASC");
    $stmtusers->execute();
    $users = $stmtusers->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading users: " . $e->getMessage();
}

class bll_userrightmaster {

   public function saveRights($user_id, $rights) {
    global $_dbh;

    try {
        echo "User ID: $user_id<br>";  // Debugging line
        var_dump($rights);  // Debugging line to see the structure of $rights

        foreach ($rights as $menu => $actions) {
            // Check if menu_right_id exists in tbl_menu_right_master
            $menuExistsSql = "SELECT COUNT(*) FROM tbl_menu_right_master WHERE menu_right_id = ?";
            $stmt = $_dbh->prepare($menuExistsSql);
            $stmt->execute([$menu]);
            $menuExists = $stmt->fetchColumn();

            if ($menuExists == 0) {
                echo "Menu ID $menu does not exist in tbl_menu_right_master. Skipping...<br>";
                continue;  // Skip this menu if it does not exist in the menu table
            }

            // Set rights
            $is_right = (isset($actions['view']) || isset($actions['add']) || isset($actions['edit']) || isset($actions['delete'])) ? 1 : 0;

            // Check if record exists for this user and menu
            $sql = "SELECT COUNT(*) FROM tbl_user_right_master WHERE user_id = ? AND menu_right_id = ?";
            $stmt = $_dbh->prepare($sql);
            $stmt->execute([$user_id, $menu]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                // Update the existing record
                $updateSql = "UPDATE tbl_user_right_master 
                              SET is_right = ? 
                              WHERE user_id = ? AND menu_right_id = ?";
                $updateStmt = $_dbh->prepare($updateSql);
                $updateStmt->execute([$is_right, $user_id, $menu]);
                echo "Updated user rights for menu $menu.<br>";  // Debugging line
            } else {
                // Insert the new record
                $insertSql = "INSERT INTO tbl_user_right_master (user_id, menu_right_id, is_right)
                              VALUES (?, ?, ?)";
                $insertStmt = $_dbh->prepare($insertSql);
                $insertStmt->execute([$user_id, $menu, $is_right]);
                echo "Inserted user rights for menu $menu.<br>";  // Debugging line
            }
        }

        return true;  // Success
    } catch (PDOException $e) {
        echo "Error saving user rights: " . $e->getMessage();  // Debugging error message
        return false;
    }
}

}



// Check if the form is submitted to save the rights
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveRights') {
    $user_id = $_POST['user_id'];
    $rights = $_POST['rights']; // Array of rights

    // Create an instance of the class
    $bll = new bll_userrightmaster();

    // Call saveRights method to save the user rights
    $success = $bll->saveRights($user_id, $rights);

    if ($success) {
        header("Location: srh_user_right_master1.php");
        exit;
    } else {
        echo "Failed to save user rights.";
    }
}
?>

<body class="hold-transition skin-blue layout-top-nav">
<?php include("include/body_open.php"); ?>
<div class="wrapper">
<?php include("include/navigation.php"); ?>

<div class="content-wrapper">
    <div class="container-fluid">
        <section class="content-header">
            <h1>Edit User Right</h1>
        </section>

        <section class="content">
            <div class="col-md-12" style="padding:0;">
                <div class="box box-info">
                    <form class="form-horizontal" id="rightsForm" method="post" action="">
                        <div class="box-body">
                            <div class="form-group row mb-3">
                                <label for="user_id" class="col-sm-1 col-form-label">User Id*</label>
                                <div class="col-sm-3">
                                    <select class="form-select form-control-sm" name="user_id" id="user_id" required>
                                        <option value="">Select User</option>
                                        <?php foreach ($users as $user) {
                                            echo "<option value='{$user['user_id']}'>" . htmlspecialchars($user['login_id']) . "</option>";
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <!-- Menu Categories -->
                            <div class="row">
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-header" onclick="showMenus(this, 'Master')">
                                            <h5 class="mb-0">Master</h5>
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
                            <input type="hidden" name="action" value="saveRights">
                            <button type="submit" class="btn btn-success">Save</button>
                            <input type="button" class="btn btn-primary" id="btn_search" name="btn_search" value="Search" onclick="window.location='srh_user_right_master.php'">
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
    let menuBody = headerElem.nextElementSibling;
    
    // Check if the current header already has an associated menu-body
    const isOpen = menuBody && menuBody.classList.contains('menu-body') && menuBody.classList.contains('show');
    
    // Close all other open menu bodies
    allMenuBodies.forEach(div => div.classList.remove('show'));

    const icon = headerElem.querySelector('.menu-icon');
    if (icon) {
        icon.textContent = isOpen ? "+" : "-";
    }

    // If menu is not open, create the menu body
    if (!isOpen) {
        // If the menu-body doesn't exist, create it
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

        // Add the "show" class to make it visible
        menuBody.classList.add('show');
    } else {
        // If it's open, just toggle it off
        menuBody.classList.remove('show');
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

</script>