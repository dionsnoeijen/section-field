<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

class PhpFormatter
{
    public static function format(string $string): string
    {
        $tabs = 0;
        $startCounter = 0;
        $result = '';
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $string) as $line) {
            $line = str_replace('    ', '', $line);
            if ($line === '}') {
                $tabs--;
            }
            $indent = '';
            for ($i = 0 ; $i < $tabs ; $i++) {
                $indent .= '    ';
            }
            if ($line !== '') {
                if (substr($line, 0, 3) === '/**') {
                    if ($startCounter >= 1) {
                        $result .= PHP_EOL;
                    }
                    $startCounter++;
                }
                if (substr($line,0,6 ) === 'public' ||
                    substr($line, 0, 9) === 'namespace' ||
                    substr($line, 0, 5) === 'class'
                ) {
                    $result .= PHP_EOL;
                }
                $result .= $indent . $line . PHP_EOL;
            }
            if ($line === '{') {
                $tabs++;
            }
        }
        $result .= PHP_EOL;

        return $result;
    }
}
