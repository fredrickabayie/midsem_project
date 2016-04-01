<?php

/**
 * Created by PhpStorm.
 * User: fredrickabayie
 * Date: 24/01/2016
 * Time: 23:15
 */

include_once 'Adb.php';

/**
 * Class admin
 */
class Admin extends Adb
{

    public function __destruct()
    {
        parent::__destruct();
    }

    /**
     * Function login
     *
     * Authenticates users to be able to access
     * the admin page in order to manipulate the
     * wine data
     *
     * @param $email
     * @param $password
     * @return bool|mysqli_result
     */
    public function adminLogin($email, $password)
    {
        $loginQuery = "SELECT
                          `users`.`user_id`,
                          `users`.`password`,
                          `users`.`username`
                        FROM `users`
                        WHERE `users`.`username` = ?
                              AND `users`.`password` = ?
                        LIMIT 1";

        if ($statement = $this->prepare($loginQuery))
            $statement->bind_param("ss", $email, $password);
        $statement->execute();
        return $statement->get_result();
    }

    /**
     * Function displayChair
     *
     * Display all the wines in the database
     * by displaying the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $limit
     * @param $offset
     * @return bool|mysqli_result
     */
    public function displayOrders()
    {
        $chairQuery = "SELECT
                          `orders`.*,
                          `payments`.`payment_status`,
                          `customers`.`email`,
                          `customers`.`surname`,
                          `customers`.`firstname`
                        FROM `orders`
                          JOIN `payments`
                          INNER JOIN `customers`
                            ON `orders`.`cust_id` = `customers`.`cust_id`
                        AND `orders`.`payment_id` = `payments`.`payment_id`
                        WHERE `orders`.`payment_id` = '2'
                        ORDER BY `orders`.`date` ASC";

        if ($statement = $this->prepare($chairQuery)) {
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }


    /**
     * @param $order_id
     * @return bool|mysqli_result
     */
    public function updateOrder($order_id)
    {
        $updateOrderQuery = "UPDATE chairgap.orders
                                SET payment_id = 1
                                WHERE order_id = ?";

        if ($statement = $this->prepare($updateOrderQuery)) {
            $statement->bind_param("i", $order_id);
            return $statement->execute();
        }
//        $statement->close();
//        return false;
    }


    /**
     * @param $cust_id
     * @return bool|mysqli_result
     */
    public function getCustomer($cust_id)
    {
        $getCustomerQuery = "SELECT 
                                `customers`.`surname`, 
                                `customers`.`firstname`,
                                `customers`.`email`
                             FROM `customers`
                             WHERE `customers`.`cust_id` = ?";

        if ($statement = $this->prepare($getCustomerQuery)) {
            $statement->bind_param("i", $cust_id);
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }


    /**
     * Function displayChair
     *
     * Display all the wines in the database
     * by displaying the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $limit
     * @param $offset
     * @return bool|mysqli_result
     */
    public function displayChairs($limit, $offset)
    {
        $chairQuery = /** @lang MySQL */
            <<<DISPLAYCHAIRS
            SELECT
              `chairs`.`chair_id`,
              `chair_types`.`chair_type`,
              `chairs`.`chair_name`,
              `manufacturers`.`manufacturer_name`,
              `inventories`.`cost`,
              `chairs`.`year`,
              `inventories`.`on_hand`
            FROM chairgap.`chairs`
              JOIN chairgap.`chair_types`
              JOIN chairgap.`manufacturers`
              JOIN chairgap.`inventories`
                ON `chairs`.`chair_type` = `chair_types`.`chair_type_id`
                   AND `chairs`.`manufacturer_id` = `manufacturers`.`manufacturer_id`
                   AND `chairs`.`chair_id` = `inventories`.`chair_id`
            ORDER BY `chairs`.`chair_id` ASC
            LIMIT ?, ?
DISPLAYCHAIRS;

        if ($statement = $this->prepare($chairQuery)) {
            $statement->bind_param("ss", $limit, $offset);
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }
}