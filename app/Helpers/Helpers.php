<?php

if (!function_exists('removeNullish')) {
    /**
     * Removing nullish values from an array.
     *
     * @param  array  $origin
     */
    function removeNullish($origin = [])
    {
        return  array_filter($origin, fn ($value) => !is_null($value) && $value !== '');
    }
}

if (!function_exists('getRandomString')) {
    /**
     * Generate random string.
     *
     * @param  number  $length
     */
    function getRandomString($length = 16)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
}
