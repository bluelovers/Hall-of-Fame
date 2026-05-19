<?php

/**
 * HOF YAML 類別 — 處理 YAML 檔案的載入與儲存
 * HOF YAML Class — Handles YAML file loading and saving
 *
 * @author bluelovers
 * @copyright 2012
 *
 * ==================== 支援的 YAML 語法 / Supported YAML Syntax ====================
 *
 * 【基本純量 / Basic Scalars】
 * - 字串: value, "quoted", 'single-quoted'
 * - 數字: 42, 3.14
 * - 布林: true, false, yes, no, on, off
 * - 空值: null, ~
 *
 * 【行內格式 / Inline Formats】
 * - 行內陣列: [item1, item2, item3]
 * - 行內物件: {key1: value1, key2: value2}
 * - 巢狀行內: [{a: 1, b: 2}, {c: 3, d: 4}]
 * - ✓ 支援巢狀結構中的逗號（修復項目）/ Commas in nested structures (fixed)
 *
 * 【縮排鍵值對 / Indented Key-Value】
 * - 巢狀物件:
 *   parent:
 *     child: value
 *     sub:
 *       key: value
 *
 * 【塊標量樣式 / Block Scalar Styles】
 * - |    Literal — 保留所有換行（Preserve all newlines）
 * - |-   Literal Strip — 保留換行，去除末尾換行（Preserve, strip trailing）
 * - >    Folded — 折疊換行為空格（Fold newlines to spaces）
 * - >-   Folded Strip — 折疊並去除末尾換行（Fold and strip trailing）
 *
 * 【不支援 / Not Supported】
 * - 錨點與別名（&anchor, *alias）/ Anchors and aliases
 * - 標籤（!!str, !!int）/ Tags
 * - 複合鍵 / Complex mapping keys
 *
 * 【容易用到但不支援 — 請改用替代方案 / Commonly Used But Not Supported — Use Alternatives】
 *
 * ❌ 不支援：多行縮排列表（- item 格式）
 *    - list:
 *      - item1
 *      - item2
 *    Not supported: Multi-line indented lists (- item format)
 *
 * ✅ 替代方案：使用行內陣列
 *    - list: [item1, item2]
 *    Alternative: Use inline array format
 *
 * ❌ 不支援：混用縮排列表與塊標量
 *    - items:
 *      - name: A
 *        desc: |
 *          多行文字
 *    Not supported: Mixing indented lists with block scalars
 *
 * ✅ 替代方案：使用行內物件與塊標量
 *    - items: [{name: "A", desc: "單行"}]
 *    或使用縮排鍵值對（非列表）
 *    Alternative: Use inline objects or indented key-value (not lists)
 *
 * ==================== 範例 / Examples ====================
 *
 * // 行內陣列與物件 / Inline array and object
 * data: [{id: 1, name: "A"}, {id: 2, name: "B"}]
 *
 * // 塊標量 / Block scalars
 * description: >-
 *   這是一段描述，
 *   會被折疊成一行。
 * content: |
 *   第一行
 *   第二行
 *
 * // 混合使用 / Mixed usage
 * config:
 *   items: [a, b, c]
 *   text: >
 *     多行文字
 *     折疊顯示
 */

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

        /** 使用括號計數來正確識別頂層逗號，避免錯誤分割巢狀結構 / Use bracket counting to identify top-level commas */
        $parts = array();
        $currentPart = '';
        $braceDepth = 0;
        $bracketDepth = 0;
        $inQuote = false;
        $quoteChar = null;
        $len = strlen($value);

        for ($i = 0; $i < $len; $i++) {
            $char = $value[$i];

            /** 處理引號內的字元，跳過引號內的所有特殊字元 / Handle quoted characters, skip all special chars inside quotes */
            if ($inQuote) {
                $currentPart .= $char;
                if ($char === $quoteChar && ($i === 0 || $value[$i - 1] !== '\\')) {
                    $inQuote = false;
                    $quoteChar = null;
                }
                continue;
            }

            /** 檢查是否進入引號狀態 / Check if entering quote state */
            if ($char === '"' || $char === "'") {
                $currentPart .= $char;
                $inQuote = true;
                $quoteChar = $char;
                continue;
            }

            /** 追蹤大括號深度，識別巢狀物件 / Track brace depth for nested objects */
            if ($char === '{') {
                $braceDepth++;
                $currentPart .= $char;
            } elseif ($char === '}') {
                $braceDepth--;
                $currentPart .= $char;
            } elseif ($char === '[') {
                $bracketDepth++;
                $currentPart .= $char;
            } elseif ($char === ']') {
                $bracketDepth--;
                $currentPart .= $char;
            } elseif ($char === ',' && $braceDepth === 0 && $bracketDepth === 0) {
                /** 頂層逗號：完成當前項目 / Top-level comma: complete current item */
                $parts[] = trim($currentPart);
                $currentPart = '';
            } else {
                $currentPart .= $char;
            }
        }

        /** 添加最後一個項目 / Add final item */
        if (trim($currentPart) !== '') {
            $parts[] = trim($currentPart);
        }

        $result = array();
        foreach ($parts as $part) {
            if (trim($part) !== '') {
                $result[] = self::parse(trim($part));
            }
        }

        return $result;
    }

    public static function parseMapping($value)
    {
        if (trim($value) === '') return array();

        /** 使用括號計數來正確識別頂層逗號，避免錯誤分割巢狀結構 / Use bracket counting to identify top-level commas */
        $parts = array();
        $currentPart = '';
        $braceDepth = 0;
        $bracketDepth = 0;
        $inQuote = false;
        $quoteChar = null;
        $len = strlen($value);

        for ($i = 0; $i < $len; $i++) {
            $char = $value[$i];

            /** 處理引號內的字元 / Handle quoted characters */
            if ($inQuote) {
                $currentPart .= $char;
                if ($char === $quoteChar && ($i === 0 || $value[$i - 1] !== '\\')) {
                    $inQuote = false;
                    $quoteChar = null;
                }
                continue;
            }

            /** 檢查是否進入引號狀態 / Check if entering quote state */
            if ($char === '"' || $char === "'") {
                $currentPart .= $char;
                $inQuote = true;
                $quoteChar = $char;
                continue;
            }

            /** 追蹤大括號深度 / Track brace depth */
            if ($char === '{') {
                $braceDepth++;
                $currentPart .= $char;
            } elseif ($char === '}') {
                $braceDepth--;
                $currentPart .= $char;
            } elseif ($char === '[') {
                $bracketDepth++;
                $currentPart .= $char;
            } elseif ($char === ']') {
                $bracketDepth--;
                $currentPart .= $char;
            } elseif ($char === ',' && $braceDepth === 0 && $bracketDepth === 0) {
                /** 頂層逗號：完成當前項目 / Top-level comma: complete current item */
                $parts[] = trim($currentPart);
                $currentPart = '';
            } else {
                $currentPart .= $char;
            }
        }

        /** 添加最後一個項目 / Add final item */
        if (trim($currentPart) !== '') {
            $parts[] = trim($currentPart);
        }

        $result = array();
        foreach ($parts as $part) {
            if (preg_match('/^([^:]+):\s*(.*)$/s', trim($part), $m)) {
                $key = self::parse(trim($m[1]));
                /** 修正：YAML 布林關鍵字 (no, yes, true, false, on, off) 及 null 作為 key 時應保留為字串
                 * Fix: YAML boolean keywords (no, yes, true, false, on, off) and null should remain as strings when used as keys */
                if (is_bool($key) || is_null($key)) {
                    $key = trim($m[1]);
                }
                $result[$key] = self::parse(trim($m[2]));
            }
        }

        return $result;
    }

    public static function dump($value, $inline = 0, $objectSupport = false)
    {
        if (null === $value) {
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
