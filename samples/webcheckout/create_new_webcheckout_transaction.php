<?php

require __DIR__ . '/../../vendor/autoload.php';


use \Paygol\Webcheckout;
use \Paygol\Models\Payer;
use \Paygol\Models\RedirectUrls;

use \Paygol\Exceptions\InvalidParameterException;

$products = [
    1234 => [
        'name' => 'Basic',
        'price' => 99.99,
        'currency' => 'USD'
    ],
    1235 => [
        'name' => 'Standard',
        'price' => 199.99,
        'currency' => 'USD'
    ],
    1236 => [
        'name' => 'Unlimited',
        'price' => 299.99,
        'currency' => 'USD'
    ],
];

$product_id = $_GET['productid'];

$product = $products[$product_id];


/**
 * Merchant service ID
 *
 * @var int
 */
$service_id = "123";
/**
 * Merchant shared secret
 *
 * @var string
 */
$shared_secret = "7c1a6a24-7943-102d-92f8-29573711ad31";

try {
    $pg = new Webcheckout($service_id, $shared_secret);

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setRedirects(
        "https://www.my-site.com/success", 
        "https://www.my-site.com/failure"
    ); // optional

    $pg->setRedirects($redirectUrls);

    $pg->setCountry('DE');

    $pg->setPrice($product['price'], $product['currency']);

    $pg->setName($product['name']);

    $payer = new Payer();
    $payer->setEmail('jdoe@my-site.com');
    $payer->setBIC('123423432');

    $pg->setPayer($payer);

    $payment = $pg->createPayment();
} catch (InvalidParameterException $e) {
    die($e->getMessage());
} catch (\Exception $e) {
    die($e->getMessage());
}
