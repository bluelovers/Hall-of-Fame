<?php

/**
 * 資源檔案 Roundtrip 測試 + 資料類型驗證
 * Resource File Roundtrip Test + Data Type Validation
 *
 * 1. 驗證 Resource 目錄下的所有 YAML 檔案在讀取、序列化、再讀取後資料保持一致
 *    Verify that all YAML files in the Resource directory maintain data consistency after loading, serialization, and re-parsing.
 *
 * 2. 驗證分析後的資料類型是否正確（修正 YAML boolean 關鍵字作為 key 被誤判的問題）
 *    Verify that parsed data types are correct (fix for YAML boolean keywords misinterpreted when used as keys)
 */
class ResourceRoundtripTest extends PHPUnit_Framework_TestCase
{
    /**
     * 是否允許字串欄位為空值（如 name: ''）
     * Whether to allow empty string values (e.g., name: '')
     *
     * true:  允許空字串（相容舊資料）
     * false: 不允許空字串，空字串視為測試失敗（預設）
     */
    const ALLOW_EMPTY_STRING = false;

    /** 資源根目錄路徑 / Resource root directory path */
    private $resourcePath;

    /**
     * 測試初始化
     * Test initialization
     */
    protected function setUp()
    {
        $this->resourcePath = BASE_TRUST_PATH . 'HOF/Resource';
    }

    // ================================================================
    // Roundtrip 測試 / Roundtrip Tests
    // ================================================================

    /**
     * 測試 Resource 目錄下的所有 YAML 檔案
     * Test all YAML files in the Resource directory
     *
     * @dataProvider yamlFileProvider
     */
    public function testResourceRoundtrip($relativeFile)
    {
        $fullPath = $this->resourcePath . DIRECTORY_SEPARATOR . $relativeFile;

        /** 載入原始資料 / Load original data */
        $originalData = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($originalData, "Failed to load data from: $relativeFile");

        /** 序列化為 YAML / Serialize to YAML */
        $dumpedYaml = HOF_Class_Yaml::dump($originalData);
        $this->assertNotEmpty($dumpedYaml, "Failed to dump data from: $relativeFile");

        /** 重新解析 YAML / Re-parse YAML */
        $reparsedData = HOF_Class_Yaml::parse($dumpedYaml);

        /**
         * 驗證資料一致性
         * Verify data consistency
         *
         * 使用 assertEquals 而非 assertSame，因為 YAML 序列化可能會影響深層結構的類型（如物件變陣列）
         * 但 HOF_Class_Yaml::load 已處理過 addslashes 等，Roundtrip 應保持內容相等
         * Use assertEquals instead of assertSame because YAML serialization might affect deep structure types (e.g. object to array)
         * But HOF_Class_Yaml::load already handles addslashes, so roundtrip should maintain value equality
         */
        $this->assertEquals($originalData, $reparsedData, "Data mismatch after roundtrip in: $relativeFile");
    }

    // ================================================================
    // Key 類型驗證 / Key Type Validation
    // ================================================================

    /**
     * 取得 Item 資源目錄的完整路徑
     * Get the full path to the Item resource directory
     */
    private function getItemResourcePath()
    {
        return $this->resourcePath . DIRECTORY_SEPARATOR . 'Item';
    }

    /**
     * 測試 Item YAML 中 `no` 鍵是否存在且為字串鍵（非整數 0）
     * Test that the `no` key exists as a string key (not integer 0) in Item YAML
     *
     * 這項測試驗證 #1 修復：YAML 布林關鍵字 `no` 不應被誤判為 boolean false
     * This test validates the #1 fix: YAML boolean keyword `no` should not be misparsed as boolean false
     *
     * @dataProvider itemYamlFileProvider
     */
    public function testItemKeyNoIsStringKey($relativeFile)
    {
        $fullPath = $this->getItemResourcePath() . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");

        /**
         * 驗證 `no` 鍵存在於 parsed data 中
         * Verify `no` key exists in parsed data
         *
         * 如果 array_key_exists('no', $data) 為 false，表示 key 被轉成了整數 0
         * If array_key_exists('no', $data) is false, the key was converted to integer 0
         */
        $this->assertArrayHasKey('no', $data,
            "Key 'no' is missing from parsed data (may have been converted to integer). File: $relativeFile");

        /**
         * 驗證鍵 `0` 不存在（它不應取代 `no`）
         * Verify that key 0 does not exist (it should not be a substitute for 'no')
         */
        $this->assertArrayNotHasKey(0, $data,
            "Key 0 (integer) should not exist. It would mean 'no' was parsed as boolean false. File: $relativeFile");

        /**
         * 驗證 `no` 的值是整數或數值字串
         * Verify `no` value is integer or numeric string
         */
        $this->assertTrue(is_int($data['no']) || is_numeric($data['no']),
            "Value of key 'no' should be integer or numeric string. File: $relativeFile, type: " . gettype($data['no']));

        /**
         * 驗證 `no` 的值不為空
         * Verify `no` value is not empty/falsy
         */
        $this->assertNotEmpty($data['no'],
            "Value of key 'no' should not be empty. File: $relativeFile");
    }

    /**
     * 測試 Item YAML 中的 `dh` 欄位是否正確解析為布林值
     * Test that the `dh` field in Item YAML is correctly parsed as boolean
     *
     * @dataProvider itemYamlFileProvider
     */
    public function testItemTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getItemResourcePath() . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");

        /**
         * 字串欄位驗證 / String field validation
         *
         * 注意：若 ALLOW_EMPTY_STRING 為 false，空字串視為測試失敗
         * Note: If ALLOW_EMPTY_STRING is false, empty strings are treated as test failure
         *
         * 部分材料在舊資料中 name/base_name 可能為空字串，若需相容請將 ALLOW_EMPTY_STRING 設為 true
         * Some material files may have empty name/base_name in legacy data; set ALLOW_EMPTY_STRING to true for compatibility
         */
        $stringFields = array('name', 'type', 'img', 'base_name', 'type2');
        foreach ($stringFields as $field) {
            if (array_key_exists($field, $data)) {
                $this->assertInternalType('string', $data[$field],
                    "Field '$field' should be string. File: $relativeFile, type: " . gettype($data[$field]));
                if (!self::ALLOW_EMPTY_STRING) {
                    $this->assertNotEmpty($data[$field],
                        "Field '$field' should not be empty. File: $relativeFile. (Set ALLOW_EMPTY_STRING = true to allow empty strings)");
                }
            }
        }

        /**
         * 陣列欄位驗證 / Array field validation
         */
        $arrayFields = array('atk', 'def', 'need');
        foreach ($arrayFields as $field) {
            if (array_key_exists($field, $data)) {
                $this->assertInternalType('array', $data[$field],
                    "Field '$field' should be array. File: $relativeFile");
                $this->assertNotEmpty($data[$field],
                    "Field '$field' should not be empty. File: $relativeFile");

                /**
                 * 檢查陣列元素類型 / Check array element types
                 */
                if ($field === 'atk' || $field === 'def') {
                    foreach ($data[$field] as $k => $v) {
                        $this->assertTrue(is_int($v) || is_float($v),
                            "Value of $field[$k] should be numeric. File: $relativeFile, type: " . gettype($v));
                    }
                }
            }
        }

        /**
         * `dh` 欄位應為布林值（當存在時）
         * `dh` field should be boolean (when present)
         */
        if (array_key_exists('dh', $data)) {
            $this->assertInternalType('bool', $data['dh'],
                "Field 'dh' should be boolean. File: $relativeFile, type: " . gettype($data['dh']));
        }

        /**
         * `buy` 欄位應為字串（YAML 中為引號字串）
         * `buy` field should be string (quoted in YAML)
         */
        if (array_key_exists('buy', $data)) {
            $this->assertInternalType('string', $data['buy'],
                "Field 'buy' should be string. File: $relativeFile, type: " . gettype($data['buy']));
        }

        /**
         * `handle` 欄位應為字串（YAML 中為引號字串）
         * `handle` field should be string (quoted in YAML)
         */
        if (array_key_exists('handle', $data)) {
            $this->assertInternalType('string', $data['handle'],
                "Field 'handle' should be string. File: $relativeFile, type: " . gettype($data['handle']));
        }
    }

    // ================================================================
    // HOF_Class_Item 整合驗證 / HOF_Class_Item Integration Validation
    // ================================================================

    /**
     * 測試 HOF_Model_Data::getItemData() 回傳資料包含 `no` 鍵
     * Test that HOF_Model_Data::getItemData() returns data with `no` key
     *
     * 這項測試驗證修復後的資料可正確流經資料模型層
     * This test validates that fixed data correctly flows through the data model layer
     */
    public function testGetItemDataHasNoKey()
    {
        /** 使用 shop 清單中的物品編號進行測試 / Use item IDs from shop list for testing */
        $testItems = array(1002, 1100, 3000, 5500, 5000);

        foreach ($testItems as $itemNo) {
            $data = HOF_Model_Data::getItemData($itemNo, true);
            $this->assertNotEmpty($data, "getItemData($itemNo) returned empty data");

            /**
             * 驗證 `no` 鍵存在（不是整數 0）
             * Verify `no` key exists (not integer 0)
             */
            $this->assertArrayHasKey('no', $data,
                "getItemData($itemNo) returned data without 'no' key");
            $this->assertArrayNotHasKey(0, $data,
                "getItemData($itemNo) returned data with integer key 0 (no was parsed as boolean)");

            /**
             * 驗證 `id` 鍵存在（由 getItemData 加入）
             * Verify `id` key exists (added by getItemData)
             */
            $this->assertArrayHasKey('id', $data,
                "getItemData($itemNo) returned data without 'id' key");
            $this->assertEquals($itemNo, $data['id']);

            /**
             * 驗證 `no` 值與 `id` 一致
             * Verify `no` value matches `id`
             */
            $this->assertEquals($data['no'], $data['id'],
                "getItemData($itemNo): 'no' value should match 'id'");
        }
    }

    /**
     * 測試 HOF_Class_Item 建構後 exists() 回傳 true
     * Test that HOF_Class_Item returns true for exists() after construction
     */
    public function testHOFClassItemExists()
    {
        /** 使用 shop 清單中的物品編號進行測試 / Use item IDs from shop list for testing */
        $testItems = array(1002, 1100, 3000, 5500, 5000);

        foreach ($testItems as $itemNo) {
            $item = new HOF_Class_Item($itemNo);

            /**
             * 驗證 exists() 回傳 true
             * Verify exists() returns true
             *
             * 這項測試驗證 #2 修復：HOF_Class_Item 的 exists() 方法依賴 $this->no 的正確性
             * This test validates fix #2: HOF_Class_Item::exists() depends on correct $this->no
             */
            $this->assertTrue($item->exists(),
                "HOF_Class_Item($itemNo)->exists() should be true");
        }
    }

    /**
     * 測試 HOF_Class_Item 的 no() 方法回傳正確格式
     * Test that HOF_Class_Item::no() returns correct format
     *
     * 注意：no() 方法在 PHP 5.6 中使用 printf 直接輸出，回傳值為 printf 的回傳值 (輸出字串長度)
     * Note: no() uses printf directly in PHP 5.6, returns printf's return value (output string length)
     */
    public function testHOFClassItemNo()
    {
        /** 使用 no 方法來驗證 / Use the no() method for validation */
        $item = new HOF_Class_Item(1100);
        $this->assertTrue($item->exists(), "Item(1100)->exists() should be true");

        /**
         * 使用輸出緩衝層捕獲 printf 的直接輸出
         * Use output buffering to capture printf's direct output
         */
        ob_start();
        $noOutput = $item->no();
        $printedOutput = ob_get_clean();

        /**
         * printf 回傳值為輸出字串的長度
         * printf returns the length of the output string
         */
        $this->assertGreaterThan(0, $noOutput,
            "Item(1100)->no() printf return value (length) should be > 0");

        /**
         * 驗證 printf 實際輸出的內容格式為 [xxxx]
         * Verify printf's actual output format is [xxxx]
         */
        $this->assertRegExp('/^\[\d{4}\]$/', $printedOutput,
            "Item(1100)->no() should output formatted number like '[1100]'");

        /**
         * 驗證輸出內容為 [1100]
         * Verify output content is [1100]
         */
        $this->assertEquals('[1100]', $printedOutput,
            "Item(1100)->no() should output '[1100]'");
    }

    // ================================================================
    // DataProvider / Data Providers
    // ================================================================

    /**
     * 提供資源目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the resource directory
     *
     * @return array
     */
    public function yamlFileProvider()
    {
        /**
         * 如果環境尚未完全初始化（如剛啟動），手動定義路徑
         * If environment is not fully initialized, define path manually
         */
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'HOF' . DIRECTORY_SEPARATOR . 'Resource';

        $files = array();
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'yml') {
                    /** 取得相對於 Resource 的路徑 / Get path relative to Resource */
                    $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $files[] = array($relativePath);
                }
            }
        }

        return $files;
    }

    /**
     * 提供 Item 目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the Item resource directory
     *
     * @return array
     */
    public function itemYamlFileProvider()
    {
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'HOF' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'Item';

        $files = array();
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'yml') {
                    $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    /**
                     * 排除快取目錄 / Exclude cache directory
                     */
                    if (strpos($relativePath, 'cache' . DIRECTORY_SEPARATOR) === 0) {
                        continue;
                    }
                    $files[] = array($relativePath);
                }
            }
        }

        return $files;
    }
}
