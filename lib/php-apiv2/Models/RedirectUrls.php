<?php
namespace Paygol\Models;

class RedirectUrls extends Model
{
    /*
     * @var string
     */
    protected $pg_return_url;

    /*
     * @var string
     */
    protected $pg_cancel_url;

    /**
     * Set redirects URLs
     *
     * @param string $pg_return_url
     * @param string $pg_cancel_url
     *
     * @return void
     */
    public function setRedirects($pg_return_url, $pg_cancel_url)
    {
        $this->pg_return_url = $pg_return_url;
        $this->pg_cancel_url = $pg_cancel_url;
    }

    /*
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->pg_return_url;
    }

    /*
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->pg_cancel_url;
    }

    /*
     * @param string
     */
    public function setReturnUrl($pg_return_url)
    {
        $this->pg_return_url = $pg_return_url;
    }
    /*
     * @param string
     */
    public function setCancelUrl($pg_cancel_url)
    {
        $this->pg_cancel_url = $pg_cancel_url;
    }
}
