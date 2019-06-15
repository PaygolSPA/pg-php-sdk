<?php

namespace Paygol;

use Paygol\Exceptions\InvalidParameterException;
use Paygol\Models\Payer;
use Paygol\Models\RedirectUrls;

abstract class Paygol
{
    const API_IMPL = "php/1.0";
    const API_BASE_URL = "https://www.paygol.com/api/v2/";

    const API_PATH_AUTH_TOKEN = "auth/token";
    const API_PATH_PAYMENT_METHODS = "payment/methods";
    const API_PATH_PAYMENT_STATUS = "payment/status";
    const API_PATH_PAYMENT_CREATE = "payment/create";
    const API_PATH_PAYMENT_WEBCHECKOUT = "payment/webcheckout";

    const MODE_API = 'api';
    const MODE_WEBCHECKOUT = 'basic';

    /**
     * Return as JSON data
     *
     * @var boolean
     */
    protected $asJSON = false;

    /**
     * Transaction mode
     *
     * @var string
     */
    protected $pg_mode;

    /**
     * Merchant service ID
     *
     * @var int
     */
    protected $service_id;

/**
 * Merchant shared secret
 *
 * @var string
 */
    protected $shared_secret;

    /**
     * Token
     *
     * @var string
     */
    protected $token;

    /**
     * Currency (ISO 4217)
     *
     * @var string
     */
    protected $pg_currency = null;

    /**
     * Price
     *
     * @var float
     */
    protected $pg_price = null;

    /**
     * Country (ISO 3166-2)
     *
     * @var string
     */
    protected $pg_country = null;

    /**
     * Language
     *
     * @var string
     */
    protected $pg_language = 'en';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $pg_method = null;

    /**
     * @var array
     */
    protected $payer = [];

    /**
     * @var array
     */
    protected $redirect_urls = [];

    /**
     * @var mixed
     */
    protected $pg_sub_merchant_id = null;

    /**
     * @var string
     */
    protected $pg_sub_merchant_url = null;

    /**
     * @var mixed
     */
    protected $pg_custom = null;

    /**
     * @var mixed
     */
    protected $pg_name = null;

    /**
     * Success return URL
     *
     * @var string
     */
    protected $pg_return_url = null;

    /**
     * Fail return URL
     *
     * @var string
     */
    protected $pg_cancel_url = null;

    /**
     * @param int $service_id
     * @param string $shared_secret
     */
    public function __construct($service_id, $shared_secret, $mode)
    {
        $this->service_id = $service_id;
        $this->shared_secret = $shared_secret;
        $this->pg_mode = is_null($mode) ? self::MODE_WEBCHECKOUT : $mode;
        $this->token = null;

        $this->auth();
    }

    public function returnJSON($asJSON = true)
    {
        $this->asJSON = $asJSON;
    }

    /**
     * Set submerchant Info
     *
     * @param mixed $id
     * @param string $url
     *
     * @return void
     */
    public function setSubmerchantInfo($id, $url)
    {
        $this->pg_sub_merchant_id = $id;
        $this->pg_sub_merchant_url = $url;
    }

    /**
     * Set custom info
     *
     * @param string $custom
     *
     * @return void
     */
    public function setCustom($custom)
    {
        $this->pg_custom = $custom;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName($name)
    {
        $this->pg_name = $name;
    }

    /**
     * Get payment methods
     *
     * @param string $country
     *
     * @return array
     */
    public function getPaymentMethods($country)
    {
        $ret = $this->get(self::API_PATH_PAYMENT_METHODS, ['pg_country' => strtoupper($country)]);
        self::expect_vars($ret, ["methods"]);

        return $this->format_response($ret);
    }

    /**
     * Get payment status
     *
     * @param string $id
     *
     * @return array
     */
    public function getPaymentStatus($id)
    {
        $ret = $this->get(self::API_PATH_PAYMENT_STATUS, ['transaction_id' => $id]);
        // self::expect_vars($ret, ["payment"]);

        if (!isset($ret['payment']) && !isset($ret['error'])) {
            throw new \Exception("Invalid response data");
        }

        return $this->format_response($ret);
    }

    /**
     * Create payment
     *
     * @return array
     */
    public function createPayment()
    {
        $args = [
            'pg_currency' => $this->pg_currency,
            'pg_price' => $this->pg_price,
            'pg_country' => $this->pg_country,
            'pg_language' => $this->pg_language,
            'pg_mode' => $this->pg_mode,
            'pg_method' => $this->pg_method,
            'pg_first_name' => null,
            'pg_last_name' => null,
            'pg_email' => null,
        ];

        $args = array_merge($args, $this->payer, $this->redirect_urls);

        if ($this->pg_sub_merchant_id != null) {
            $args['pg_sub_merchant_id'] = $this->pg_sub_merchant_id;
        }

        if ($this->pg_sub_merchant_url != null) {
            $args['pg_sub_merchant_url'] = $this->pg_sub_merchant_url;
        }

        if ($this->pg_custom != null) {
            $args['pg_custom'] = $this->pg_custom;
        }

        if ($this->pg_name != null) {
            $args['pg_name'] = $this->pg_name;
        }

        foreach ($args as $k => $v) {
            if (null == $v) {
                throw new InvalidParameterException("Parameter {$k} is requiered");
            }
        }

        $ret = $this->get(self::API_PATH_PAYMENT_CREATE, $args);

        if (!isset($ret['data']) && !isset($ret['error'])) {
            throw new \Exception("Invalid response data");
        }

        return $this->format_response($ret);
    }

    /**
     * Set payment method
     *
     * @param string $method
     *
     * @return void
     */
    public function setPaymentMethod($method)
    {
        $this->pg_method = $method;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return void
     */
    public function setCountry($country)
    {
        $this->pg_country = $country;
    }

    /**
     * Set price
     *
     * @param float $price
     * @param string $currency
     *
     * @return void
     */
    public function setPrice($price, $currency)
    {
        $this->pg_price = $price;
        $this->pg_currency = $currency;
    }

    public function setPayer(Payer $payer)
    {
        $this->payer = $payer->asArray();
    }

    /**
     * @param Payer $redirect_urls
     * @return void
     */
    public function setRedirects(RedirectUrls $redirect_urls)
    {
        $this->redirect_urls = $redirect_urls->asArray();
    }

    /**
     * Authenticate user
     *
     * @return void
     */
    protected function auth()
    {
        $ret = $this->get(self::API_PATH_AUTH_TOKEN);
        self::expect_vars($ret, ["token"]);
        $this->token = $ret["token"];
    }

    /**
     * @param array $o
     * @param array $vars
     *
     * @throws Exception
     *
     * @return void
     */
    protected static function expect_vars($o, $vars)
    {
        foreach ($vars as $v) {
            if (!isset($o[$v])) {
                throw new \Exception("missing var $v");
            }
        }
    }

    /**
     * Compute signature
     *
     * @param string $msg
     * @param string $secret
     *
     * @return string
     */
    protected static function compute_signature($msg, $secret)
    {
        return hash_hmac("sha256", $msg, $secret);
    }

    /**
     * Validate response
     *
     * @param string $json_response
     * @param array $headers
     *
     * @throws Exception
     *
     * @return array
     */
    protected function validate_response($json_response, $headers)
    {
        $result = json_decode($json_response, true);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("json parse error");
        }

        $sig_response = self::compute_signature($json_response, $this->shared_secret);

        $sig_ok = false;
        foreach ($headers as $hdr) {
            if (preg_match("/^X\-PG\-SIG:\s([0-9a-f]{64})$/", $hdr, $m)) {
                if ($sig_response != $m[1]) {
                    throw new \Exception("SIG MISMATCH");
                }

                $sig_ok = true;
                break;
            }
        }

        if (!$sig_ok) {
            throw new \Exception("NO SIG");
        }

        self::expect_vars($result, ["result"]);

        // Retornar data error
        // if ($result["result"] != 0) {
        //     throw new \Exception("invalid result: " . $result["message"]);
        // }

        return $result;
    }

    /**
     * @param string $path
     * @param array $args
     *
     * @throws Exception
     *
     * @return array
     */
    protected function get($path, $args = [])
    {
        $args['pg_serviceid'] = $this->service_id;

        if ($this->token != null) {
            $args['pg_token'] = $this->token;
        }

        $json_request = json_encode($args, JSON_FORCE_OBJECT);
        $sig_request = self::compute_signature($json_request, $this->shared_secret);

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Content-type: application/json',
                    'X-PG-IMPL: ' . self::API_IMPL,
                    'X-PG-SIG: ' . $sig_request,
                ],
                'content' => $json_request,
            ],
        ];

        $context = @stream_context_create($opts);
        $json_response = @file_get_contents(self::API_BASE_URL . $path, false, $context);

        if (false === $json_response) {
            throw new \Exception("connection error");
        }

        return $this->validate_response($json_response, $http_response_header);
    }

    protected function format_response($data)
    {
        return $this->asJSON ? json_encode($data) : $data;
    }
}
