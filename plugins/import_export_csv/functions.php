<?php
/**
 * @author Carlos García Gómez      neorazorx@gmail.com
 * @copyright 2016-2018, Carlos García Gómez. All Rights Reserved. 
 */
if (!function_exists('floatval_coma')) {

    function floatval_coma($value)
    {
        return (float) str_replace(',', '.', $value);
    }
}