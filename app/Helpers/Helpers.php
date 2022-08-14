<?php

if (!function_exists('removeNullish')) {
    /**
     * Removing nullish values from an array.
     *
     * @param  array  $array
     */
    function removeNullish($origin = [])
    {
        return  array_filter($origin, fn ($value) => !is_null($value) && $value !== '');
    }
}
