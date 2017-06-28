<?php

namespace Tardigrades\Helper;

class StringConverter
{
    public static function toCamelCase(string $string, array $noStrip = []): string
    {
        $string = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $string);
        $string = trim($string);
        $string = ucwords($string);
        $string = str_replace(" ", "", $string);
        $string = lcfirst($string);

        return $string;
    }
}
