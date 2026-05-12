<?php

class Symfony_Component_Yaml_Escaper
{
    public static function escapeWithSingleQuotes($value)
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    public static function escapeWithDoubleQuotes($value)
    {
        $escaped = str_replace(array('\\', '"', "\n", "\r", "\t", "\x08", "\x0C"), array('\\\\', '\\"', '\\n', '\\r', '\\t', '\\b', '\\f'), $value);

        if (false !== mb_detect_encoding($value, 'UTF-8', true)) {
            $escaped = preg_replace_callback('/[^\x09\x0A\x0D\x20-\x7E]/u', array('Symfony_Component_Yaml_Escaper', 'escapeUtf8CharDoubleQuote'), $escaped);
        }

        return '"' . $escaped . '"';
    }

    public static function escapeUtf8CharDoubleQuote($matches)
    {
        $c = $matches[0];
        $escaped = '';

        $o = unpack('N*', mb_convert_encoding($c, 'UTF-32BE', 'UTF-8'));

        foreach ($o as $v) {
            if ($v < 0x10000) {
                $escaped .= sprintf('\\u%04X', $v);
            } else {
                $escaped .= sprintf('\\U%08X', $v);
            }
        }

        return $escaped;
    }

    public static function requiresDoubleQuoting($value)
    {
        if (preg_match('/^[0-9]+$/', $value)) return true;
        if (in_array(strtolower($value), array('true', 'false', 'null', 'yes', 'no', 'on', 'off'))) return true;
        if (preg_match('/[:\x00-\x0A\x0D-\x1F\x7F\'"\[\]\{\},&*\#\?<>=!%@`|]/', $value)) return true;

        return false;
    }

    public static function requiresSingleQuoting($value)
    {
        if (preg_match('/\s/', $value)) return true;

        return false;
    }
}
