<?php

class Symfony_Component_Yaml_Yaml
{
    static public $enablePhpParsing = false;

    static public function enablePhpParsing()
    {
        self::$enablePhpParsing = true;
    }

    static public function parse($input)
    {
        $file = '';

        if (strpos($input, "\n") === false && is_file($input)) {
            if (false === is_readable($input)) {
                throw new Symfony_Component_Yaml_Exception_ParseException(sprintf('Unable to parse "%s" as the file is not readable.', $input));
            }

            $file = $input;

            if (self::$enablePhpParsing) {
                ob_start();
                $retval = include($file);
                $content = ob_get_clean();
                $input = is_array($retval) ? $retval : $content;

                if (is_array($input)) {
                    return $input;
                }
            } else {
                $input = file_get_contents($file);
            }
        }

        $yaml = new Symfony_Component_Yaml_Parser();

        try {
            return $yaml->parse($input);
        } catch (Symfony_Component_Yaml_Exception_ParseException $e) {
            if ($file) {
                $e->setParsedFile($file);
            }
            throw $e;
        }
    }

    static public function dump($array, $inline = 2)
    {
        $yaml = new Symfony_Component_Yaml_Dumper();
        return $yaml->dump($array, $inline);
    }
}
