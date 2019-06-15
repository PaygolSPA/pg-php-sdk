<?php

namespace Paygol;

use Paygol\Exceptions\InvalidSignatureException;

class Notification
{
    const API_IMPL = "php/1.0";
    const API_SIGN_HEADER = "X-Pg-Sig";

    /**
     * Request params
     *
     * @var array
     */
    private $params = [];

    /**
     * Request headers
     *
     * @var array
     */
    private $headers = [];

    /**
     * @var int
     */
    private $service_id;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param int $service_id
     * @param string $shared_secret
     */
    public function __construct($service_id, $shared_secret)
    {
        $this->params = json_decode(file_get_contents('php://input'), true);

        $this->sort_data();

        $this->headers = getallheaders();
        $this->service_id = $service_id;
        $this->secret = $shared_secret;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Validate signature
     *
     * @throws Exception
     *
     * @return boolean
     */
    public function validate()
    {
        if (empty($this->headers[self::API_SIGN_HEADER]) || $this->headers[self::API_SIGN_HEADER] !== $this->compute_signature()) {
            throw new InvalidSignatureException('Invalid signature');
        }

        return true;
    }

    /**
     * Send response
     *
     * @param array $data
     * @param integer $code
     *
     * @return void
     */
    public function sendResponse($data = [], $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
    }

    /**
     * Sort params
     *
     * @return void
     */
    private function sort_data()
    {
        ksort($this->params, SORT_NATURAL | SORT_FLAG_CASE);
    }

    /**
     * Compute signature
     *
     * @return string
     */
    private function compute_signature()
    {
        return hash_hmac("sha256", json_encode($this->params), $this->secret);
    }
}
