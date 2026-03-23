<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "nepal_payslip";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {
        echo "Connection failed: " .$e->getMessage();
    }

    define("BASE_URL","/payslip/");
    define("HR_URL", BASE_URL . "HR/");
  
 