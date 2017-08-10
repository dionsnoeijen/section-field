<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

class XmlFormatter
{
    public static function format(string $string): string
    {
        $xml = simplexml_load_string($string);
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return $dom->saveXML();
    }
}
