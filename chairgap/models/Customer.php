<?php

/**
 * Created by PhpStorm.
 * User: fredrickabayie
 * Date: 22/01/2016
 * Time: 00:38
 */

include_once 'Adb.php';

/**
 * Class Wine
 *
 */
class Customer extends Adb
{

    /**
     *
     */
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
    public function customerLogin($email, $password)
    {
        $loginQuery = "SELECT
                          `customers`.`email`,
                          `customers`.`password`,
                          `customers`.`cust_id`,
                          `customers`.`title_id`,
                          `customers`.`surname`,
                          `customers`.`firstname`,
                          `customers`.`phone`,
                          `customers`.address
                        FROM `customers`
                        WHERE `customers`.`email` = ?
                              AND `customers`.`password` = ?
                        LIMIT 1";

        if ($statement = $this->prepare($loginQuery))
            $statement->bind_param("ss", $email, $password);
        $statement->execute();
        return $statement->get_result();
    }


    /**
     * Function customerSignUp
     *
     * Adding new wine to the database.
     * The id, name, type, year, image, winery id,
     * and description of the wine is added.
     * @param $surname
     * @param $firstname
     * @param $title_id
     * @param $address
     * @param $phone
     * @param $email
     * @param $password
     * @return bool
     */
    public function customerSignUp($surname, $firstname, $title_id, $address, $phone, $email, $password)
    {
        $customerSignUpQuery = "INSERT INTO `chairgap`.`customers` 
                                (surname, firstname, title_id, address, phone, email, password) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";

        if ($statement = $this->prepare($customerSignUpQuery)) {
            $statement->bind_param("sssssss", $surname, $firstname, $title_id, $address, $phone, $email, $password);
            return $statement->execute();
        }
        $statement->close();
        return false;
    }


    /**
     * Function customerUpdate
     *
     * Adding new wine to the database.
     * The id, name, type, year, image, winery id,
     * and description of the wine is added.
     * @param $cust_id
     * @param $surname
     * @param $firstname
     * @param $title_id
     * @param $address
     * @param $phone
     * @param $email
     * @param $password
     * @return bool
     */
    public function customerUpdate($cust_id, $surname, $firstname, $title_id, $address, $phone, $email, $password)
    {
        $customerUpdateQuery = "UPDATE `chairgap`.`customers` 
                                SET surname = ?, firstname = ?, title_id = ?, address = ?, phone = ?, email = ?, password = ? 
                                WHERE cust_id = ?";

        if ($statement = $this->prepare($customerUpdateQuery)) {
            $statement->bind_param("ssssssss", $surname, $firstname, $title_id, $address, $phone, $email, $password, $cust_id);
            return $statement->execute();
        }
        $statement->close();
        return false;
    }


    /**
     * Function purchaseHistory
     *
     * Display all the wines in the database
     * by displaying the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $cust_id
     * @return bool|mysqli_result
     */
    public function purchaseHistory($cust_id)
    {
        $chairQuery = "SELECT
                          `chairs`.`chair_name`,
                          `chairs`.`chair_id`,
                          `items`.`order_id`,
                          `items`.`quantity`,
                          `items`.`price`,
                          `orders`.`date`
                        FROM `items`
                          JOIN `chairs`
                          INNER JOIN `orders`
                            ON `items`.`chair_id` = `chairs`.`chair_id`
                               AND `orders`.`order_id` = `items`.`order_id`
                        WHERE `items`.`cust_id` = ?
                        ORDER BY `orders`.`date` DESC";

        if ($statement = $this->prepare($chairQuery)) {
            $statement->bind_param("s", $cust_id);
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }

}

//$chair = new Customer();
//foreach ($chair->purchaseHistory(1) as $row) {
//    print_r($row);
//    echo '<br>';
//}