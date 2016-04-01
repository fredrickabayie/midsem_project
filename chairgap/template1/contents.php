<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>
        ChairGap
    </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div align="center">
        <a href="https://github.com/PHPMailer/PHPMailer/">
            <img style="padding: 10px" src="../assets/images/home/logo.png" height="50" width="120" alt="ChairGap Logo">
        </a>
    </div>
    <div class="panel panel-success">

        <div class="panel-heading">
            Transaction Receipt
        </div>
        <div class="panel-body">
            <p>
                You made an order for the following items on
                <a href="../controllers/ChairController.php?cmd=1&shop" target="_blank">
                    ChairGap
                </a>
                the number ecommerce stop shop for your affordable chairs. These items will
                be delivered to the address <code>1st University Avenue, Berekuso</code>. The goods will be delivered
                 in <kbd>2 days</kbd>. Payment should be made when the items are delivered.
            </p>
        </div>

        <!-- Table -->
        <table class="table table-bordered">
            <thead>
            <tr>
            <th>Item</th>
            <th>Item Name</th>
            <th>Price ($)</th>
            <th>Quantity</th>
            <th>Total ($)</th>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach ($_SESSION['cart'] as $key => $value) {
                echo '<tr>';
                echo '<td>' . $key . '</td>';
                echo '<td>' . $value['name'] . '</td>';
                echo '<td>' . $value['cost'] . '</td>';
                echo '<td>'.$value['quantity'] .'</td>';
                echo '<td>' . $value['total'] . '</td>';
                echo '</tr>';
            }
            ?>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="3">&nbsp;</td>
                <td colspan="2">
                    <table class="table table-condensed total-result">
                        <tr>
                            <td>Cart Sub Total</td>
                            <td>$  674674</td>
                        </tr>
                        <tr>
                            <td>Item Discount</td>
                            <td>10%</td>
                        </tr>
                        <tr class="shipping-cost">
                            <td>Shipping Cost</td>
                            <td>Free</td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td class="success">
                                <span>$  475845</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
<!--                <tr>-->
<!--                    <td></td>-->
<!--                    <td></td>-->
<!--                    <td></td>-->
<!--                    <td class="active">Cart Sub Total</td>-->
<!--                    <td class="success">$ 84756748</td>-->
<!--                </tr>-->
            </tfoot>
        </table>
    </div>
<!--    <div class="alert alert-danger" role="alert">-->
<!--        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>-->
<!--        <span class="sr-only">Error:</span>-->
<!--        Enter a valid email address-->
<!--    </div>-->
</div>
</body>
</html>
