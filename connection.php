<?php
require __DIR__ . '/../vendor/autoload.php'; // ADDED BY BHUMITA
// Start the session if it hasn't already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

/* ADDED BY BHUMITA */
/* GET COMPANY ID */
use Hashids\Hashids;

$hashids = new Hashids('',6);


$encodedId = $_GET['company_id'] ?? null;
if ($encodedId) { 
    $decoded = $hashids->decode($encodedId);
    if (!empty($decoded)) {
        $_SESSION["sess_encoded_company_id"]=$encodedId; 
        $realId = $decoded[0];
        $_SESSION["sess_company_id"]=$realId;
    } 
}else {
   /* $encoded = $hashids->encode(2); 
    echo "Try this: <a href='http://cbs5-pc/csms1/$encoded'>http://cbs5-pc/csms1/$encoded</a>";
    exit;*/
} 
/* \GET COMPANY ID */
/* \ADDED BY BHUMITA */

global $database_name;
global $_dbh;
$servername = "localhost";
$username = "root";
$password = "";
$database_name="csms1";
date_default_timezone_set("Asia/Kolkata");
try {
  
  $_dbh = new PDO("mysql:host=$servername;dbname=".$database_name, $username, $password);
  // set the PDO error mode to exception
  $_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}


/*Switch year By Mansi*/
if (
    (!isset($_SESSION["sess_company_year_id"]) || empty($_SESSION['sess_company_year_id'])) &&
    (!isset($_SESSION["sess_selected_year"]) || empty($_SESSION['sess_selected_year']))
) {
    $currentMonth = date("n");
    $currentYear = date("Y");

    if ($currentMonth >= 4) {
        $startYear = $currentYear;
        $endYear = $currentYear + 1;
    } else {
        $startYear = $currentYear - 1;
        $endYear = $currentYear;
    }
    $columns = "company_year_id";
    $tableName = "tbl_company_year_master";
    $whereClause = "YEAR(start_date) = $startYear AND YEAR(end_date) = $endYear";
    $stmt = $_dbh->prepare("CALL csms1_search_detail(:columns, :tableName, :whereClause)");
    $stmt->bindParam(':columns', $columns);
    $stmt->bindParam(':tableName', $tableName);
    $stmt->bindParam(':whereClause', $whereClause);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if (!$row) {
        $startDate = "$startYear-04-01";
        $endDate = "$endYear-03-31";
        $insert = $_dbh->prepare("
            INSERT INTO tbl_company_year_master (start_date, end_date, company_id)
            VALUES (?, ?, ?)
        ");
        $insert->execute([$startDate, $endDate]);
        $companyYearId = $_dbh->lastInsertId();
    } else {
        $companyYearId = $row['company_year_id'];
    }
    $_SESSION['sess_selected_year'] = 'FY ' . $startYear . '-' . $endYear;
    $_SESSION["sess_company_year_id"] = $companyYearId;
}
/*Done*/

include("variables.php"); // ADDED BY BHUMITA
?> 
