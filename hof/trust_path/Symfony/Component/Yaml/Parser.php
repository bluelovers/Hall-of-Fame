<?php

class Symfony_Component_Yaml_Parser
{
    private $offset = 0;
    private $lines = array();
    private $currentLineNb = -1;
    private $currentLine = '';
    private $refs = array();

    public function parse($input, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        $this->lines = explode("\n", $input);
        $this->currentLineNb = -1;
        $this->currentLine = '';
        $this->offset = 0;
        $this->refs = array();

        $data = null;
        while ($this->moveToNextLine()) {
            if ($this->isCurrentLineEmpty()) continue;

            /**
             * Skip indented lines at root level
             * Indented lines belong to parent blocks and will be consumed by parseBlock
             */
            $indent = $this->getLineIndent($this->currentLine);
            if ($indent > 0) continue;

            $line = trim($this->currentLine);

            /**
             * Root-level list item: "- item"
             */
            if (preg_match('/^-(?:\s+(.*))?$/', $line, $m)) {
                $item = isset($m[1]) && '' !== $m[1] ? Symfony_Component_Yaml_Inline::parse($m[1]) : null;
                $nextIdx = $this->currentLineNb + 1;
                if ($nextIdx < count($this->lines)) {
                    $nextLine = $this->lines[$nextIdx];
                    $nextIndent = $this->getLineIndent($nextLine);
                    if ('' !== trim($nextLine) && $nextIndent > $indent) {
                        $item = $this->parseBlock($nextIdx, $nextIndent);
                    }
                }
                $data[] = $item;
            }

            /**
             * Root-level key-value pair: "key: value"
             * Merge into accumulator instead of overwriting
             */
            elseif (preg_match('/^([^#]+?):(\s+(.*))?$/', $line, $m)) {
                $key = trim($m[1]);
                $val = isset($m[3]) && '' !== trim($m[3]) ? Symfony_Component_Yaml_Inline::parse(trim($m[3])) : null;

                /**
                 * Check if next line is indented -> parse as block
                 */
                if ($val === null && $this->nextLineIndented($this->currentLineNb)) {
                    $val = $this->parseBlock($this->currentLineNb + 1, $indent + 1);
                }

                if (is_array($data)) {
                    $data[$key] = $val;
                } else {
                    $data = array($key => $val);
                }
            }

            /**
             * Scalar value at root level
             */
            else {
                $data = Symfony_Component_Yaml_Inline::parse($line);
            }
        }

        return $data;
    }

    private function moveToNextLine()
    {
        if ($this->currentLineNb >= count($this->lines) - 1) return false;

        $this->currentLineNb++;

        if ($this->currentLineNb < count($this->lines)) {
            $this->currentLine = $this->lines[$this->currentLineNb];
        }

        $this->offset = 0;

        return true;
    }

    private function isCurrentLineEmpty()
    {
        $line = trim($this->currentLine);
        return '' === $line || '#' === $line[0];
    }

    private function getRealCurrentLineNb()
    {
        return $this->currentLineNb;
    }

    private function parseBlock($currentLineNb, $indent)
    {
        $data = array();
        $lineIdx = $currentLineNb;

        while ($lineIdx < count($this->lines)) {
            $line = $this->lines[$lineIdx];
            if ('' === trim($line)) { $lineIdx++; continue; }

            $lineIndent = $this->getLineIndent($line);
            if ($lineIndent < $indent) break;

            if (preg_match('/^-(?:\s+(.*))?$/', ltrim($line), $m)) {
                $item = isset($m[1]) && '' !== $m[1] ? Symfony_Component_Yaml_Inline::parse($m[1]) : null;
                $nextIdx = $lineIdx + 1;
                if ($nextIdx < count($this->lines)) {
                    $nextLine = $this->lines[$nextIdx];
                    $nextIndent = $this->getLineIndent($nextLine);
                    if ('' !== trim($nextLine) && $nextIndent > $lineIndent) {
                        $item = $this->parseBlock($nextIdx, $nextIndent);
                        $lineIdx = $this->currentLineNb;
                    }
                }
                $data[] = $item;
                $lineIdx++;
                continue;
            }

            if (preg_match('/^([^#]+?):(\s+(.*))?$/', ltrim($line), $m)) {
                $key = trim($m[1]);
                $value = isset($m[3]) && '' !== trim($m[3]) ? Symfony_Component_Yaml_Inline::parse(trim($m[3])) : null;

                if ($value === null && ($this->nextLineIndented($lineIdx))) {
                    $value = $this->parseBlock($lineIdx + 1, $lineIndent + 1);
                    $lineIdx = $this->currentLineNb;
                }

                $data[$key] = $value;
                $lineIdx++;
                continue;
            }

            $lineIdx++;
        }

        if ($currentLineNb > $this->currentLineNb) {
            $this->currentLineNb = $lineIdx - 1;
            if (isset($this->lines[$this->currentLineNb])) {
                $this->currentLine = $this->lines[$this->currentLineNb];
            }
            $this->offset = $indent;
        }

        return $data;
    }

    private function nextLineIndented($lineIdx)
    {
        $nextIdx = $lineIdx + 1;
        if ($nextIdx >= count($this->lines)) return false;

        $nextLine = $this->lines[$nextIdx];
        if ('' === trim($nextLine)) return false;

        $currentIndent = $this->getLineIndent($this->lines[$lineIdx]);
        $nextIndent = $this->getLineIndent($nextLine);

        return $nextIndent > $currentIndent;
    }

    private function getLineIndent($line)
    {
        $indent = 0;
        $len = strlen($line);
        while ($indent < $len && ($line[$indent] === ' ' || $line[$indent] === "\t")) {
            $indent++;
        }
        return $indent;
    }

    private function parseValue($value)
    {
        $value = trim($value);

        if (preg_match('/^([^#]+?):(\s+(.*))?$/', $value, $m)) {
            $key = trim($m[1]);
            $val = isset($m[3]) && '' !== trim($m[3]) ? Symfony_Component_Yaml_Inline::parse(trim($m[3])) : null;
            return array($key => $val);
        }

        return Symfony_Component_Yaml_Inline::parse($value);
    }
}
