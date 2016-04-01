<?php

/**
 * Created by PhpStorm.
 * User: fredrickabayie
 * Date: 22/01/2016
 * Time: 00:38
 */

include_once 'Adb.php';

/**
 * Chair
 *
 */
class Chair extends Adb
{

    /**
     * Chair Destructor
     */
    public function __destruct()
    {
        parent::__destruct();
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
            <<<TAG
                  SELECT
                    `chairgap`.`chairs`.`chair_id`,
                    `chairgap`.`chair_types`.`chair_type`,
                    `chairgap`.`chairs`.`chair_name`,
                    `chairgap`.`manufacturers`.`manufacturer_name`,
                    `chairgap`.`inventories`.`cost`,
                    `chairgap`.`chairs`.`year`,
                    `chairgap`.`inventories`.`on_hand`
                  FROM `chairgap`.`chairs`
                    JOIN `chairgap`.`chair_types`
                    JOIN `chairgap`.`manufacturers`
                    JOIN `chairgap`.`inventories`
                      ON `chairgap`.`chairs`.`chair_type` = `chairgap`.`chair_types`.`chair_type_id`
                         AND `chairgap`.`chairs`.`manufacturer_id` = `chairgap`.`manufacturers`.`manufacturer_id`
                         AND `chairgap`.`chairs`.`chair_id` = `chairgap`.`inventories`.`chair_id`
                  ORDER BY `chairgap`.`chairs`.`chair_id` ASC
                  LIMIT ?, ?
TAG;

        /** @var mysqli $this */
        if ($statement = $this->prepare($chairQuery)) {
            $statement->bind_param("ss", $limit, $offset);
            $statement->execute();
            return $statement->get_result();
        }
        if (!empty($statement)) {
            $statement->close();
        }
        return false;
    }


    /**
     * Function insertItems
     *
     * @param $cust_id
     * @param $order_id
     * @param $chair_id
     * @param $quantity
     * @param $price
     * @return bool|mysqli_result
     */
    public function insertItems($cust_id, $order_id, $chair_id, $quantity, $price)
    {
        $insertItemQuery = "INSERT
                            INTO `chairgap`.`items`(`cust_id`, `order_id`, `chair_id`, `quantity`, `price`)
                            VALUES (?, ?, ?, ?, ?)";

        if ($statement = $this->prepare($insertItemQuery)) {
            $statement->bind_param("iiiis", $cust_id, $order_id, $chair_id, $quantity, $price);
            return $statement->execute();
        }
        $statement->close();
        return false;
    }


    /**
     * Function insertOrder
     *
     * @param $order_id
     * @param $cust_id
     * @param $instructions
     * @param $payment_id
     * @param $amount
     * @return bool
     */
    public function insertOrder($order_id, $cust_id, $instructions, $payment_id, $amount)
    {
        $insertOrderQuery = "INSERT
                            INTO `chairgap`.`orders`(`order_id`, `cust_id`, `instructions`, `payment_id`, `amount`)
                            VALUES (?, ?, ?, ?, ?)";

        if ($statement = $this->prepare($insertOrderQuery)) {
            $statement->bind_param("iisss", $order_id, $cust_id, $instructions, $payment_id, $amount);
            return $statement->execute();
        }
        $statement->close();
        return false;
    }


    /**
     * Function searchChair
     *
     * Search the wine by wine name from the wine database
     * and displays the name, type, cost, winery, year and
     * the id of the wine
     *
     * @param $searchWord
     * @return bool|mysqli_result
     */
    public function searchChair($searchWord)
    {
        $searchChairQuery = "SELECT `chairs`.`chair_id`, `chair_types`.`chair_type`, `chairs`.`chair_name`, `manufacturers`.`manufacturer_name`, `inventories`.`cost`, `chairs`.`year`, `inventories`.`on_hand`
                            FROM `chairs`
                            JOIN `chair_types`
                            JOIN `manufacturers`
                            JOIN `inventories`
                            ON `chairs`.`chair_type` = `chair_types`.`chair_type_id`
                            AND `chairs`.`manufacturer_id` = `manufacturers`.`manufacturer_id`
                            AND `chairs`.`chair_id` = `inventories`.`chair_id`
                            WHERE `chairs`.`chair_name`
                            LIKE '%$searchWord%'
                            ORDER BY `chairs`.`chair_id` ASC";


        return $this->query($searchChairQuery);
    }


    /**
     * Function sortChair
     *
     * Display all the wines in the database
     * by displaying the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $chairType
     * @return bool|mysqli_result
     */
    public function sortChair($chairType)
    {
        $sortChairQuery = "SELECT `chairs`.`chair_id`, `chair_types`.`chair_type`, `chairs`.`chair_name`, `manufacturers`.`manufacturer_name`, `inventories`.`cost`, `chairs`.`year`, `inventories`.`on_hand`
                            FROM `chairs`
                            JOIN `chair_types`
                            JOIN `manufacturers`
                            JOIN `inventories`
                            ON `chairs`.`chair_type` = `chair_types`.`chair_type_id`
                            AND `chairs`.`manufacturer_id` = `manufacturers`.`manufacturer_id`
                            AND `chairs`.`chair_id` = `inventories`.`chair_id`
                            WHERE `chairs`.`chair_type`= ?
                            ORDER BY `chairs`.`chair_id` ASC";

        if ($statement = $this->prepare($sortChairQuery)) {
            $statement->bind_param("i", $chairType);
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }


    /**
     * Function wineDetails
     *
     * Display a wine in the database
     * by displaying the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $wine_id
     * @return bool|mysqli_result
     */
    public function wineDetail($wine_id)
    {
        $wineDetailsQuery = "SELECT `wine`.`wine_id`, `wine_type`.`wine_type`, `wine`.`wine_name`, `winery`.`winery_name`, `wine`.`year`, `inventory`.`cost`, `inventory`.`on_hand`
                             FROM `wine`
                             JOIN `wine_type`
                             JOIN `winery`
                             JOIN `inventory`
                             ON `wine`.`wine_type` = `wine_type`.`wine_type_id`
                             AND `wine`.`winery_id` = `winery`.`winery_id`
                             AND `wine`.`wine_id` = `inventory`.`wine_id`
                             WHERE `wine`.`wine_id` = ?
                             LIMIT 1";

        if ($statement = $this->prepare($wineDetailsQuery)) {
            $statement->bind_param("i", $wine_id);
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }


    /**
     * Function selectChair
     *
     * Display all the wines in the database
     * by displaying the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $chair_id
     * @return bool|mysqli_result
     */
    public function selectChair($chair_id)
    {
        $selectChairQuery = "SELECT `chairs`.`chair_id`, `chair_types`.`chair_type`, `chairs`.`chair_name`, `manufacturers`.`manufacturer_name`, `inventories`.`cost`, `chairs`.`year`, `inventories`.`on_hand`
                            FROM `chairs`
                            JOIN `chair_types`
                            JOIN `manufacturers`
                            JOIN `inventories`
                            ON `chairs`.`chair_type` = `chair_types`.`chair_type_id`
                            AND `chairs`.`manufacturer_id` = `manufacturers`.`manufacturer_id`
                            AND `chairs`.`chair_id` = `inventories`.`chair_id`
                            WHERE `chairs`.`chair_id` = ?";

        if ($statement = $this->prepare($selectChairQuery)) {
            $statement->bind_param("i", $chair_id);
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }

    /**
     * Function searchWine
     *
     * Search the wine by wine name from the wine database
     * and displays the name, type, cost, winery, year and
     * the id of the wine
     *
     * @param $searchWord
     * @return bool|mysqli_result
     */
    public function searchWine($searchWord)
    {
        $searchWineQuery = "SELECT `wine`.`wine_id`, `wine_type`.`wine_type`, `wine`.`wine_name`, `winery`.`winery_name`, `inventory`.`cost`, `wine`.`year`
                            FROM `wine`
                            JOIN `wine_type`
                            JOIN `winery`
                            JOIN `inventory`
                            ON `wine`.`wine_type` = `wine_type`.`wine_type_id`
                            AND `wine`.`winery_id` = `winery`.`winery_id`
                            AND `wine`.`wine_id` = `inventory`.`wine_id`
                            WHERE `wine`.`wine_name`
                            LIKE '%$searchWord%'
                            LIMIT 9";

        return $this->query($searchWineQuery);
    }

    /**
     * Function wineType
     *
     * Load the types of wines from the
     * database. Also fetches the id of
     * the wine type
     *
     * @return bool|mysqli_result
     */
    public function wineType()
    {
        $wineTypeQuery = "SELECT `wine_type`.`wine_type_id`, `wine_type`.`wine_type`
                          FROM `wine_type`";

        if ($statement = $this->prepare($wineTypeQuery)) {
            $statement->execute();
            return $statement->get_result();
        }
    }

    /**
     * Function displayWineByType
     *
     * Display all the wines in the database
     * by type. Shows the wine type, cost of the wine,
     * wine name, the year of manufacture, the wine id
     * and winery name
     *
     * @param $wineType
     * @return bool|mysqli_result
     */
    public function displayWineByType($wineType)
    {
        $displayWineByTypeQuery = "SELECT `wine`.`wine_id`, `wine_type`.`wine_type`, `wine`.`wine_name`, `winery`.`winery_name`, `inventory`.`cost`, `wine`.`year`
                                   FROM `wine`
                                   JOIN `wine_type`
                                   JOIN `winery`
                                   JOIN `inventory`
                                   ON `wine`.`wine_type` = `wine_type`.`wine_type_id`
                                   AND `wine`.`winery_id` = `winery`.`winery_id`
                                   AND `wine`.`wine_id` = `inventory`.`wine_id`
                                   WHERE `wine_type`.`wine_type` = ?
                                   ORDER BY `wine_id`
                                   LIMIT 9";

        $statement = $this->prepare($displayWineByTypeQuery);
        $statement->bind_param("s", $wineType);
        $statement->execute();
        return $statement->get_result();
    }

    /**
     * Function sortWineDesc
     *
     * Sorting of the wines in descending order
     * in relation to the prices of the wine
     *
     * @return bool|mysqli_result
     */
    public function sortWinePriceDesc()
    {
        $sortWineDescQuery = "SELECT `wine`.`wine_id`, `wine_type`.`wine_type`, `wine`.`wine_name`, `winery`.`winery_name`, `inventory`.`cost`, `wine`.`year`
                              FROM `wine`
                              JOIN `wine_type`
                              JOIN `winery`
                              JOIN `inventory`
                              ON `wine`.`wine_type` = `wine_type`.`wine_type_id`
                              AND `wine`.`winery_id` = `winery`.`winery_id`
                              AND `wine`.`wine_id` = `inventory`.`wine_id`
                              ORDER BY `inventory`.`cost` DESC
                              LIMIT 9";

        if ($statement = $this->prepare($sortWineDescQuery)) {
            $statement->execute();
            return $statement->get_result();
        }
    }

    /**
     * Function sortWineAsc
     *
     * Sorting of the wines in ascending order
     * in relation to the prices of the wine
     *
     * @return bool|mysqli_result
     */
    public function sortWinePriceAsc()
    {
        $sortWineAscQuery = "SELECT `wine`.`wine_id`, `wine_type`.`wine_type`, `wine`.`wine_name`, `winery`.`winery_name`, `inventory`.`cost`, `wine`.`year`
                              FROM `wine`
                              JOIN `wine_type`
                              JOIN `winery`
                              JOIN `inventory`
                              ON `wine`.`wine_type` = `wine_type`.`wine_type_id`
                              AND `wine`.`winery_id` = `winery`.`winery_id`
                              AND `wine`.`wine_id` = `inventory`.`wine_id`
                              ORDER BY `inventory`.`cost` ASC
                              LIMIT 9";

        if ($statement = $this->prepare($sortWineAscQuery)) {
            $statement->execute();
            return $statement->get_result();
        }
    }

    /**
     * Function sortWineName
     *
     * Sorting of the wines by name
     *
     * @return bool|mysqli_result
     */
    public function sortWineName()
    {
        $sortWineName = "SELECT `wine`.`wine_id`, `wine_type`.`wine_type`, `wine`.`wine_name`, `winery`.`winery_name`, `inventory`.`cost`, `wine`.`year`
                         FROM `wine`
                         JOIN `wine_type`
                         JOIN `winery`
                         JOIN `inventory`
                         ON `wine`.`wine_type` = `wine_type`.`wine_type_id`
                         AND `wine`.`winery_id` = `winery`.`winery_id`
                         AND `wine`.`wine_id` = `inventory`.`wine_id`
                         ORDER BY `wine`.`wine_name`
                         LIMIT 9";

        if ($statement = $this->prepare($sortWineName)) {
            $statement->execute();
            return $statement->get_result();
        }
    }

    /**
     * Function countChairs
     *
     * Counting the total number of wine in the
     * database
     *
     * @return bool|mysqli_result
     */
    public function countChairs()
    {
        $countWineQuery = "SELECT COUNT(*)
                           AS `chair_id`
                           FROM `chairs`";

        if ($statement = $this->prepare($countWineQuery)) {
            $statement->execute();
            return $statement->get_result();
        }
        $statement->close();
        return false;
    }
}

//
//
//$chair = new Chair();
//$result = $chair->addCustomer("sally", "sam", "","","","skajsbdka@jksd.com", "jabsdljkas");
//print_r($result->fetch_assoc());
//echo '<br>';
//foreach ($chair->sortChair(3) as $row) {
//    print_r($row);
//    echo '<br>';
//}


//$result = $statement->get_result();
//$row = $result->fetch_assoc();
//echo "".$row["user_name"];
//echo "".$row["password"];
//$statement->close();
//
//
//$word = "kin";
//$result = $Wine->searchWine($word);
//
//$row = $result->fetch_assoc();
//echo 'Success...' . $Wine->host_info . "\n";
//print_r ($row);