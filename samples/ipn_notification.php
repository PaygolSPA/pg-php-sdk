<?php

use Paygol\Notification;

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

$ipn = new Notification($service_id, $shared_secret);

try {
    $ipn->validate();

    $params = $ipn->getParams();

    var_dump($params);
    // do something

    // Confirm reception
    $ipn->sendResponse(['OK'], 200);
} catch (\Exception $e) {
    $ipn->sendResponse(['error' => 'Validation error'], 400);
}
