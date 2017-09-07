<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

class XmlFormatter
{
    public static function format(string $string): string
    {
        $dom = new \DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($string);
        return $dom->saveXML();
    }
}
