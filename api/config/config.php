<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nepal_payslip";

    try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        error_log("DB Connection failed: " . $e->getMessage());
        $conn = null;
    }

    define("BASE_URL","/payslip/");
    define("HR_URL", BASE_URL . "HR/");
  
 