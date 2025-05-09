<?php
include("classes/cls_company_year_master.php");
include("include/header.php");
include("include/theme_styles.php");
include("include/header_close.php");

$transactionmode = $_REQUEST["transactionmode"] ?? "";
$label = ($transactionmode == "U") ? "Update" : "Add";

// Get available year ranges
$query = "
    SELECT company_year_id, 
           CONCAT(YEAR(start_date), '-', YEAR(end_date)) AS year_range 
    FROM tbl_company_year_master
";
$stmt = $_dbh->prepare($query);
$stmt->execute();
$yearRanges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['companyYear'])) {
    $selectedYearId = $_POST['companyYear'];

    $stmt = $_dbh->prepare("SELECT CONCAT(YEAR(start_date), '-', YEAR(end_date)) AS year_range FROM tbl_company_year_master WHERE company_year_id = ?");
    $stmt->execute([$selectedYearId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $_SESSION['sess_company_year_id'] = $selectedYearId;
        $_SESSION['sess_selected_year'] = 'FY ' . $result['year_range'];
    }

    header("Location: dashboard.php");
    exit;
}

// Auto-select year on login if not already set
if (empty($_SESSION['sess_company_year_id'])) {
    $today = date('Y-m-d');
    $stmt = $_dbh->prepare("
        SELECT company_year_id, CONCAT(YEAR(start_date), '-', YEAR(end_date)) AS year_range 
        FROM tbl_company_year_master 
        WHERE ? BETWEEN start_date AND end_date 
        LIMIT 1
    ");
    $stmt->execute([$today]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $_SESSION['sess_company_year_id'] = $row['company_year_id'];
        $_SESSION['sess_selected_year'] = 'FY ' . $row['year_range'];
    }
}

$currentYearId = $_SESSION['sess_company_year_id'] ?? '';
?>
<body class="hold-transition skin-blue layout-top-nav">
<?php include("include/body_open.php"); ?>
<div class="wrapper">
    <?php include("include/navigation.php"); ?>
    <div class="content-wrapper">
        <div class="container-fluid">
            <section class="content-header">
                <h1><?php echo $label; ?> Company Year</h1>
                <ol class="breadcrumb">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                    <li class="active"><?php echo $label; ?></li>
                </ol>
            </section>

            <section class="content">
                <div class="col-md-12" style="padding:0;">
                    <div class="box box-info">
                        <form action="" method="post" class="form-horizontal">
                            <div class="box-body">
                                <div class="form-group row">
                                    <label for="companyYear" class="col-sm-2 control-label">Select Company Year</label>
                                    <div class="col-sm-4">
                                        <select name="companyYear" id="companyYear" class="form-control" required>
                                            <option value="">-- Select Year --</option> 
                                            <?php foreach ($yearRanges as $yearRange): ?>
                                                <option value="<?php echo $yearRange['company_year_id']; ?>" 
                                                    <?php echo ($currentYearId == $yearRange['company_year_id']) ? 'selected' : ''; ?>>
                                                    <?php echo $yearRange['year_range']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <input type="submit" class="btn btn-success" value="Update">
                                <button type="button" class="btn btn-dark" onclick="window.history.back();">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php include("include/footer.php"); ?>
<?php include("include/footer_includes.php"); ?>
<?php include("include/footer_close.php"); ?>
