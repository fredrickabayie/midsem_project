<?php

print_r($_SESSION['cart']);
echo '<br>';
/**
 * Controls the shopping cart
 */

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $chair_id = intval(isset($_GET['chair_id']) ? $_GET['chair_id'] : 0);

    switch ($action) {
        case 'add':
            if (!isset($_SESSION['cart'][$chair_id])) {

                include_once '../models/Chair.php';
                $cartChair = new Chair();

                $result = $cartChair->selectChair($chair_id);
                if ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    $_SESSION['cart'][$chair_id] = [
                        'id' => $row['chair_id'],
                        'name' => $row['chair_name'],
                        'cost' => $row['cost'],
                        'quantity' => 1,
                    ];
//                    echo 'Added';
                    print_r($_SESSION['cart']);
                }
            } else {
                echo 'Item already added to cart';
            }
//            header('Location: test.php');
            break;

        case 'change':
            if (isset($_SESSION['cart'][$chair_id])) {
                echo $quantity = $_POST['quantity'];

                include_once '../models/Chair.php';
                $cartChair = new Chair();

                $result = $cartChair->selectChair($chair_id);
                if ($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    if ($quantity < $row['on_hand'] && $quantity > 0) {
                        $_SESSION['cart'][$chair_id]['quantity'] = $quantity;

                        $_SESSION['cart'][$chair_id] = [
                            'id' => $row['chair_id'],
                            'name' => $row['chair_name'],
                            'cost' => $row['cost'] * $_SESSION['cart'][$chair_id]['quantity'],
                            'quantity' => $_SESSION['cart'][$chair_id]['quantity']
                        ];
                    } else {
                        echo 'Available In stock is ' . $row['on_hand'];
                    }

                } else {
                    echo 'Item does not exist in the database';
                }
            }
            displayWines();
            break;

        case 'increase':
            if (isset($_SESSION['cart'][$chair_id])) {

                include_once '../models/Chair.php';
                $cartChair = new Chair();

                $result = $cartChair->selectChair($chair_id);
                if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $_SESSION['cart'][$chair_id]['quantity']++;

                    $_SESSION['cart'][$chair_id] = [
                        'id' => $row['chair_id'],
                        'name' => $row['chair_name'],
                        'cost' => $row['cost'] * $_SESSION['cart'][$chair_id]['quantity'],
                        'quantity' => $_SESSION['cart'][$chair_id]['quantity']
                    ];
                } else {
                    echo 'Item does not exist in the database';
                }
            }
            displayWines();
            break;

        case 'decrease':
            if (isset($_SESSION['cart'][$chair_id]) && intval($_SESSION['cart'][$chair_id]['quantity']) > 0) {

                include_once '../models/Chair.php';
                $cartChair = new Chair();

                $result = $cartChair->selectChair($chair_id);
                if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                    $_SESSION['cart'][$chair_id]['quantity']--;

                    $_SESSION['cart'][$chair_id] = [
                        'id' => $row['chair_id'],
                        'name' => $row['chair_name'],
                        'cost' => $row['cost'] * $_SESSION['cart'][$chair_id]['quantity'],
                        'quantity' => $_SESSION['cart'][$chair_id]['quantity']
                    ];
                } else {
                    echo 'Item not in the database';
                }
            } else {
                unset($_SESSION['cart'][$chair_id]);
            }
            displayWines();
            break;

        case 'remove':
            if (isset($_SESSION['cart'][$chair_id])) {
                unset($_SESSION['cart'][$chair_id]);
            }
            displayWines();
            break;

        case 'empty':
            unset($_SESSION['cart']);
//            displayWines();
            break;

        default:
            displayWines();
            break;
    }
}