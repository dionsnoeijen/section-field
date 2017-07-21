<?php

namespace Tardigrades\Helper;

class StringConverter
{
    public static function toCamelCase(string $string, array $noStrip = []): string
    {
        $string = preg_replace('/[^a-z0-9' . implode('', $noStrip) . ']+/i', ' ', $string);
        $string = trim($string);
        $string = ucwords($string);
        $string = str_replace(" ", "", $string);
        $string = lcfirst($string);

        return $string;
    }

    public static function toSlug(string $string): string
    {
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);
        $string = preg_replace('~[^-\w]+~', '', $string);
        $string = trim($string, '-');
        $string = preg_replace('~-+~', '-', $string);
        $string = strtolower($string);

        if (empty($string)) {
            return 'n-a';
        }

        return $string;
    }
}
