<?php

include_once '../errors/ErrorHandler.php';

echo $_GET['hey'];

if (isset ($_REQUEST ['cmd'])) {
    session_start();

    $cmd = $_REQUEST['cmd'];

    switch ($cmd) {
        case 1:
            ManageChairs();
            break;

        case 2:
            customerLogin();
            break;

        case 3:
            checkout();
            break;

        case 4:
            login();
            break;

        case 5:
            wineDetail();
            break;

        case 6:
            ManageCart();
            break;

        case 7:
            customerLogin();
            break;

        case 8:
            insertItems();
            break;

        case 9:
            printPdf();
            break;

        case 10:
            searchChair();
            break;

        case 11:
            customerSignUp();
            break;

        case 12:
            customerDetail();
            break;

        case 13:
            customerDetailsUpdate();
            break;

        case 14:
            customerPurchaseHistory();
            break;

        case 15:
            pageNotFound();
            break;

        default:
            echo '{"result":0,status:"unknown command"}';
            break;
    }//end of switch

}//end of if


function customerLogin()
{
//    require_once '../vendor/autoload.php';
    include_once '../models/Customer.php';

    if (isset($_POST['email']) && isset($_POST['password'])) {

        $email = $_POST['email'];
//        filter_var($email, FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'];

        $customer = new Customer();
        $result = $customer->customerLogin($email, $password);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if (!$row) {
            header('Location: ../controllers/ChairController.php?cmd=1&checkout');
            echo 'Failed to login';
        } else {
            $_SESSION['customer'] = $row;
            print_r($_SESSION['customer']);
            header('Location: ../controllers/ChairController.php?cmd=1&checkout');
        }
    } elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
        unset($_SESSION['customer']);
        header('Location: ../controllers/ChairController.php?cmd=1&checkout');
    }
}


//Function to handle customer signup
function customerSignUp() {
    include_once '../models/Customer.php';

    if (isset($_POST['surname']) && isset($_POST['firstname']) &&
        isset($_POST['email']) && isset($_POST['password'])) {

        $surname = $_POST['surname'];
        $firstname = $_POST['firstname'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $customer = new Customer();
        $result = $customer->customerSignUp($surname, $firstname, "3", "", "", $email, $password);

        if (!$result) {
            echo 'Failed to signUp';
//            header('Location: ../controllers/ChairController.php?cmd=1&checkout');
        } else {
            echo 'SignUp Successful';
            header('Location: ../controllers/ChairController.php?cmd=1&checkout');
        }
    }
}


function customerDetailsUpdate() {
    include_once '../models/Customer.php';
//    echo "In ok";
    $surname = $_POST['surname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $title = $_POST['title_id'];
    $cust_id = $_SESSION['customer']['cust_id'];

    $customer = new Customer();
    $result = $customer->customerUpdate($cust_id, $surname, $firstname, $title, $address, $phone, $email, $password);

    if (!$result) {
        echo 'Failed to update your details';
    } else {
        $result = $customer->customerLogin($email, $password);
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $_SESSION['customer'] = $row;
//        echo 'Profile Update Successful';
        header('Location: ../controllers/ChairController.php?cmd=12');
    }
}

//Function to edit customer details
/**
 *
 */
function customerDetail() {
    include_once '../models/Customer.php';

    $totalCost = 0;
    $totalQuantity = 0;

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $quantity) {
            $totalCost += $_SESSION['cart'][$key]['total'];
            $totalQuantity += $_SESSION['cart'][$key]['quantity'];
        }
    }

    /** @var array $data */
    require_once '../vendor/autoload.php';

    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('../template1');
    $twig = new Twig_Environment($loader);

    if (isset($_SESSION['customer'])) {
        $url = '/profile.html.twig';
    } else {
        $url = '/login.html.twig';
    }

    /** @var String $url */
    echo $twig->render($url, [
        'carts' => isset($_SESSION['cart']) ? $_SESSION['cart'] : "",
        'totalCost' => $totalCost,
        'totalQuantity' => $totalQuantity,
        'customer' => isset($_SESSION['customer']) ? $_SESSION['customer'] : ''
    ]);
}


function customerPurchaseHistory () {
    include_once '../models/Customer.php';

    $data = '';
    $totalCost = 0;
    $items = 0;
    $cust_id = $_SESSION['customer'] ? $_SESSION['customer']['cust_id'] : '';

    $customer = new Customer();
    if ($result = $customer->purchaseHistory($cust_id)) {

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $data[] = $row;
            $totalCost += $row['price'];
            $items ++;
        }
    }

    $totalQuantity = 0;

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $quantity) {
//            $totalCost += $_SESSION['cart'][$key]['total'];
            $totalQuantity += $_SESSION['cart'][$key]['quantity'];
        }
    }

    /** @var array $data */
    require_once '../vendor/autoload.php';

    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('../template1');
    $twig = new Twig_Environment($loader);

    if (isset($_SESSION['customer'])) {
        $url = '/history.html.twig';
    } else {
        $url = '/login.html.twig';
    }

    /** @var String $url */
    /** @var array $data */
    echo $twig->render($url, [
        'chairHistory' => $data,
        'carts' => isset($_SESSION['cart']) ? $_SESSION['cart'] : "",
        'totalCost' => $totalCost,
        'totalQuantity' => $totalQuantity,
        'items' => $items,
        'customer' => isset($_SESSION['customer']) ? $_SESSION['customer'] : ''
    ]);
}

//Function to manage the cart
function ManageCart()
{
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
                            'total' => $row['cost'],
                            'quantity' => 1,
                        ];
                    }
                } else {
                    echo 'Item already added to cart';
                }
                ManageChairs();
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
                                'cost' => $row['cost'],
                                'total' => $row['cost'] * $_SESSION['cart'][$chair_id]['quantity'],
                                'quantity' => $_SESSION['cart'][$chair_id]['quantity']
                            ];
                        } elseif ($quantity <= 0) {
                            unset($_SESSION['cart'][$chair_id]);
                        } else {
                            echo 'Available In stock is ' . $row['on_hand'];
                        }

                    } else {
                        echo 'Item does not exist in the database';
                    }
                }
                ManageChairs();
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
                            'cost' => $row['cost'],
                            'total' => $row['cost'] * $_SESSION['cart'][$chair_id]['quantity'],
                            'quantity' => $_SESSION['cart'][$chair_id]['quantity']
                        ];
                    } else {
                        echo 'Item does not exist in the database';
                    }
                }
                ManageChairs();
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
                            'cost' => $row['cost'],
                            'total' => $row['cost'] * $_SESSION['cart'][$chair_id]['quantity'],
                            'quantity' => $_SESSION['cart'][$chair_id]['quantity']
                        ];
                    } else {
                        echo 'Item not in the database';
                    }
                } else {
                    unset($_SESSION['cart'][$chair_id]);
                }
                ManageChairs();
                break;

            case 'remove':
                if (isset($_SESSION['cart'][$chair_id])) {
                    unset($_SESSION['cart'][$chair_id]);
                }
                ManageChairs();
                break;

            case 'empty':
                unset($_SESSION['cart']);
                ManageChairs();
                break;

            default:
                ManageChairs();
                break;
        }
    }
}


function checkout()
{
    if (isset($_SESSION['customer'])) {
        echo 'checkout page';
    } else {
        header('Location: ../controllers/Shoppingcart.php?cmd=2');
    }
}


/**
 * Function to display all wines
 */
function ManageChairs()
{
    $num_perPage = 9;
    if (isset($_REQUEST['page'])) {
        $page = $_REQUEST['page'];
    } else {
        $page = 1;
    }

    $start_from = ($page - 1) * $num_perPage;

    include_once '../models/Chair.php';
    $chair = new Chair();

//    if (isset())
    if ($result = $chair->displayChairs($start_from, $num_perPage)) {

        $num = $chair->countChairs();
        $total = $num->fetch_assoc();
        $total_chairs = $total["chair_id"];

        $total_pages = ceil($total_chairs / $num_perPage);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $data[] = $row;
        }
    }

    $totalCost = 0;
    $totalQuantity = 0;
    $totalPercentage = 0;

    $chair_id = intval(isset($_GET['chair_id']) ? $_GET['chair_id'] : 0);

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $quantity) {
            $totalCost += $_SESSION['cart'][$key]['total'];
            $totalQuantity += $_SESSION['cart'][$key]['quantity'];
        }
        if ($totalQuantity > 10) {
            $totalPercentage = (10 / 100) * $totalCost;
            $totalPercentage = $totalCost - $totalPercentage;
        } else {
            $totalPercentage = $totalCost;
        }
    }

    /** @var TYPE_NAME $data */
    /** @var TYPE_NAME $total_chairs */
    /** @var TYPE_NAME $total_pages */
//    twiggg('/shop.html.twig', $data, $total_chairs, $page, $totalCost, $totalQuantity, $total_pages);
    require_once '../vendor/autoload.php';
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('../template1');
    $twig = new Twig_Environment($loader);

    if (isset($_GET['shop'])) {
        $url = '/shop.html.twig';
    } elseif (isset($_GET['cart'])) {
        $url = '/cart.html.twig';
    } elseif (isset($_GET['checkout'])) {
        if (isset($_SESSION['customer'])) {
            $url = '/checkout.html.twig';
        } else {
            $url = '/login.html.twig';
        }
    } elseif (isset($_GET['confirmation'])) {
        $url = '/confirm.html.twig';
    } else {
        $url = '/index.html.twig';
    }

//elseif (isset($_GET[''])) {
//    $url = '/profile.html.twig';
//}

    /** @var String $url */
    /** @var TYPE_NAME $totalPercentage */
    echo $twig->render($url, [
        'chairs' => $data,
        'totalChairs' => $total_chairs,
        'page' => $page,
        'totalPercentage' => $totalPercentage,
        'totalPages' => $total_pages,
        'carts' => isset($_SESSION['cart']) ? $_SESSION['cart'] : "",
        'totalCost' => $totalCost,
        'totalQuantity' => $totalQuantity,
        'customer' => isset($_SESSION['customer']) ? $_SESSION['customer'] : ''
    ]);
}


function insertItems()
{
    if (isset($_SESSION['cart'])) {
        include_once '../models/Chair.php';
        $chair = new Chair();

        if (isset($_SESSION['cart']) && isset($_SESSION['customer'])) {
            $cust_id = $_SESSION['customer']['cust_id'];
            $cust_email = $_SESSION['customer']['email'];
            $cust_name = $_SESSION['customer']['firstname'] ." " . $_SESSION['customer']['surname'];
            $order_id = rand(100, 999);

            $amount = '';
            /** @var bool $items */
            foreach ($_SESSION['cart'] as $key => $value) {
                $items = $chair->insertItems($cust_id, $order_id, $key, $value['quantity'], $value['total']);
                $amount += $value['total'];
            }

            if ($items) {
                $instructions = $_POST['instructions'];
                $orders = $chair->insertOrder($order_id, $cust_id, $instructions, '2', $amount);
                if ($orders) {
                    unset($_SESSION['cart']);
                    sendMail($cust_email, $cust_name);
                    header('Location: ../controllers/ChairController.php?cmd=1&confirmation');
                } else {
                    header('Location: ../controllers/ChairController.php?cmd=1&checkout');
                    echo "Failed to insert orders";
                }
            } else {
                header('Location: ../controllers/ChairController.php?cmd=1&checkout');
                echo "Failed to insert items";
            }
        }
    }
}


//Function to sendMail to a customer
function sendMail($cust_email, $cust_name)
{
    date_default_timezone_set('Etc/UTC');
    require '../vendor/autoload.php';

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Debugoutput = 'html';
    $mail->Host = "smtp.office365.com";
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = "fredrick.abayie@ashesi.edu.gh";
    $mail->Password = "Goldbar77";
    $mail->setFrom('fredrick.abayie@ashesi.edu.gh', 'ChairGap');
    $mail->addAddress($cust_email, $cust_name);
    $mail->Subject = 'ChairGap Acceptance Order';
    $mail->msgHTML(file_get_contents('../template1/contents.php'), dirname(__FILE__));
    $mail->AltBody = 'This is a plain-text message body';
//    $mail->addAttachment('images/phpmailer_mini.png');
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }
}

//Function to generate a report for the transaction for a customer
function printPdf()
{
    require '../vendor/autoload.php';

    $pdf = new FPDF();
    // Colors, line width and bold font
    $pdf->AddPage();
    $pdf->Image('../assets/images/home/logo.png', 90,6,30);
    $pdf->SetFillColor(255, 0, 0);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(128, 0, 0);
    $pdf->SetLineWidth(.3);
    $pdf->SetFont('Arial', 'B', 14);
//    $pdf->SetFont('','B');
    // Header

    $pdf->Ln(10);
    $header = array('Item', 'Name', 'Cost', 'Quantity', 'Price');
    $w = array(35, 60, 30, 30, 35);
    for ($i = 0; $i < count($header); $i++)
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    $pdf->Ln();
    // Color and font restoration
    $pdf->SetFillColor(224, 235, 255);
    // Data
    $pdf->SetTextColor(0);
    /** @var TYPE_NAME $totalCost */
    $totalCost = 0;
    $totalQuantity = 0;
    $fill = false;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $value) {
            $totalQuantity += $_SESSION['cart'][$key]['quantity'];
            $totalCost += $_SESSION['cart'][$key]['total'];
//            if ($totalQuantity > 10) {
//                $totalPercentage = (10 / 100) * $totalCost;
//                $totalPercentage = $totalCost - $totalPercentage;
//            } else {
//                $totalPercentage = $totalCost;
//            }
            $pdf->Cell($w[0], 6, $value['id'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[1], 6, $value['name'], 'LR', 0, 'L', $fill);
            $pdf->Cell($w[2], 6, $value['cost'], 'LR', 0, 'R', $fill);
            $pdf->Cell($w[3], 6, number_format($value['quantity']), 'LR', 0, 'R', $fill);
            $pdf->Cell($w[4], 6, $value['total'], 'LR', 0, 'R', $fill);
            $pdf->Ln();
            $fill = !$fill;
        }
        if ($totalQuantity > 10) {
            $total = $totalCost;
            $totalCost = (10 / 100) * $totalCost;
            $totalCost = $total - $totalCost;
        } else {
            $totalCost = $totalCost;
        }
        $pdf->Cell($w[0], 6, 'Cart Sub Total', 'LR', 0, 'R', $fill);
        $pdf->Cell($w[4], 6, $totalCost, 'LR', 0, 'R', $fill);
    }

    $pdf->Cell(array_sum($w), 0, '', 'T');
    $pdf->Output('transaction.pdf', 'I');
}



//Function to search for a chair in the database
function searchChair() {

    $num_perPage = 9;
    if (isset($_REQUEST['page'])) {
        $page = $_REQUEST['page'];
    } else {
        $page = 1;
    }

    $start_from = ($page - 1) * $num_perPage;

    include_once '../models/Chair.php';
    $chair = new Chair();

    if (isset($_GET['category'])) {
        $category = $_GET['category'];

        if ($result = $chair->sortChair($category)) {

            $num = $chair->countChairs();
            $total = $num->fetch_assoc();
            $total_chairs = $total["chair_id"];

            $total_pages = ceil($total_chairs / $num_perPage);

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $data[] = $row;
            }
        }

    } else {
        $searchWord = $_POST['searchWord'];

        if ($result = $chair->searchChair($searchWord)) {

            $num = $chair->countChairs();
            $total = $num->fetch_assoc();
            $total_chairs = $total["chair_id"];

            $total_pages = ceil($total_chairs / $num_perPage);

            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $data[] = $row;
            }
        }
    }

    $totalCost = 0;
    $totalQuantity = 0;

    $chair_id = intval(isset($_GET['chair_id']) ? $_GET['chair_id'] : 0);

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $quantity) {
            $totalCost += $_SESSION['cart'][$key]['total'];
            $totalQuantity += $_SESSION['cart'][$key]['quantity'];
        }
    }

    /** @var TYPE_NAME $data */
    /** @var TYPE_NAME $total_chairs */
    /** @var TYPE_NAME $total_pages */
//    twiggg('/shop.html.twig', $data, $total_chairs, $page, $totalCost, $totalQuantity, $total_pages);
    require_once '../vendor/autoload.php';
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('../template1');
    $twig = new Twig_Environment($loader);

    if (isset($_POST['searchWord'])) {
        $url = '/shop.html.twig';
    } elseif (isset($_GET['category'])) {
        $url = "/shop.html.twig";
    }

    /** @var String $url */
    echo $twig->render($url, [
        'chairs' => $data,
        'totalChairs' => $total_chairs,
        'page' => $page,
        'totalPages' => $total_pages,
        'carts' => isset($_SESSION['cart']) ? $_SESSION['cart'] : "",
        'totalCost' => $totalCost,
        'totalQuantity' => $totalQuantity,
        'customer' => isset($_SESSION['customer']) ? $_SESSION['customer'] : ''
    ]);
}

//printPdf();


function pageNotFound () {
    $totalQuantity = 0;

    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $quantity) {
            $totalQuantity += $_SESSION['cart'][$key]['quantity'];
        }
    }

    /** @var array $data */
    require_once '../vendor/autoload.php';

    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('../template1');
    $twig = new Twig_Environment($loader);

    $url = '/404.html.twig';

    /** @var String $url */
    /** @var array $data */
    echo $twig->render($url, [
        'carts' => isset($_SESSION['cart']) ? $_SESSION['cart'] : "",
        'totalQuantity' => $totalQuantity,
        'customer' => isset($_SESSION['customer']) ? $_SESSION['customer'] : ''
    ]);
}






































/**
 * Function to display all wines
 */
function wineDetail()
{

    if (isset($_GET['wine_id'])) {
        include_once '../models/Wine.php';

        $wine_id = $_GET['wine_id'];
        $wine = new Wine();

        if ($result = $wine->wineDetail($wine_id)) {
            $row = $result->fetch_assoc();

            twiggg('/details.html.twig', $row);
        }
    }
}


/**
 * Function to search for a task
 */
function searchWine()
{
    if (isset ($_REQUEST ['searchWord'])) {
        include_once '../models/Wine.php';
        $wine = new Wine ();

        $searchWord = $_REQUEST ['searchWord'];

        if ($result = $wine->searchWine($searchWord)) {
            $row = $result->fetch_assoc();
            echo '{"result":1, "wines": [';
            while ($row) {
                echo '{"wine_id": "' . $row ["wine_id"] . '", "wine_name": "' . $row ["wine_name"] . '",
            "winery_name": "' . $row ["winery_name"] . '", "cost": "' . $row ["cost"] . '",
            "wine_type": "' . $row["wine_type"] . '", "year": "' . $row["year"] . '"}';

                if ($row = $result->fetch_assoc()) {
                    echo ',';
                }
            }
            echo ']}';
        } else {
            echo '{"result":0,"status": "An error occurred for select product."}';
        }
    }
}//end of search_task()


/**
 * Function to display all tasks
 */
function displayWineTypes()
{
    include_once '../models/Wine.php';
    $wine = new Wine ();

    if ($result = $wine->wineType()) {
        $row = $result->fetch_assoc();
        echo '{"result":1, "wineType": [';
        while ($row) {
            echo '{"wine_type_id": "' . $row ["wine_type_id"] . '", "wine_type": "' . $row ["wine_type"] . '"}';

            if ($row = $result->fetch_assoc()) {
                echo ',';
            }
        }
        echo ']}';
    } else {
        echo '{"result":0,"status": "An error occurred for display wines."}';
    }
}


/**
 * Function to display all tasks
 */
function displayWineByType()
{
    if (isset ($_REQUEST ['wineType'])) {
        include_once '../models/Wine.php';
        $wine = new Wine ();

        $wineType = $_REQUEST ['wineType'];

        if ($result = $wine->displayWineByType($wineType)) {
            $row = $result->fetch_assoc();
            echo '{"result":1, "wines": [';
            while ($row) {
                echo '{"wine_id": "' . $row ["wine_id"] . '", "wine_name": "' . $row ["wine_name"] . '",
            "winery_name": "' . $row ["winery_name"] . '", "cost": "' . $row ["cost"] . '",
            "wine_type": "' . $row["wine_type"] . '", "year": "' . $row["year"] . '"}';

                if ($row = $result->fetch_assoc()) {
                    echo ',';
                }
            }
            echo ']}';
        } else {
            echo '{"result":0,"status": "An error occurred for display wines."}';
        }
    }
}//end of display_all_tasks()


/**
 * Function to display all sorted wines by cost in
 * descending order
 */
function sortWineDesc()
{
    include_once '../models/Wine.php';
    $wine = new Wine ();

    if ($result = $wine->sortWinePriceDesc()) {
        $row = $result->fetch_assoc();
        echo '{"result":1, "sortWines": [';

        while ($row) {
            echo '{"wine_id": "' . $row ["wine_id"] . '", "wine_name": "' . $row ["wine_name"] . '",
            "winery_name": "' . $row ["winery_name"] . '", "cost": "' . $row ["cost"] . '",
            "wine_type": "' . $row["wine_type"] . '", "year": "' . $row["year"] . '"}';

            if ($row = $result->fetch_assoc()) {
                echo ',';
            }
        }
        echo ']}';
    } else {
        echo '{"result":0,"status": "An error occurred for display wines."}';
    }
}


/**
 * Function to display all sorted wines by cost in
 * ascending order
 */
function sortWineAsc()
{
    include_once '../models/Wine.php';
    $wine = new Wine ();

    if ($result = $wine->sortWinePriceAsc()) {
        $row = $result->fetch_assoc();
        echo '{"result":1, "sortWines": [';

        while ($row) {
            echo '{"wine_id": "' . $row ["wine_id"] . '", "wine_name": "' . $row ["wine_name"] . '",
            "winery_name": "' . $row ["winery_name"] . '", "cost": "' . $row ["cost"] . '",
            "wine_type": "' . $row["wine_type"] . '", "year": "' . $row["year"] . '"}';

            if ($row = $result->fetch_assoc()) {
                echo ',';
            }
        }
        echo ']}';
    } else {
        echo '{"result":0,"status": "An error occurred for display wines."}';
    }
}


/**
 * Function to display all sorted wines by name
 */
function sortWineName()
{
    include_once '../models/Wine.php';
    $wine = new Wine ();

    if ($result = $wine->sortWineName()) {
        $row = $result->fetch_assoc();
        echo '{"result":1, "sortWines": [';

        while ($row) {
            echo '{"wine_id": "' . $row ["wine_id"] . '", "wine_name": "' . $row ["wine_name"] . '",
            "winery_name": "' . $row ["winery_name"] . '", "cost": "' . $row ["cost"] . '",
            "wine_type": "' . $row["wine_type"] . '", "year": "' . $row["year"] . '"}';

            if ($row = $result->fetch_assoc()) {
                echo ',';
            }
        }
        echo ']}';
    } else {
        echo '{"result":0,"status": "An error occurred for display wines."}';
    }
}

function login()
{
    if (isset ($_REQUEST['username']) & isset ($_REQUEST['password'])) {
        include_once '../models/Wine.php';
        $obj = new Wine ();
        $username = stripslashes($_REQUEST ['username']);
        $password = stripslashes($_REQUEST ['password']);
        $username = $obj->real_escape_string($username);
        $password = $obj->real_escape_string($password);

        $result = $obj->login($username, $password);
        $row = $result->fetch_assoc();

        if (!$row) {
            echo '{"result":0, "message":"Failed to login"}';
        } else {
            echo '{"result":1, "user_name":"' . $row['user_name'] . '"}';
        }

        $result->close();
    }

}




//sendMail();