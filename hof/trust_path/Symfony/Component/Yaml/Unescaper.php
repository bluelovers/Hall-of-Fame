<?php

class Symfony_Component_Yaml_Unescaper
{
    public static function unescapeSingleQuotedString($value)
    {
        return str_replace("''", "'", $value);
    }

    public static function unescapeDoubleQuotedString($value)
    {
        $callback = array('Symfony_Component_Yaml_Unescaper', 'unescapeDoubleQuotedStringCallback');

        return preg_replace_callback('/\\\\([\\\\"nrtbf \/]|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8}|x[0-9a-fA-F]{2}|.)/', $callback, $value);
    }

    public static function unescapeDoubleQuotedStringCallback($matches)
    {
        $char = $matches[1];

        $map = array(
            '\\' => '\\',
            '"' => '"',
            'n' => "\n",
            'r' => "\r",
            't' => "\t",
            'b' => "\x08",
            'f' => "\x0C",
            ' ' => ' ',
            '/' => '/',
        );

        if (isset($map[$char])) {
            return $map[$char];
        }

        if (strlen($char) > 1 && $char[0] === 'u' || $char[0] === 'U') {
            $hex = substr($char, 1);
            $n = hexdec($hex);
            if ($n < 0x10000) {
                return mb_convert_encoding('&#' . $n . ';', 'UTF-8', 'HTML-ENTITIES');
            }
        }

        if ($char[0] === 'x') {
            return chr(hexdec(substr($char, 1)));
        }

        return $char;
    }
}
