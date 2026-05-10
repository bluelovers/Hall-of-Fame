<?php

class Symfony_Component_Yaml_Inline
{
    public static function parse($value, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        $value = trim($value);

        if ('' === $value) {
            return null;
        }

        if (in_array(strtolower($value), array('true', 'on', 'yes'))) {
            return true;
        }

        if (in_array(strtolower($value), array('false', 'off', 'no'))) {
            return false;
        }

        if (in_array(strtolower($value), array('null', '~'))) {
            return null;
        }

        if (is_numeric($value)) {
            return $value === (string)(int)$value ? (int)$value : (float)$value;
        }

        if ($value[0] === "'") {
            $value = rtrim(substr($value, 1), "'");
            return Symfony_Component_Yaml_Unescaper::unescapeSingleQuotedString($value);
        }

        if ($value[0] === '"') {
            $value = rtrim(substr($value, 1), '"');
            return Symfony_Component_Yaml_Unescaper::unescapeDoubleQuotedString($value);
        }

        if (preg_match('/^\[(.*)\]$/s', $value, $m)) {
            return self::parseSequence($m[1]);
        }

        if (preg_match('/^\{(.*)\}$/s', $value, $m)) {
            return self::parseMapping($m[1]);
        }

        return $value;
    }

    public static function parseSequence($value)
    {
        if (trim($value) === '') return array();

        $parts = preg_split('/,\s*/', $value);
        $result = array();

        foreach ($parts as $part) {
            $result[] = self::parse(trim($part));
        }

        return $result;
    }

    public static function parseMapping($value)
    {
        if (trim($value) === '') return array();

        $parts = preg_split('/,\s*/', $value);
        $result = array();

        foreach ($parts as $part) {
            if (preg_match('/^([^:]+):\s*(.*)$/s', trim($part), $m)) {
                $key = trim($m[1]);
                $result[$key] = self::parse(trim($m[2]));
            }
        }

        return $result;
    }

    public static function dump($value, $inline = 0, $objectSupport = false)
    {
        if ('' === $value || null === $value) {
            return 'null';
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_int($value) || is_float($value)) {
            return (string)$value;
        } elseif (is_array($value)) {
            return self::dumpArray($value, $inline);
        } elseif (is_string($value)) {
            return self::dumpString($value);
        }

        return (string)$value;
    }

    public static function dumpArray($value, $inline = 0)
    {
        $isHash = self::isHash($value);
        $result = array();

        foreach ($value as $k => $v) {
            if ($isHash) {
                $result[] = self::dump($k) . ': ' . self::dump($v, $inline);
            } else {
                $result[] = self::dump($v, $inline);
            }
        }

        return ($isHash ? '{' : '[') . implode(', ', $result) . ($isHash ? '}' : ']');
    }

    public static function dumpString($value)
    {
        if ('' === $value) {
            return "''";
        }

        if (Symfony_Component_Yaml_Escaper::requiresDoubleQuoting($value)) {
            return Symfony_Component_Yaml_Escaper::escapeWithDoubleQuotes($value);
        }

        if (Symfony_Component_Yaml_Escaper::requiresSingleQuoting($value)) {
            return Symfony_Component_Yaml_Escaper::escapeWithSingleQuotes($value);
        }

        return $value;
    }

    public static function isHash($value)
    {
        if (!is_array($value)) return false;
        $expectedKey = 0;
        foreach ($value as $key => $val) {
            if ($key !== $expectedKey++) return true;
        }
        return false;
    }
}
