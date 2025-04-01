<?php

require_once "stripe-php-master/init.php";

$stripe_secret_key = "sk_test_51R8utkDvWXj8CDKClmNOe14pWI5MdrnlZaAZvM7T5lOvePgTqtOIjIrJllrfl7j7FUYYqPP344VW1lJXLtNPtEyf00XdzCYTn9";

\Stripe\Stripe::setApiKey($stripe_secret_key);

$checkout_session = \Stripe\Checkout\Session::create([
    "mode" => "payment",
    "success_url" => "http://localhost:3000/BYD-Clothing-E-Commerce-main/shop/confirm.php",
    "cancel_url" => "http://localhost:3000/BYD-Clothing-E-Commerce-main/shop/cancelled.php",
    "locale" => "auto",
    "line_items" => [
        [
            "quantity" => 1,
            "price_data" => [
                "currency" => "usd",
                "unit_amount" => 1428,
                "product_data" => [
                    "name" => "Gipsy"
                ]
            ]
        ],      
    ]
]);

http_response_code(303);
header("Location: " . $checkout_session->url);