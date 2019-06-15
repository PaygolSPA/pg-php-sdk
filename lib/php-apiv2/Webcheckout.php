<?php

namespace Paygol;

use Paygol\Exceptions\InvalidParameterException;

class Webcheckout extends Paygol
{
    public function __construct($service_id, $shared_secret)
    {
        parent::__construct($service_id, $shared_secret, self::MODE_WEBCHECKOUT);
    }

    /**
     * Create payment
     *
     * @throws Exception
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
        ];

        $args = array_merge($args, $this->payer, $this->redirect_urls);

        if ($this->pg_method != null) {
            $args['pg_method'] = $this->pg_method;
        }

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

        $ret = $this->get(self::API_PATH_PAYMENT_WEBCHECKOUT, $args);

        if (isset($ret['error'])) {
            $error = explode(':', $ret['error']['message']);
            throw new \Exception(!empty($error[1]) ? $error[1] : 'Something is wrong');
        }

        self::expect_vars($ret, ["data"]);

        header('Location: ' . $ret['data']['redirectUrl']);
        exit();
    }
}
