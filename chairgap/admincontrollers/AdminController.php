<?php


if (isset ($_REQUEST ['cmd'])) {
    session_start();

    $cmd = $_REQUEST['cmd'];

    switch ($cmd) {
        case 1:
            loadPages();
            break;

        case 2:
            adminLogin();
            break;

        case 3:
            displayOrders();
            break;

        case 4:
            updateOrders();
            break;

        case 5:
            manageProducts();
            break;

        default:
            echo '{"result":0,status:"unknown command"}';
            break;
    }//end of switch

}//end of if


function loadPages()
{
    require_once '../vendor/autoload.php';
    Twig_Autoloader::register();

    $loader = new Twig_Loader_Filesystem('../admin');
    $twig = new Twig_Environment($loader);

    if (isset($_GET['login'])) {
        $url = '/adminLogin.html.twig';
    } elseif (isset($_GET['dashboard'])) {
        $url = '/dashboard.html.twig';
    } else {
        $url = '/adminLogin.html.twig';
    }

    /** @var String $url */
    /** @var TYPE_NAME $totalPercentage */
    echo $twig->render($url, [
        'hey' => 'hey'
    ]);
}


function adminLogin()
{
//    require_once '../vendor/autoload.php';
    include_once '../models/Admin.php';

    if (isset($_POST['username']) && isset($_POST['password'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];

        $admin = new Admin();
        $result = $admin->adminLogin($username, $password);
        $row = $result->fetch_array(MYSQLI_ASSOC);

        if (!$row) {
            header('Location: ../admincontrollers/AdminController.php?cmd=1&login');
            echo 'Failed to login';
        } else {
            $_SESSION['admin'] = $row;
            print_r($_SESSION['admin']);
            header('Location: ../admincontrollers/AdminController.php?cmd=3');
        }
    } elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
        unset($_SESSION['customer']);
        header('Location: ../admincontrollers/ChairController.php?cmd=1&checkout');
    }
}


function displayOrders()
{
    include_once '../models/Admin.php';
    require_once '../vendor/autoload.php';

    Twig_Autoloader::register();
    $admin = new Admin();

    $loader = new Twig_Loader_Filesystem('../admin');
    $twig = new Twig_Environment($loader);

    $orders = '';
    foreach ($admin->displayOrders() as $order) {
        $orders[] = $order;
    }

    echo $twig->render('dashboard.html.twig', [
        'orders' => $orders
    ]);

}

function updateOrders()
{
    include_once '../models/Admin.php';
    require_once '../vendor/autoload.php';

    Twig_Autoloader::register();
    $admin = new Admin();

    $loader = new Twig_Loader_Filesystem('../admin');
    $twig = new Twig_Environment($loader);

    if (isset($_GET['order_id']) && isset($_GET['cust_email'])) {
        $order_id = $_GET['order_id'];
        $cust_email = $_GET['cust_email'];
        $amount = $_GET['amount'];

        $result = $admin->updateOrder($order_id);
        if ($result) {
            header('Location: ../admincontrollers/AdminController.php?cmd=3');
            sendMail($cust_email, $cust_email, $amount, $order_id);
        }
    }

}


function manageProducts()
{
    include_once '../models/Admin.php';
    require_once '../vendor/autoload.php';

    Twig_Autoloader::register();
    $admin = new Admin();

    $loader = new Twig_Loader_Filesystem('../admin');
    $twig = new Twig_Environment($loader);

    $orders = '';
    foreach ($admin->displayChairs(1,10) as $chair) {
        $chairs[] = $chair;
    }

    echo $twig->render('manage.html.twig', [
        'chairs' => $orders
    ]);
}


//Function to sendMail to a customer
function sendMail($cust_email, $cust_name, $amount, $order_id)
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
    $mail->Subject = 'ChairGap Payment Confirmation';
    $mail->msgHTML('You have made payment of $' . $amount . ' for order number #' . $order_id . '. Thank you for shopping at Chairgap');
    $mail->AltBody = 'This is a plain-text message body';
//    $mail->addAttachment('images/phpmailer_mini.png');
    if (!$mail->send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        echo "Message sent!";
    }
}