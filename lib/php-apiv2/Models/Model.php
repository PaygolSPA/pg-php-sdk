<?php

namespace Paygol\Models;


abstract class Model
{
    public function asArray()
    {
        return array_filter(get_object_vars($this));
    }
}
