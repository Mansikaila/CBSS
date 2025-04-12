<?php
include("config/connection.php");
include("include/header.php");
?>
<link rel="stylesheet" href="plugins/select2/select2.min.css">
<link rel="stylesheet" href="dist/css/styleright.css">
<?php
include("include/theme_styles.php");
include("include/header_close.php");

$transactionmode = "";
if (isset($_REQUEST["transactionmode"])) {
    $transactionmode = $_REQUEST["transactionmode"];
}
if ($transactionmode == "U") {
    $_bll->fillModel();
    $label = "Update";
} else {
    $label = "Add";
}
try {
    $columns = 'user_id, login_id';  
    $tableName = 'tbl_user_master';      
    $stmt = $_dbh->prepare("CALL csms1_search(:columns, :tableName)");

    $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
    $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt->closeCursor(); 
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


// Modify the query to include the right_name from tbl_menu_right_master
try {
    $stmtmenu = $_dbh->prepare("SELECT menu_right_id, right_name, right_text FROM tbl_menu_right_master ORDER BY menu_right_id ASC");
    $stmtmenu->execute();
    $users = $stmtmenu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading users: " . $e->getMessage();
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
            <div class="col-md-12" style="padding: 0;">
                <div class="box box-info">
                    <form class="form-horizontal" id="masterForm" method="post" action="classes/cls_user_right_master.php">
                        <div class="box-body">
                            <div class="form-group row mb-3">
                                <label for="user_id" class="col-sm-1 col-form-label">User Id*</label>
                                <div class="col-sm-3">
                                    <select class="form-select form-control-sm" name="user_id" id="user_id" required>
                                        <option value="">Select User</option>
                                        <?php 
                                            if (!empty($users)) {
                                                foreach ($users as $user) {
                                                 echo "<option value='" . htmlspecialchars($user['user_id']) . "'>" . htmlspecialchars($user['user_name']) . htmlspecialchars($user['login_id']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No users found</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Menu Categories with Checkboxes -->
                           <?php
                            if (!empty($menuData)) {
                                echo "<div class='row'>";
                                echo "<div style='padding-top:50px;'>";
                                foreach ($menuData as $module => $groups) {
                                    echo "<div class='mb-3'>";
                                    echo "<div class='card'>";
                                    echo "<div class='card-header' onclick='showMenus(this, \"$module\")'>";
                                    echo "<h5 class='mb-0'>$module</h5>";
                                    echo "</div>";
                                    echo "<div class='card-body' id='menus_$module'>";

                                    foreach ($groups as $group => $menuItems) {
                                        echo "<div class='menu-group' id='group_$group'>";

                                        foreach ($menuItems as $item) {
                                     $viewRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['view']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['view'] : 0;
                                    $addRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['add']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['add'] : 0;
                                    $editRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['edit']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['edit'] : 0;
                                    $deleteRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['delete']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['delete'] : 0;


                                        echo "<div class='row'>";
                                        echo "<div class='col-md-2 menu-name-column'><span class='menu-name'>{$item['name']}</span></div>";
                                        if (!isset($item['menu_right_id'])) {
                                            $item['menu_right_id'] = '';
                                        }
                                        echo "<input type='hidden' name='rights[{$item['name']}][menu_right_id]' value='" . htmlspecialchars($item['menu_right_id']) . "' />";


                                        echo "<div class='col-md-6 d-flex flex-wrap align-items-center'>";
                                        echo "<input type='hidden' name='rights[{$item['name']}][view]' value='0'>";
                                        echo "<input type='hidden' name='rights[{$item['name']}][add]' value='0'>";
                                        echo "<input type='hidden' name='rights[{$item['name']}][edit]' value='0'>";
                                        echo "<input type='hidden' name='rights[{$item['name']}][delete]' value='0'>";

                                        // Checkboxes for each right, checking if the user already has the right
                                        echo "<label class='mr-3 mb-2'>View <input type='checkbox' name='rights[{$item['name']}][view]' value='view' " . ($viewRight ? "checked" : "") . "></label>";
                                        echo "<label class='mr-3 mb-2'>Add <input type='checkbox' name='rights[{$item['name']}][add]' value='add' " . ($addRight ? "checked" : "") . "></label>";
                                        echo "<label class='mr-3 mb-2'>Edit <input type='checkbox' name='rights[{$item['name']}][edit]' value='edit' " . ($editRight ? "checked" : "") . "></label>";
                                        echo "<label class='mr-3 mb-2'>Delete <input type='checkbox' name='rights[{$item['name']}][delete]' value='delete' " . ($deleteRight ? "checked" : "") . "></label>";

                                        echo "</div>";  
                                        echo "</div>"; 
                                    }
                                        echo "</div>"; 
                                    }

                                    echo "</div>"; 
                                    echo "</div>"; 
                                    echo "</div>"; 
                                }
                                echo "</div>"; 
                                echo "</div>";  
                            }
                            ?>

                        </div>
                        <div class="box-footer">
                            
                           <input type="hidden" id="user_right_id" name="user_right_id" value="<?php echo isset($_bll->_mdl->_user_right_id) ? $_bll->_mdl->_user_right_id : 0; ?>">
                            <input type="hidden" id="transactionmode" name="transactionmode" value="<?php echo ($transactionmode == "U") ? "U" : "I"; ?>">
                            <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                            <input class="btn btn-success" type="submit" id="btn_add" name="btn_add" value="Save">
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
<?php include('include/footer_includes.php'); ?>
<script>
function showMenus(header, module) {
    const menuContainer = document.getElementById(`menus_${module}`);
    
    if (menuContainer.style.display === "none" || menuContainer.style.display === "") {
        $(menuContainer).slideDown(400); 
    } else {
        $(menuContainer).slideUp(400); 
    }
    $(".card-body").not(menuContainer).slideUp(400);
}
</script><?php
include("config/connection.php");
include("include/header.php");
?>
<link rel="stylesheet" href="plugins/select2/select2.min.css">
<link rel="stylesheet" href="dist/css/styleright.css">
<?php
include("include/theme_styles.php");
include("include/header_close.php");

$transactionmode = "";
if (isset($_REQUEST["transactionmode"])) {
    $transactionmode = $_REQUEST["transactionmode"];
}
if ($transactionmode == "U") {
    $_bll->fillModel();
    $label = "Update";
} else {
    $label = "Add";
}
try {
    $columns = 'user_id, login_id';  
    $tableName = 'tbl_user_master';      
    $stmt = $_dbh->prepare("CALL csms1_search(:columns, :tableName)");

    $stmt->bindParam(':columns', $columns, PDO::PARAM_STR);
    $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt->closeCursor(); 
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


// Modify the query to include the right_name from tbl_menu_right_master
try {
    $stmtmenu = $_dbh->prepare("SELECT m.menu_right_id, m.right_name, m.right_text 
                                FROM tbl_menu_right_master m
                                WHERE EXISTS (SELECT 1 FROM tbl_user_right_master ur WHERE ur.menu_right_id = m.menu_right_id)");
    $stmtmenu->execute();
    $menuData = $stmtmenu->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error loading menu rights: " . $e->getMessage();
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
            <div class="col-md-12" style="padding: 0;">
                <div class="box box-info">
                    <form class="form-horizontal" id="masterForm" method="post" action="classes/cls_user_right_master.php">
                        <div class="box-body">
                            <div class="form-group row mb-3">
                                <label for="user_id" class="col-sm-1 col-form-label">User Id*</label>
                                <div class="col-sm-3">
                                    <select class="form-select form-control-sm" name="user_id" id="user_id" required>
                                        <option value="">Select User</option>
                                        <?php 
                                            if (!empty($users)) {
                                                foreach ($users as $user) {
                                                 echo "<option value='" . htmlspecialchars($user['user_id']) . "'>" . htmlspecialchars($user['user_name']) . htmlspecialchars($user['login_id']) . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No users found</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Menu Categories with Checkboxes -->
                           <?php
                            if (!empty($menuData)) {
                                echo "<div class='row'>";
                                echo "<div style='padding-top:50px;'>";
                                foreach ($menuData as $module => $groups) {
                                    echo "<div class='mb-3'>";
                                    echo "<div class='card'>";
                                    echo "<div class='card-header' onclick='showMenus(this, \"$module\")'>";
                                    echo "<h5 class='mb-0'>$module</h5>";
                                    echo "</div>";
                                    echo "<div class='card-body' id='menus_$module'>";

                                    foreach ($groups as $group => $menuItems) {
                                        echo "<div class='menu-group' id='group_$group'>";

                                        foreach ($menuItems as $item) {
 $viewRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['view']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['view'] : 0;
$addRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['add']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['add'] : 0;
$editRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['edit']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['edit'] : 0;
$deleteRight = isset($_bll->_mdl->_array_detail[$item['menu_right_id']]['delete']) ? $_bll->_mdl->_array_detail[$item['menu_right_id']]['delete'] : 0;


    echo "<div class='row'>";
    echo "<div class='col-md-2 menu-name-column'><span class='menu-name'>{$item['name']}</span></div>";

    // Hidden input for menu_right_id (this should always be included in the form submission)
    echo "<input type='hidden' name='menu_right_id' value='" . (isset($_bll->_mdl->_menu_right_id) ? $_bll->_mdl->_menu_right_id : 0) . "' />";

    echo "<div class='col-md-6 d-flex flex-wrap align-items-center'>";
    echo "<input type='hidden' name='rights[{$item['name']}][view]' value='0'>";
    echo "<input type='hidden' name='rights[{$item['name']}][add]' value='0'>";
    echo "<input type='hidden' name='rights[{$item['name']}][edit]' value='0'>";
    echo "<input type='hidden' name='rights[{$item['name']}][delete]' value='0'>";

    // Checkboxes for each right, checking if the user already has the right
    echo "<label class='mr-3 mb-2'>View <input type='checkbox' name='rights[{$item['name']}][view]' value='view' " . ($viewRight ? "checked" : "") . "></label>";
    echo "<label class='mr-3 mb-2'>Add <input type='checkbox' name='rights[{$item['name']}][add]' value='add' " . ($addRight ? "checked" : "") . "></label>";
    echo "<label class='mr-3 mb-2'>Edit <input type='checkbox' name='rights[{$item['name']}][edit]' value='edit' " . ($editRight ? "checked" : "") . "></label>";
    echo "<label class='mr-3 mb-2'>Delete <input type='checkbox' name='rights[{$item['name']}][delete]' value='delete' " . ($deleteRight ? "checked" : "") . "></label>";

    echo "</div>";  
    echo "</div>"; 
}
                                        echo "</div>"; 
                                    }

                                    echo "</div>"; 
                                    echo "</div>"; 
                                    echo "</div>"; 
                                }
                                echo "</div>"; 
                                echo "</div>";  
                            }
                            ?>

                        </div>
                        <div class="box-footer">
                            
                           <input type="hidden" id="user_right_id" name="user_right_id" value="<?php echo isset($_bll->_mdl->_user_right_id) ? $_bll->_mdl->_user_right_id : 0; ?>">
                            <input type="hidden" id="transactionmode" name="transactionmode" value="<?php echo ($transactionmode == "U") ? "U" : "I"; ?>">
                            <input type="hidden" name="masterHidden" id="masterHidden" value="save" />
                            <input class="btn btn-success" type="submit" id="btn_add" name="btn_add" value="Save">
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
<?php include('include/footer_includes.php'); ?>
<script>
function showMenus(header, module) {
    const menuContainer = document.getElementById(`menus_${module}`);
    
    if (menuContainer.style.display === "none" || menuContainer.style.display === "") {
        $(menuContainer).slideDown(400); 
    } else {
        $(menuContainer).slideUp(400); 
    }
    $(".card-body").not(menuContainer).slideUp(400);
}
</script>