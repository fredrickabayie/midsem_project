<?php

function customerLogin()
{
//    require_once '../vendor/autoload.php';
    include_once '../models/Customer.php';

    if (isset($_POST['email']) && isset($_POST['password'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];

        $customer = new Customer();
        $result = $customer->customerLogin($email, $password);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if (!$row) {
            echo 'Failed to login';
        } else {
            $_SESSION['customer'] = $row;
            print_r($_SESSION['customer']);
            header('Location: ../controllers/ChairController.php?cmd=6&checkout');
        }
    } elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
        unset($_SESSION['customer']);
    }
}