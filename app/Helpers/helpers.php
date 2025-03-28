<?php

/**
 * Write code on Method
 *
 * @return response()
 */
if (!function_exists('convertDivider')) {
    function convertDivider($amount)
    {
        return number_format($amount, 0, '.', '.');
    }
}

