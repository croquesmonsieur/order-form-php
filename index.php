<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);
session_start();
const MAX_NUM = 100;
const MAX_ZIP = 4;
$error_email = "";
$error_street = "";
$error_number = "";
$error_fields = "";

//we are going to use session variables so we need to enable sessions


function check($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$user_order = [];
//your products with their price.
if ((!isset($_GET['food']) && !isset($_POST['food'])) || (($_GET['food'] == 1) || $_POST['food'] == 1)) {
    $stateFood = 1;
    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
} elseif ($_GET['food'] == 0 || $_POST['food'] == 0) {
    $stateFood = 0;
    $products = [
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3],
    ];
}

$currentTime = time();
$standardDelivery = 7200;
$expressDelivery = 2700;
//$currentDate = date("H:i:s");
$deliver_time = $currentTime + $standardDelivery;
$fast_deliver_time = $currentTime + $expressDelivery;
$order_time = date("H:i", $deliver_time);
$fast_order_time = date("H:i", $fast_deliver_time);

$totalValue = 0;
if (!isset($_COOKIE['orders'])) {
    $totalValue = 0;
    setcookie('orders', (string)$totalValue, time() + 3600);
} else {
    $totalValue = (float)$_COOKIE['orders'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_arr = [
    'userEmail' => "",
    'userStreet' => "",
    'userStreetnumber' => "",
    'userCity' => "",
    'userZipcode' => ""
];

    if (empty($_POST['email'])) {
        $error_email = '* This is a required field!';
    } else {
        $user_arr['userEmail'] = check($_POST['email']);
        $user_arr['userEmail'] = filter_var($user_arr['userEmail'], FILTER_VALIDATE_EMAIL);
        $_SESSION['email'] = $user_arr['userEmail'];
        if ($user_arr['userEmail'] == false) {
            $error_email = '* Invalid Email!';
        }
    }
    if (empty($_POST['street'])) {
        $error_street = '* This is a required field!';
    } else {
        $user_arr['userStreet'] = check($_POST['street']);
        $_SESSION['street'] = $user_arr['userStreet'];
    }
    if (empty($_POST['streetnumber'])) {
        $error_number = "* This is a required field!";
    } else {
        $user_arr['userStreetnumber'] = check($_POST['streetnumber']);
        $user_arr['userStreetnumber'] = min($_POST['streetnumber'], MAX_NUM);
        $_SESSION['streetnumber'] = $user_arr['userStreetnumber'];
    }
    if (empty($_POST['city'])) {
        $error_street = "* This is a required field!";
    } else {
        $user_arr['userCity'] = check($_POST['city']);
        $_SESSION['city'] = $user_arr['userCity'];
    }
    if (empty($_POST['zipcode'])) {
        $error_street = "* This is a required field!";
    } else {
        $user_arr['userZipcode'] = check($_POST['zipcode']);
        $_SESSION['zipcode'] = $user_arr['userZipcode'];
    }


if (isset($_POST['submit1'])){
    if (!empty($_POST['products']) && empty($_POST['express_delivery'])) {
        foreach ($_POST['products'] as $i => $product) {
            $totalValue += $products[$i]['price'] * $_POST['products'][$i];
            array_push($user_order, $products[$i]['name']);
        }
        var_dump($totalValue);
        setcookie('orders', (string)$totalValue, time() + 3600);
        echo "Your order:" . $user_order . " will be delivered at " . $order_time . "!";
    }
    if (!empty($_POST['express_delivery']) && !empty($_POST['products'])) {
        $totalValue += $_POST['express_delivery'];
        foreach ($_POST['products'] as $i => $product) {
            $totalValue += $products[$i]['price'] * $_POST['products'][$i];
            array_push($user_order, $products[$i]['name']);
        }
        var_dump($totalValue);
        setcookie('orders', (string)$totalValue, time() + 3600);
        echo "Your order will be delivered at " . $fast_order_time . "!";
    }
    unset($_POST);
    header("Location: ".$_SERVER['PHP_SELF']);
}


}




function whatIsHappening()
{
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}

whatIsHappening();

require 'form-view.php';
