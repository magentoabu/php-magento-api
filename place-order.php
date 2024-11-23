<?php
declare(strict_types=1);

require_once 'api.php';

// The URL to your Magento 2 instance (ending with /index.php/rest/V1)
$api_url = '';

// Set the integrations access token.
$token = '';

// Fill in the SKU of the product which should be ordered.
$sku = '';

$magento = new MagentoClient($token, $api_url);

try {

    $product = $magento->getProduct($sku);

    if (!$product) {
        throw new Exception('Product not found or unable to fetch product details.');
    }

    $cart = $magento->createCart();
    if (!$cart) {
        throw new Exception('Failed to create cart.');
    }
    
    $cart = str_replace('"', '', $cart);

    $order_filled = $magento->addToCart($cart, $sku, 1);
    if (!$order_filled) {
        throw new Exception('Failed to add product to cart.');
    }  
    $ship_to = [
        'addressInformation' => [
            'shippingAddress' => [
                'region' => 'Wien',
                'region_id' => 95,
                'country_id' => 'AT',
                'street' => ['Fillgradergasse 12-14/1a'],
                'company' => 'acolono GmbH',
                'telephone' => '1111111',
                'postcode' => '1060',
                'city' => 'Vienna',
                'firstname' => 'Mohamed',
                'lastname' => 'Abbas',
                'email' => 'mohamed@gmail.com',
                'prefix' => 'address_',
                'region_code' => 'W',
                'sameAsBilling' => 1,
            ],
            'billingAddress' => [
                'region' => 'Wien',
                'region_id' => 95,
                'country_id' => 'AT',
                'street' => ['Fillgradergasse 12-14/1a'],
                'company' => 'acolono GmbH',
                'telephone' => '1111111',
                'postcode' => '1060',
                'city' => 'Vienna',
                'firstname' => 'Mohamed',
                'lastname' => 'abbas',
                'email' => 'mohamed@gmail.com',
                'prefix' => 'address_',
                'region_code' => 'W',
            ],
            'shipping_method_code' => 'flatrate',
            'shipping_carrier_code' => 'flatrate',
        ],
    ];

    $order_shipment = $magento->setShipping($cart, $ship_to);
    if (!$order_shipment) {
        throw new Exception('Failed to set shipping information.');
    }

    $ordered = $magento->placeOrder($cart, 'checkmo');
    if (!$ordered) {
        throw new Exception('Failed to place the order.');
    }

    echo "\nOrdered Successfully\n";
    var_dump($ordered);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}