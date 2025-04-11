<?php 
include('config/connection.php');  

class mdl_countrymaster {
    public $_country_id;
    public $_country_name;
    public $_created_date;
    public $_created_by;
    public $_modified_date;
    public $_modified_by;
    public $_transactionmode;
}

class bll_countrymaster {
    public $_mdl;
    public $_dal;

    public function __construct() {
        $this->_mdl = new mdl_countrymaster();
        $this->_dal = new dal_countrymaster();
    }

    public function fillModel($countryId = null) {
        if (!empty($countryId)) {
            $this->_dal->fillModel($this->_mdl, $countryId);
        }
    }

    public function checkCountryExists($countryName, $countryId = null) {
        return $this->_dal->checkCountryExists($countryName, $countryId);
    }

    public function dbTransaction() {
    try {
        global $connect;
        $connect->beginTransaction(); // Start transaction

        $currentDate = date('Y-m-d H:i:s');
        $userId = $_SESSION["ad_session"] ?? "System"; 


        if ($this->_mdl->_transactionmode === 'I') {
            $this->_mdl->_created_date = $currentDate;
            $this->_mdl->_created_by = $userId;
        }

        if (in_array($this->_mdl->_transactionmode, ['I', 'U'])) {
            $this->_mdl->_modified_date = $currentDate;
            $this->_mdl->_modified_by = $userId;
        }

        // **Check if the country already exists**
        if ($this->checkCountryExists($this->_mdl->_country_name, $this->_mdl->_country_id)) {
            echo "<script>alert('Record already exists!');</script>";
            $connect->rollBack();
            return false;
        }

        $result = $this->_dal->dbTransaction($this->_mdl);
        $connect->commit(); // Commit transaction
        return $result;
    } catch (Exception $e) {
        $connect->rollBack(); // Rollback on failure
        error_log("Transaction failed: " . $e->getMessage());
        return false;
    }
}


    public function pageSearch() {
        global $connect;

        $sql = "SELECT t.country_name, t.modified_date, u.name AS modified_by, t.country_id 
                FROM tbl_country_master t 
                LEFT JOIN tbl_user_master u ON t.modified_by = u.id";
        
        $stmt = $connect->prepare($sql);
        $stmt->execute();
        $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "<table id='searchMaster' class='table table-bordered table-striped'>
                <thead>
                    <tr><th>Country Name</th><th>Modified Date</th><th>Modified By</th><th>Action</th></tr>
                </thead>
                <tbody>";

        foreach ($countries as $row) {
            echo "<tr>
                    <td>{$row['country_name']}</td>
                    <td>{$row['modified_date']}</td>
                    <td>{$row['modified_by']}</td>
                    <td>
                        <form method='post' action='frm_country_master.php' style='display:inline; margin-right:5px;'>
                            <input class='btn btn-default update' type='submit' name='btn_update' value='Edit' />
                            <input type='hidden' name='country_id' value='{$row['country_id']}' />
                            <input type='hidden' name='transactionmode' value='U' />
                        </form>
                        <form method='post' action='delete_country.php' style='display:inline;'>
                            <input class='btn btn-danger delete' type='submit' name='btn_delete' value='Delete' />
                            <input type='hidden' name='country_id' value='{$row['country_id']}' />
                            <input type='hidden' name='transactionmode' value='D' />
                        </form>
                    </td>
                </tr>";
        }

        echo "</tbody></table>";
    }

    public function deleteCountry($countryId) {
        return $this->_dal->deleteCountry($countryId);
    }
}

class dal_countrymaster {
    public function dbTransaction($mdl) {
        global $connect;

        $sql = "CALL transaction_country_master(:country_id, :country_name, NOW(), :created_by, NOW(), :modified_by, :transactionmode)";
        $stmt = $connect->prepare($sql);
        
        $stmt->bindParam(':country_id', $mdl->_country_id, PDO::PARAM_INT);
        $stmt->bindParam(':country_name', $mdl->_country_name, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $mdl->_created_by, PDO::PARAM_STR);
        $stmt->bindParam(':modified_by', $mdl->_modified_by, PDO::PARAM_STR);
        $stmt->bindParam(':transactionmode', $mdl->_transactionmode, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    public function fillModel($mdl, $countryId) {
        global $connect;

        $sql = "SELECT * FROM tbl_country_master WHERE country_id = :country_id";
        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $mdl->_country_id = $row["country_id"];
            $mdl->_country_name = $row["country_name"];
            $mdl->_created_date = $row["created_date"];
            $mdl->_created_by = $row["created_by"];
            $mdl->_modified_date = $row["modified_date"];
            $mdl->_modified_by = $row["modified_by"];
            $mdl->_transactionmode = 'U';
        }
    }

    public function checkCountryExists($countryName, $countryId = null) {
        global $connect;

        $sql = "SELECT COUNT(*) FROM tbl_country_master WHERE country_name = :country_name";
        if ($countryId) {
            $sql .= " AND country_id != :country_id";
        }

        $stmt = $connect->prepare($sql);
        $stmt->bindParam(':country_name', $countryName, PDO::PARAM_STR);
        if ($countryId) {
            $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function searchCountries() {
        global $connect;

        $sql = "SELECT * FROM tbl_country_master ORDER BY country_name ASC";
        return $connect->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteCountry($countryId) {
        global $connect;

        try {
            $sql = "DELETE FROM tbl_country_master WHERE country_id = :country_id";
            $stmt = $connect->prepare($sql);
            $stmt->bindParam(':country_id', $countryId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Delete failed: " . $e->getMessage());
            return false;
        }
    }
}
?>
<script>
   document.addEventListener("DOMContentLoaded", function () {
    let searchBox = document.getElementById("searchBox");
    if (searchBox) {
        searchBox.addEventListener("keyup", function () {
            let filter = searchBox.value.toLowerCase();
            document.querySelectorAll("#searchMaster tbody tr").forEach(row => {
                let countryName = row.querySelector("td:first-child").textContent.toLowerCase();
                row.style.display = countryName.includes(filter) ? "" : "none";
            });
        });
    }
});

</script>
