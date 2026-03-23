<?php
    require_once('../config/config.php');

    if(isset($_POST['']))
{
    $name = isset($_POST['name']) ? $_POST['name'] :'';
    $email = isset($_POST['email']) ? $_POST['email'] :'';
    $password = isset($_POST['password']) ? $_POST['password'] :'';
    $staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if($name == '' || $email == '' || $password == ''){
        header('location: ../index.php?error=All fields are required');
        return false;
    }
}