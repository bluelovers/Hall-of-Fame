<?php

class Symfony_Component_Yaml_Dumper
{
    public function dump($input, $inline = 0, $indent = 0)
    {
        $output = '';
        $prefix = str_repeat('  ', $indent);

        if ($input instanceof ArrayObject || $input instanceof HOF_Class_Array)
        {
            $input = $input->getArrayCopy();
        }

        if ($inline <= 0 || !is_array($input) || !$this->isHash($input))
        {
            $result = Symfony_Component_Yaml_Inline::dump($input, $inline);
            if ($prefix !== '' && strpos($result, "\n") === false)
            {
                $result = $prefix . $result;
            }
            return $result;
        }

        foreach ($input as $key => $value)
        {
            if (is_array($value) && $this->isHash($value))
            {
                $output .= sprintf('%s%s:', $prefix, Symfony_Component_Yaml_Inline::dump($key)) . "\n";
                $output .= $this->dump($value, $inline - 1, $indent + 1);
            }
            elseif (is_array($value))
            {
                $valStr = Symfony_Component_Yaml_Inline::dump($value);
                $output .= sprintf('%s%s: %s', $prefix, Symfony_Component_Yaml_Inline::dump($key), $valStr) . "\n";
            }
            else
            {
                $valStr = Symfony_Component_Yaml_Inline::dump($value);
                $output .= sprintf('%s%s: %s', $prefix, Symfony_Component_Yaml_Inline::dump($key), $valStr) . "\n";
            }
        }

        return $output;
    }

    private function isHash($value)
    {
        if (!is_array($value)) return false;
        $expectedKey = 0;
        foreach ($value as $key => $val)
        {
            if ($key !== $expectedKey++) return true;
        }
        return false;
    }
}
