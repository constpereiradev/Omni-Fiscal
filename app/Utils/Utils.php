<?php

namespace App\Utils;

class Utils

{
    public static function onlyNumbers($value)
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
}
