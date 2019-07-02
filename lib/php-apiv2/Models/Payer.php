<?php

namespace Paygol\Models;

class Payer extends Model
{
    /**
     * Payer IP address
     *
     * @var string
     */
    protected $pg_ip = null;

    /**
     * Payer email address
     *
     * @var string
     */
    protected $pg_email = null;

    /**
     * Payer first name
     *
     * @var string
     */
    protected $pg_first_name = null;

    /**
     * Payer last name
     *
     * @var string
     */
    protected $pg_last_name = null;

    /**
     * Payer phone number
     *
     * @var string
     */
    protected $pg_phone = null;

    /**
     * Payer DNI
     *
     * @var string
     */
    protected $pg_personal_id = null;

    /**
     * Payer BIC
     *
     * @var string
     */
    protected $pg_bic = null;

    /**
     * Payer IBAN
     *
     * @var string
     */
    protected $pg_iban = null;

    public function __construct()
    {
        if (!empty($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $this->pg_ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (!empty($_SERVER["REMOTE_ADDR"])) {
            $this->pg_ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $this->pg_ip = '127.0.0.1';
        }
    }

    public function setFirstName($first_name)
    {
        $this->pg_first_name = $first_name;
    }

    public function setLastName($last_name)
    {
        $this->pg_last_name = $last_name;
    }

    public function setEmail($email)
    {
        $this->pg_email = $email;
    }

    public function setPhoneNumber($phone)
    {
        $this->pg_phone = $phone;
    }

    public function setPersonalID($pid)
    {
        $this->pg_personalid = $pid;
    }
    
    public function setIBAN($iban)
    {
        $this->pg_iban = $iban;
    }

    public function setBIC($bic)
    {
        $this->pg_bic = $bic;
    }

    public function setIP($ip)
    {
        $this->pg_ip = $ip;
    }
}
