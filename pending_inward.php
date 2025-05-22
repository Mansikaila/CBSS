<?php
include('config/connection.php');
try {
    // Adjust the WHERE clause if you want to filter, e.g., for pending inwards or by customer_id.
    $sql = "SELECT 
                inward_no,
                lot_no,
                inward_date,
                broker,
                item_name,
                marko,
                inward_qty,
                unit,
                inward_wt_kg,
                stock_qty,
                stock_wt_kg,
                out_qty,
                loading_charges,
                location
            FROM tbl_inward_master
            ORDER BY inward_date DESC";

    $stmt = $connect->prepare($sql);
    $stmt->execute();
    $inwards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($inwards) {
        header('Content-Type: application/json');
        echo json_encode($inwards);
    } else {
        echo json_encode(["message" => "No records found."]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
?>