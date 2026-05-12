<?php

/**
 * YAML 解析器 — 支援行內格式與多行縮排格式
 * YAML Parser - Supports inline format and multi-line block format
 *
 * 支援的塊標量樣式 / Supported block scalar styles:
 * - |    : Literal (保留換行 / Preserve newlines)
 * - |-   : Literal Strip (去除末尾換行 / Strip trailing newline)
 * - >    : Folded (折疊換行為空格 / Fold newlines to spaces)
 * - >-   : Folded Strip (折疊並去除末尾換行 / Fold and strip trailing newline)
 */
class Symfony_Component_Yaml_Parser
{
    private $offset = 0;
    private $lines = array();
    private $currentLineNb = -1;
    private $currentLine = '';
    private $refs = array();

    /**
     * 解析 YAML 內容
     * Parse YAML content
     *
     * @param string $input YAML 內容或檔案路徑 / YAML content or file path
     * @return array 解析後的資料 / Parsed data
     */
    public function parse($input, $exceptionOnInvalidType = false, $objectSupport = false)
    {
        /** 如果是檔案路徑，讀取內容 / If file path, read content */
        if (strpos($input, "\n") === false && is_file($input)) {
            $input = file_get_contents($input);
        }

        $this->lines = explode("\n", $input);
        $this->currentLineNb = -1;
        $this->currentLine = '';
        $this->offset = 0;
        $this->refs = array();

        /** 從根層級開始解析 / Start parsing from root level */
        return $this->parseBlockContent(0, 0);
    }

    /**
     * 解析區塊內容（核心遞迴方法）
     * Parse block content (core recursive method)
     *
     * @param int $startLine 起始行號 / Starting line number
     * @param int $baseIndent 基礎縮排量 / Base indentation level
     * @return array|null 解析結果 / Parsed result
     */
    private function parseBlockContent($startLine, $baseIndent)
    {
        $data = null;
        $lineIdx = $startLine;
        $lastLineWasListItem = false;

        while ($lineIdx < count($this->lines)) {
            $this->currentLineNb = $lineIdx;
            $this->currentLine = $this->lines[$lineIdx];

            /** 跳過空行與註解 / Skip empty lines and comments */
            if ($this->isLineEmpty($this->currentLine)) {
                $lineIdx++;
                continue;
            }

            $lineIndent = $this->getLineIndent($this->currentLine);

            /** 如果縮排小於基礎縮排，區塊結束 / If indent less than base, block ends */
            if ($lineIndent < $baseIndent) {
                break;
            }

            /** 如果縮排大於基礎縮排，屬於上一行的子內容 / If indent greater, belongs to previous line */
            if ($lineIndent > $baseIndent && !$lastLineWasListItem) {
                $lineIdx++;
                continue;
            }

            $line = trim($this->currentLine);
            $lastLineWasListItem = false;

            /**
             * 列表項目: "- item" / List item: "- item"
             */
            if (preg_match('/^-(?:\s+(.*))?$/', $line, $m)) {
                $item = isset($m[1]) && '' !== $m[1] ? $this->parseValue($m[1], $lineIdx, $lineIndent) : null;

                /** 檢查是否有縮排子內容 / Check for indented child content */
                $nextIdx = $lineIdx + 1;
                if ($nextIdx < count($this->lines)) {
                    $nextLine = $this->lines[$nextIdx];
                    if (!$this->isLineEmpty($nextLine)) {
                        $nextIndent = $this->getLineIndent($nextLine);
                        if ($nextIndent > $lineIndent) {
                            /** 解析子區塊 / Parse child block */
                            $childData = $this->parseBlockContent($nextIdx, $nextIndent);
                            if ($childData !== null) {
                                if ($item !== null && !is_array($item)) {
                                    $item = array('_value' => $item);
                                }
                                if (is_array($item) && is_array($childData)) {
                                    $item = array_merge($item, $childData);
                                } else {
                                    $item = $childData;
                                }
                            }
                            /** 跳過已處理的行 / Skip processed lines */
                            while ($lineIdx + 1 < count($this->lines) &&
                                   $this->getLineIndent($this->lines[$lineIdx + 1]) > $lineIndent) {
                                $lineIdx++;
                            }
                        }
                    }
                }

                $data[] = $item;
                $lastLineWasListItem = true;
                $lineIdx++;
                continue;
            }

            /**
             * 鍵值對: "key: value" / Key-value: "key: value"
             */
            elseif (preg_match('/^(\".+?\"|\'.+?\'|[^#]+?):(?:\s+(.*))?$/', $line, $m)) {
                $key = Symfony_Component_Yaml_Inline::parse(trim($m[1]));
                $val = null;

                if (isset($m[2]) && '' !== trim($m[2])) {
                    /** 檢查塊標量標記 / Check for block scalar indicator */
                    $valStr = trim($m[2]);
                    if (preg_match('/^(\||>)(-)?$/', $valStr, $scalarMatch)) {
                        /** 解析塊標量 / Parse block scalar */
                        $style = $scalarMatch[1]; // '|' 或 '>' / '|' or '>'
                        $strip = isset($scalarMatch[2]) && $scalarMatch[2] === '-'; // 是否去除末尾換行 / Whether to strip trailing newline
                        $val = $this->parseBlockScalar($lineIdx + 1, $lineIndent, $style, $strip);
                        /** 跳過已處理的塊內容 - 使用 parseBlockScalar 實際處理到的行號 / Skip processed block content */
                        $blockEndLine = $lineIdx + 1;
                        while ($blockEndLine < count($this->lines)) {
                            $checkLine = $this->lines[$blockEndLine];
                            if ($this->isLineEmpty($checkLine)) {
                                $blockEndLine++;
                                continue;
                            }
                            $checkIndent = $this->getLineIndent($checkLine);
                            /** 找到縮排小於等於當前 key 的行，塊結束 / Found line with indent <= current key, block ends */
                            if ($checkIndent <= $lineIndent) {
                                break;
                            }
                            $blockEndLine++;
                        }
                        $lineIdx = $blockEndLine - 1;  /** 減 1 因為主迴圈會再 ++ / Minus 1 because main loop will increment */
                    } else {
                        /** 解析行內值 / Parse inline value */
                        $val = $this->parseValue($valStr, $lineIdx, $lineIndent);
                    }
                } else {
                    /** 無行內值，檢查縮排子內容 / No inline value, check for indented child content */
                    $nextIdx = $lineIdx + 1;
                    if ($nextIdx < count($this->lines)) {
                        $nextLine = $this->lines[$nextIdx];
                        if (!$this->isLineEmpty($nextLine)) {
                            $nextIndent = $this->getLineIndent($nextLine);
                            if ($nextIndent > $lineIndent) {
                                $val = $this->parseBlockContent($nextIdx, $nextIndent);
                                /** 跳過已處理的行 / Skip processed lines */
                                while ($lineIdx + 1 < count($this->lines) &&
                                       $this->getLineIndent($this->lines[$lineIdx + 1]) > $lineIndent) {
                                    $lineIdx++;
                                }
                            }
                        }
                    }
                }

                if ($data === null) {
                    $data = array();
                }
                $data[$key] = $val;
                $lineIdx++;
                continue;
            }

            /**
             * 純量值 / Scalar value
             */
            else {
                if ($data === null) {
                    $data = Symfony_Component_Yaml_Inline::parse($line);
                }
                $lineIdx++;
            }
        }

        $this->currentLineNb = $lineIdx > 0 ? $lineIdx - 1 : 0;
        if (isset($this->lines[$this->currentLineNb])) {
            $this->currentLine = $this->lines[$this->currentLineNb];
        }

        return $data;
    }

    /**
     * 解析塊標量（Literal | 或 Folded >）
     * Parse block scalar (Literal | or Folded >)
     *
     * @param int $startLine 起始行號 / Starting line number
     * @param int $baseIndent 基礎縮排量（key 的縮排）/ Base indentation (key's indent)
     * @param string $style 樣式 '|' 或 '>' / Style '|' or '>'
     * @param bool $strip 是否去除末尾換行 / Whether to strip trailing newline
     * @return string 解析後的內容 / Parsed content
     */
    private function parseBlockScalar($startLine, $baseIndent, $style, $strip)
    {
        $lines = array();
        $lineIdx = $startLine;
        $contentIndent = null;  /** 內容縮排量（從第一個非空行推斷）/ Content indent (inferred from first non-empty line) */

        /** 首先找到內容縮排量 / First, find the content indentation level */
        $tempIdx = $startLine;
        while ($tempIdx < count($this->lines)) {
            $tempLine = $this->lines[$tempIdx];
            if (!$this->isLineEmpty($tempLine)) {
                $tempIndent = $this->getLineIndent($tempLine);
                /** 內容必須比 baseIndent 深 / Content must be deeper than baseIndent */
                if ($tempIndent > $baseIndent) {
                    $contentIndent = $tempIndent;
                    break;
                } elseif ($tempIndent <= $baseIndent) {
                    /** 沒有內容或內容縮排不足 / No content or insufficient indent */
                    return '';
                }
            }
            $tempIdx++;
        }

        /** 如果沒有找到內容，返回空字串 / If no content found, return empty string */
        if ($contentIndent === null) {
            return '';
        }

        /** 現在解析內容 / Now parse the content */
        $lineIdx = $startLine;
        $consecutiveEmptyLines = 0;

        while ($lineIdx < count($this->lines)) {
            $line = $this->lines[$lineIdx];

            /** 空行處理 / Empty line handling */
            if ($this->isLineEmpty($line)) {
                $consecutiveEmptyLines++;
                $lineIdx++;

                /** 檢查下一個非空行，如果縮排不足則結束塊 / Check next non-empty line, if indent insufficient then end block */
                $peekIdx = $lineIdx;
                while ($peekIdx < count($this->lines) && $this->isLineEmpty($this->lines[$peekIdx])) {
                    $peekIdx++;
                }
                if ($peekIdx < count($this->lines)) {
                    $peekIndent = $this->getLineIndent($this->lines[$peekIdx]);
                    /** 如果下一個非空行縮排小於等於 baseIndent，塊結束 / If next non-empty line indent <= baseIndent, block ends */
                    if ($peekIndent <= $baseIndent) {
                        break;
                    }
                }

                /** 保留空行（但最多保留一個作為段落分隔）/ Preserve empty line (but only one as paragraph separator) */
                if ($consecutiveEmptyLines <= 1 || $style === '|') {
                    $lines[] = '';
                }
                continue;
            }

            $consecutiveEmptyLines = 0;
            $lineIndent = $this->getLineIndent($line);

            /** 縮排小於內容縮排，塊結束 / Indent less than content indent, block ends */
            if ($lineIndent < $contentIndent) {
                break;
            }

            /** 提取內容（去除內容縮排） / Extract content (remove content indentation) */
            $content = substr($line, $contentIndent);
            $lines[] = $content;
            $lineIdx++;
        }

        /** 根據樣式處理內容 / Process content according to style */
        if ($style === '>') {
            /** Folded: 將非空行後的換行轉為空格 / Folded: convert newlines after non-empty lines to spaces */
            $result = '';
            $prevNonEmpty = false;
            foreach ($lines as $i => $line) {
                if ($line === '') {
                    /** 空行保留為換行 / Empty lines preserve as newlines */
                    $result .= "\n";
                    $prevNonEmpty = false;
                } else {
                    if ($prevNonEmpty && $i > 0) {
                        /** 前一行非空，加空格 / Previous non-empty, add space */
                        $result .= ' ';
                    }
                    $result .= $line;
                    $prevNonEmpty = true;
                }
            }
        } else {
            /** Literal: 保留所有換行 / Literal: preserve all newlines */
            $result = implode("\n", $lines);
        }

        /** 去除末尾換行（如果需要） / Strip trailing newline if needed */
        if ($strip) {
            $result = rtrim($result, "\n");
        }

        return $result;
    }

    /**
     * 檢查行是否為空或註解
     * Check if line is empty or a comment
     *
     * @param string $line 要檢查的行 / Line to check
     * @return bool 是否為空行 / Whether line is empty
     */
    private function isLineEmpty($line)
    {
        $trimmed = trim($line);
        return '' === $trimmed || '#' === $trimmed[0];
    }

    /**
     * 解析值（行內值或引導到子區塊）
     * Parse value (inline value or guide to child block)
     *
     * @param string $value 值字串 / Value string
     * @param int $lineIdx 當前行號 / Current line index
     * @param int $lineIndent 當前行縮排 / Current line indentation
     * @return mixed 解析結果 / Parsed result
     */
    private function parseValue($value, $lineIdx = null, $lineIndent = null)
    {
        $trimmed = trim($value);

        /** 檢查塊標量標記 / Check for block scalar indicator */
        if (preg_match('/^(\||>)(-)?$/', $trimmed, $scalarMatch)) {
            $style = $scalarMatch[1];
            $strip = isset($scalarMatch[2]) && $scalarMatch[2] === '-';
            return $this->parseBlockScalar($lineIdx + 1, $lineIndent, $style, $strip);
        }

        return Symfony_Component_Yaml_Inline::parse($trimmed);
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

    /**
     * 取得行的縮排數量（空格或 Tab）
     * Get line indentation level (spaces or tabs)
     */
    private function getLineIndent($line)
    {
        $indent = 0;
        $len = strlen($line);
        while ($indent < $len && ($line[$indent] === ' ' || $line[$indent] === "\t")) {
            $indent++;
        }
        return $indent;
    }
}
