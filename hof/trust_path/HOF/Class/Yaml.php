<?php

/**
 * HOF YAML 類別 — YAML 檔案處理的主要介面
 * HOF YAML Class — Main interface for YAML file processing
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
 * - ✓ 支援巢狀結構中的逗號（核心修復）/ Commas in nested structures (core fix)
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
 *    此語法目前暫不實裝支援，請改用替代方案
 *    This syntax is currently NOT supported, please use alternatives
 *
 *    ❌ 不支援的寫法 / Not supported:
 *    - item:
 *      - subitem1
 *      - subitem2
 *
 *    ✅ 替代方案：使用行內陣列
 *    Alternative: Use inline array format
 *    data: [{item: [subitem1, subitem2]}]
 *
 * ❌ 不支援：混用縮排列表與塊標量
 *    此語法目前暫不實裝支援，請改用替代方案
 *    This syntax is currently NOT supported, please use alternatives
 *
 *    ❌ 不支援的寫法 / Not supported:
 *    items:
 *      - name: A
 *        desc: |
 *          多行文字
 *      - name: B
 *        desc: |
 *          多行文字
 *
 *    ✅ 替代方案 1：使用行內物件與塊標量（行內陣列 + 縮排鍵值對）
 *    Alternative 1: Use inline array with indented key-value
 *    items:
 *      - {name: "A", desc: "單行文字"}
 *      - {name: "B", desc: "另一行"}
 *
 *    ✅ 替代方案 2：完全使用行內物件
 *    Alternative 2: Fully inline objects
 *    items: [{name: "A", desc: "單行文字"}, {name: "B", desc: "另一行"}]
 *
 * ==================== 使用範例 / Usage Examples ====================
 *
 * // 載入 YAML 檔案 / Load YAML file
 * $data = HOF_Class_Yaml::load('/path/to/file.yml');
 *
 * // 儲存資料到 YAML / Save data to YAML
 * HOF_Class_Yaml::save('/path/to/file.yml', $data);
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

class HOF_Class_Yaml extends Symfony_Component_Yaml_Yaml
{

	const INLINE = 20;
	static $auto_addslashes = false;
	static $auto_fixarray = -1;

	public static function load($file, $enablePhpParsing = false)
	{
		if (HOF_Class_File::is_resource_file($file) || file_exists($file))
		{
			if (HOF_Class_File::is_resource_file($file))
			{
				$data = HOF_Class_File::fp_get_contents($file);
			}
			else
			{
				HOF_Class_File::opened_files_add($file);

				$data = $file;
			}

			$old = HOF_Class_Yaml::$enablePhpParsing;
			if ($old != $enablePhpParsing) HOF_Class_Yaml::$enablePhpParsing = $enablePhpParsing;

			$yaml = self::parse($data);

			if ($old != HOF_Class_Yaml::$enablePhpParsing) HOF_Class_Yaml::$enablePhpParsing = $old;

			if (self::$auto_addslashes)
			{
				$yaml = HOF::stripslashes($yaml);
			}
		}
		else
		{
			$yaml = false;
		}

		return $yaml;
	}

	public static function save($file, $data, $inline = HOF_Class_Yaml::INLINE)
	{
		if (self::$auto_addslashes)
		{
			$data = HOF::addslashes($data);
		}

		if (self::$auto_fixarray !== null && self::$auto_fixarray !== false && self::$auto_fixarray >= -1)
		{
			$data = HOF_Class_Array::_fixArrayRecursive($data, self::$auto_fixarray == -1 ? HOF_Class_Array::ARRAY_RECURSIVE_ALL : self::$auto_fixarray);
		}

		$dump = self::dump($data, $inline);

		if (is_resource($file))
		{
			ftruncate($file, 0);
			rewind($file);
			fputs($file, $dump);

			return true;
		}
		else
		{
			$ret = file_put_contents($file, $dump, LOCK_EX);
		}

		return $ret;
	}

	public static function dump($array, $inline = HOF_Class_Yaml::INLINE)
	{
		return parent::dump($array, $inline);
	}

}

