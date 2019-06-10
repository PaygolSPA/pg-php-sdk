<?php

use \Paygol\API;
use \Paygol\Models\Payer;
use \Paygol\Models\RedirectUrls;

use \Paygol\Exceptions\InvalidParameterException;


$service_id = "123";
$shared_secret = "7c1a6a24-7943-102d-92f8-29573711ad31";

try {
    $pg = new API($service_id, $shared_secret);

    $redirectUrls = new RedirectUrls();
    $redirectUrls->setRedirects("https://www.my-site.com/success", "https://www.my-site.com/failure"); // optional

    $pg->setRedirects($redirectUrls);

    $pg->setCountry('DE');
    $pg->setPrice(100, 'EUR');
    $pg->setPaymentMethod('bitcoin');

    $payer = new Payer();
    $payer->setFirstName('John');
    $payer->setLastName('Doe');
    $payer->setEmail('jdoe@my-site.com');
    $payer->setBIC('123423432');

    $pg->setPayer($payer);

    $payment = $pg->createPayment();

    var_dump( $payment['data']['payment_method_url'] );
} catch (InvalidParameterException $e) {
    die($e->getMessage());
} catch (\Exception $e) {
    die($e->getMessage());
}
