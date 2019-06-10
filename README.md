# PHP SDK for API and Webcheckout implementation

## Installation

### Requirements

PHP 5.6.4 and later.

### Developer Documentation
[Official Paygol Documentation](https://devs.paygol.com)

### Composer

It's recommended to use [composer](http://getcomposer.org) or you can download the latest release.

```
composer require paygol/php-sdk
```

## Example
```
require_once '../vendor/autoload.php';

$service_id = "123";
$shared_secret = "7c1a6a24-7943-102d-92f8-29573711ad31";

try {
    $pg = new \Paygol\API($service_id, $shared_secret);

    $redirectUrls = new \Paygol\Models\RedirectUrls();
    $redirectUrls->setRedirects(
      "https://www.my-site.com/success", 
      "https://www.my-site.com/failure"
    ); // optional

    $pg->setRedirects($redirectUrls);

    $pg->setCountry('DE');
    $pg->setPrice(10.00, 'EUR');
    $pg->setPaymentMethod('bitcoin');

    $payer = new \Paygol\Models\Payer();
    $payer->setFirstName('John');
    $payer->setLastName('Doe');
    $payer->setEmail('jdoe@my-site.com');
    $payer->setBIC('123423432');

    $pg->setPayer($payer);

    $payment = $pg->createPayment();

    var_dump( $payment );

    if (!empty($payment['data']['payment_method_url'])) {
        // do something
    }
} catch (\Exception $e) {
    die($e->getMessage());
}
```

## License

Read [License](https://github.com/PaygolSPA/php-sdk/blob/master/LICENSE) for more licensing information.
