<?php

use \Paygol\API;

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
    $pg = new API($service_id, $shared_secret);

    $status = $pg->getPaymentStatus('1234-5678-ABCD-EFGH');

    var_dump( $status );
} catch (\Exception $e) {
    die($e->getMessage());
}
