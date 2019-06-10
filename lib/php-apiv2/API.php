<?php

namespace Paygol;

class API extends Paygol
{
    public function __construct($service_id, $shared_secret)
    {
        parent::__construct($service_id, $shared_secret, self::MODE_API);
    }

}
