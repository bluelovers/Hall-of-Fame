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

/** 載入 Resource 測試共用工具 / Load Resource test shared utilities */
require_once PROJECT_TEST_PATH . '/lib/ResourceTestHelper.php';
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
    // Roundtrip 測試 / Roundtrip Tests (All Resource Types)
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
    // 全域驗證 / Global Validation (All Resource Types)
    // ================================================================

    /**
     * 測試所有資源類型的 YAML 中不存在整數鍵 0
     * Test that integer key 0 does not exist in any parsed YAML
     *
     * 整數鍵 0 是 YAML boolean 關鍵字被誤解為 false 的典型症狀
     * Integer key 0 is a symptom of YAML boolean keywords being misinterpreted as false
     *
     * @dataProvider yamlFileProvider
     */
    public function testNoKeyZeroInAnyResourceType($relativeFile)
    {
        $fullPath = $this->resourcePath . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");

        /**
         * 驗證整數鍵 0 不存在
         * Verify integer key 0 does not exist
         */
        $this->assertArrayNotHasKey(0, $data,
            "Key 0 (integer) should not exist. File: $relativeFile");

        /**
         * 遞迴檢查巢狀陣列中是否有整數鍵 0 作為字串鍵的替代
         * Recursively check nested arrays for integer key 0 as a replacement for string keys
         */
        $this->assertNoNestedKeyZero($data, $relativeFile);
    }

    /**
     * 遞迴檢查巢狀陣列中是否有整數鍵 0 且同時存在對應的字串鍵
     * Recursively check nested arrays for key 0 when string keys exist
     */
    private function assertNoNestedKeyZero($data, $file, $path = '')
    {
        if (!is_array($data)) return;

        foreach ($data as $key => $value) {
            $currentPath = $path ? "$path.$key" : (string)$key;

            if (is_array($value)) {
                /**
                 * 如果是關聯陣列（有字串鍵），檢查是否有鍵 0
                 * If it's an associative array (has string keys), check for key 0
                 */
                $hasStringKey = false;
                foreach ($value as $k => $v) {
                    if (is_string($k)) {
                        $hasStringKey = true;
                        break;
                    }
                }
                if ($hasStringKey) {
                    $this->assertArrayNotHasKey(0, $value,
                        "Key 0 (integer) should not exist in nested array at '$currentPath'. File: $file");
                }

                /** 繼續遞迴 / Continue recursion */
                $this->assertNoNestedKeyZero($value, $file, $currentPath);
            }
        }
    }

    // ================================================================
    // 共通輔助方法 / Common Helper Methods
    // ================================================================

    /**
     * 取得指定資源類型的目錄完整路徑
     * Get the full path to a resource directory
     *
     * @param string $type 資源類型（如 Item, Mon, Skill）/ Resource type
     * @return string 完整路徑 / Full path
     */
    private function getResourcePath($type)
    {
        return $this->resourcePath . DIRECTORY_SEPARATOR . $type;
    }

    /**
     * 驗證字串欄位
     * Validate string fields
     */
    private function assertStringFields($data, $fields, $file, $fullPath)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $this->assertInternalType('string', $data[$field],
                    "Field '$field' should be string. File: $file, type: " . gettype($data[$field]));
                if (!self::ALLOW_EMPTY_STRING) {
                    $this->assertNotEmpty($data[$field],
                        "Field '$field' should not be empty. File: $file. Path: $fullPath");
                }
            }
        }
    }

    private function assertStringFieldsAllowEmpty($data, $fields, $file, $fullPath)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $this->assertInternalType('string', $data[$field],
                    "Field '$field' should be string. File: $file, type: " . gettype($data[$field]));
            }
        }
    }

    /**
     * 驗證整數或數值字串欄位
     * Validate integer or numeric string fields
     */
    private function assertNumericFields($data, $fields, $file, $fullPath)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $val = $data[$field];
                $this->assertTrue(is_int($val) || is_float($val) || is_numeric($val),
                    "Field '$field' should be numeric. File: $file, type: " . gettype($val) . ", Path: $fullPath");
            }
        }
    }

    /**
     * 驗證整數陣列欄位
     * Validate integer array fields
     */
    private function assertIntegerArrayFields($data, $fields, $file)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $this->assertInternalType('array', $data[$field],
                    "Field '$field' should be array. File: $file");
                foreach ($data[$field] as $k => $v) {
                    $this->assertTrue(is_int($v) || is_float($v),
                        "Value of $field[$k] should be numeric. File: $file, type: " . gettype($v));
                }
            }
        }
    }

    /**
     * 驗證 `no` 鍵存在且型別正確
     * Validate `no` key exists with correct type
     */
    private function assertNoKeyCorrect($data, $file)
    {
        $this->assertArrayHasKey('no', $data,
            "Key 'no' is missing. File: $file");
        $this->assertArrayNotHasKey(0, $data,
            "Key 0 (integer) should not exist. File: $file");
    }

    // ================================================================
    // Item 資料類型驗證 / Item Data Type Validation
    // ================================================================

    /**
     * 測試 Item YAML 中 `no` 鍵存在且為字串鍵（非整數 0）
     * Test that the `no` key exists as a string key (not integer 0)
     *
     * @dataProvider itemYamlFileProvider
     */
    public function testItemKeyNoIsStringKey($relativeFile)
    {
        $fullPath = $this->getResourcePath('Item') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        $this->assertTrue(is_int($data['no']) || is_numeric($data['no']),
            "Value of key 'no' should be integer or numeric string. File: $relativeFile, type: " . gettype($data['no']));
        $this->assertNotEmpty($data['no'],
            "Value of key 'no' should not be empty. File: $relativeFile");
    }

    /**
     * 測試 Item YAML 各欄位型別正確性
     * Test Item YAML field type correctness
     *
     * @dataProvider itemYamlFileProvider
     */
    public function testItemTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Item') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");

        /** 字串欄位 / String fields */
        $this->assertStringFields($data, array('name', 'type', 'img', 'base_name', 'type2'), $relativeFile, $fullPath);

        /** 數值陣列欄位 / Numeric array fields */
        $this->assertIntegerArrayFields($data, array('atk', 'def'), $relativeFile);

        /** `need` 應為陣列（關聯陣列） / `need` should be array */
        if (array_key_exists('need', $data)) {
            $this->assertInternalType('array', $data['need'],
                "Field 'need' should be array. File: $relativeFile");
            $this->assertNotEmpty($data['need'],
                "Field 'need' should not be empty. File: $relativeFile");
        }

        /** `dh` 應為布林值（當存在時） / `dh` should be boolean (when present) */
        if (array_key_exists('dh', $data)) {
            $this->assertInternalType('bool', $data['dh'],
                "Field 'dh' should be boolean. File: $relativeFile, type: " . gettype($data['dh']));
        }

        /** `buy`, `handle` 應為字串 / `buy`, `handle` should be string */
        $this->assertStringFields($data, array('buy', 'handle'), $relativeFile, $fullPath);

        /** `sell` 若存在應為字串 / `sell` should be string if present */
        if (array_key_exists('sell', $data)) {
            $this->assertInternalType('string', $data['sell'],
                "Field 'sell' should be string. File: $relativeFile, type: " . gettype($data['sell']));
        }
    }

    /**
     * 提供所有物品編號（用於完整檢查 HOF_Model_Data::getItemData()）
     * Provides all item numbers (for full validation of HOF_Model_Data::getItemData())
     *
     * @return array
     */
    public function allItemNoProvider()
    {
        $basePath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'HOF' . DIRECTORY_SEPARATOR . 'Resource' . DIRECTORY_SEPARATOR . 'Item';
        $files = glob($basePath . DIRECTORY_SEPARATOR . '*.yml');

        $result = array();
        foreach ($files as $file) {
            $basename = basename($file, '.yml');
            if (preg_match('/^item\.(\d+)$/', $basename, $m)) {
                $result[] = array((int) $m[1]);
            }
        }

        /** 依編號排序 / Sort by number */
        usort($result, function ($a, $b) { return $a[0] - $b[0]; });

        return $result;
    }

    /**
     * 測試所有物品經過 HOF_Model_Data::getItemData() 後都包含 `no` 鍵
     * Test that ALL items through HOF_Model_Data::getItemData() have `no` key
     *
     * @dataProvider allItemNoProvider
     */
    public function testGetItemDataHasNoKey($itemNo)
    {
        $data = HOF_Model_Data::getItemData($itemNo, true);
        $this->assertNotEmpty($data, "getItemData($itemNo) returned empty data");
        $this->assertArrayHasKey('no', $data,
            "getItemData($itemNo) returned data without 'no' key");
        $this->assertArrayNotHasKey(0, $data,
            "getItemData($itemNo) returned data with integer key 0");
        $this->assertArrayHasKey('id', $data,
            "getItemData($itemNo) returned data without 'id' key");
        $this->assertEquals($itemNo, $data['id']);
    }

    /**
     * 測試 HOF_Class_Item 建構後 exists() 回傳 true
     * Test HOF_Class_Item::exists() returns true after construction
     */
    public function testHOFClassItemExists()
    {
        $testItems = array(1002, 1100, 3000, 5500, 5000);

        foreach ($testItems as $itemNo) {
            $item = new HOF_Class_Item($itemNo);
            $this->assertTrue($item->exists(),
                "HOF_Class_Item($itemNo)->exists() should be true");
        }
    }

    /**
     * 測試 HOF_Class_Item::no() 輸出正確格式
     * Test HOF_Class_Item::no() outputs correct format
     */
    public function testHOFClassItemNo()
    {
        $item = new HOF_Class_Item(1100);
        $this->assertTrue($item->exists(), "Item(1100)->exists() should be true");

        ob_start();
        $noOutput = $item->no();
        $printedOutput = ob_get_clean();

        $this->assertGreaterThan(0, $noOutput,
            "Item(1100)->no() printf return value (length) should be > 0");
        $this->assertRegExp('/^\[\d{4}\]$/', $printedOutput,
            "Item(1100)->no() should output formatted number like '[1100]'");
        $this->assertEquals('[1100]', $printedOutput,
            "Item(1100)->no() should output '[1100]'");
    }

    // ================================================================
    // Char 資料類型驗證 / Char Data Type Validation
    // ================================================================

    /**
     * @dataProvider charYamlFileProvider
     */
    public function testCharTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Char') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** 字串欄位 / String fields */
        $this->assertStringFields($data, array('name', 'job'), $relativeFile, $fullPath);

        /** 數值欄位 / Numeric fields */
        $this->assertNumericFields($data, array('no', 'level', 'exp', 'maxhp', 'hp', 'maxsp', 'sp',
            'str', 'int', 'dex', 'spd', 'luk'), $relativeFile, $fullPath);

        /** `skill` 應為陣列 / `skill` should be array */
        if (array_key_exists('skill', $data)) {
            $this->assertInternalType('array', $data['skill'],
                "Field 'skill' should be array. File: $relativeFile, Path: $fullPath");
        }

        /** `equip` 應為關聯陣列 / `equip` should be associative array */
        if (array_key_exists('equip', $data)) {
            $this->assertInternalType('array', $data['equip'],
                "Field 'equip' should be array. File: $relativeFile, Path: $fullPath");
        }

        /** `behavior` 應為陣列 / `behavior` should be array */
        if (array_key_exists('behavior', $data)) {
            $this->assertInternalType('array', $data['behavior'],
                "Field 'behavior' should be array. File: $relativeFile, Path: $fullPath");
        }
    }

    // ================================================================
    // Guard 資料類型驗證 / Guard Data Type Validation
    // ================================================================

    /**
     * @dataProvider guardYamlFileProvider
     */
    public function testGuardTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Guard') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** `info.desc` 應存在且為字串 / `info.desc` should exist and be string */
        $this->assertArrayHasKey('info', $data,
            "Field 'info' should exist. File: $relativeFile");
        $this->assertInternalType('array', $data['info'],
            "Field 'info' should be array. File: $relativeFile");
        $this->assertArrayHasKey('desc', $data['info'],
            "Field 'info.desc' should exist. File: $relativeFile");
        $this->assertInternalType('string', $data['info']['desc'],
            "Field 'info.desc' should be string. File: $relativeFile");
    }

    // ================================================================
    // Job 資料類型驗證 / Job Data Type Validation
    // ================================================================

    /**
     * @dataProvider jobYamlFileProvider
     */
    public function testJobTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Job') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** 字串欄位 / String fields */
        $this->assertStringFields($data, array('job', 'img', 'job_name'), $relativeFile, $fullPath);

        /** `equip` 應為陣列 / `equip` should be array */
        if (array_key_exists('equip', $data)) {
            $this->assertInternalType('array', $data['equip'],
                "Field 'equip' should be array. File: $relativeFile");
        }

        /** `info.desc` 應存在 / `info.desc` should exist */
        if (array_key_exists('info', $data)) {
            $this->assertInternalType('array', $data['info'],
                "Field 'info' should be array. File: $relativeFile");
        }
    }

    // ================================================================
    // Judge 資料類型驗證 / Judge Data Type Validation
    // ================================================================

    /**
     * @dataProvider judgeYamlFileProvider
     */
    public function testJudgeTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Judge') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile" . ", Path: $fullPath");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** `no` 應為整數或數值字串 / `no` should be integer or numeric string */
        $this->assertTrue(is_int($data['no']) || is_numeric($data['no']),
            "Value of key 'no' should be numeric. File: $relativeFile, type: " . gettype($data['no']) . ", Path: $fullPath");

        /** `exp` 應為字串 / `exp` should be string */
        if (array_key_exists('exp', $data)) {
            $this->assertInternalType('string', $data['exp'],
                "Field 'exp' should be string. File: $relativeFile, type: " . gettype($data['exp']) . ", Path: $fullPath");
        }

        /** `quantity` 可為布林或數值 / `quantity` can be boolean or numeric */
        if (array_key_exists('quantity', $data)) {
            $this->assertTrue(is_bool($data['quantity']) || is_int($data['quantity']) || is_numeric($data['quantity']),
                "Field 'quantity' should be boolean or numeric. File: $relativeFile, type: " . gettype($data['quantity']) . ", Path: $fullPath");
        }
    }

    // ================================================================
    // Land 資料類型驗證 / Land Data Type Validation
    // ================================================================

    /**
     * @dataProvider landYamlFileProvider
     */
    public function testLandTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Land') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** `land` 應為陣列，包含 name, name0, land, proper 等欄位 / `land` should be array */
        if (array_key_exists('land', $data)) {
            $this->assertInternalType('array', $data['land'],
                "Field 'land' should be array. File: $relativeFile");
            $this->assertStringFields($data['land'], array('name', 'land', 'proper'), $relativeFile, $fullPath);

            $this->assertStringFieldsAllowEmpty($data['land'], array('name0'), $relativeFile, $fullPath);
        }

        /** `monster` 應為關聯陣列 / `monster` should be associative array */
        if (array_key_exists('monster', $data)) {
            $this->assertInternalType('array', $data['monster'],
                "Field 'monster' should be array. File: $relativeFile" . ", Path: $fullPath");
        }
    }

    // ================================================================
    // Mon 資料類型驗證 / Mon Data Type Validation
    // ================================================================

    /**
     * @dataProvider monYamlFileProvider
     */
    public function testMonTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Mon') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** 字串欄位 / String fields */
        $this->assertStringFields($data, array('name', 'img'), $relativeFile, $fullPath);

        /** 數值欄位 / Numeric fields */
        $this->assertNumericFields($data, array('no', 'level', 'maxhp', 'hp', 'maxsp', 'sp',
            'str', 'int', 'dex', 'spd', 'luk'), $relativeFile, $fullPath);

        /** 數值陣列欄位 / Numeric array fields */
        $this->assertIntegerArrayFields($data, array('atk', 'def'), $relativeFile);

        /** `special` 應為陣列 / `special` should be array */
        if (array_key_exists('special', $data)) {
            $this->assertInternalType('array', $data['special'],
                "Field 'special' should be array. File: $relativeFile" . ", Path: $fullPath");
        }

        /** `info.desc` 應存在 / `info.desc` should exist */
        if (array_key_exists('info', $data)) {
            $this->assertInternalType('array', $data['info'],
                "Field 'info' should be array. File: $relativeFile" . ", Path: $fullPath");
        }
    }

    // ================================================================
    // Skill 資料類型驗證 / Skill Data Type Validation
    // ================================================================

    /**
     * @dataProvider skillYamlFileProvider
     */
    public function testSkillTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Skill') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** 字串欄位 / String fields */
        $this->assertStringFields($data, array('name', 'img', 'exp'), $relativeFile, $fullPath);
        // $this->assertStringFieldsAllowEmpty($data, array('exp'), $relativeFile, $fullPath);

        /** 數值欄位 / Numeric fields */
        $this->assertNumericFields($data, array('no', 'sp', 'type', 'learn', 'pow'), $relativeFile, $fullPath);

        /** `target` 應為陣列 / `target` should be array */
        if (array_key_exists('target', $data)) {
            $this->assertInternalType('array', $data['target'],
                "Field 'target' should be array. File: $relativeFile");
        }
    }

    // ================================================================
    // Skilltree 資料類型驗證 / Skilltree Data Type Validation
    // ================================================================

    /**
     * @dataProvider skilltreeYamlFileProvider
     */
    public function testSkilltreeTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Skilltree') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** `no` 應為整數或數值字串 / `no` should be integer or numeric string */
        $this->assertTrue(is_int($data['no']) || is_numeric($data['no']),
            "Value of key 'no' should be numeric. File: $relativeFile, type: " . gettype($data['no']));

        /** `check` 應為陣列 / `check` should be array */
        if (array_key_exists('check', $data)) {
            $this->assertInternalType('array', $data['check'],
                "Field 'check' should be array. File: $relativeFile");
        }
    }

    // ================================================================
    // Union 資料類型驗證 / Union Data Type Validation
    // ================================================================

    /**
     * @dataProvider unionYamlFileProvider
     */
    public function testUnionTypeCorrectness($relativeFile)
    {
        $fullPath = $this->getResourcePath('Union') . DIRECTORY_SEPARATOR . $relativeFile;
        $data = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($data, "Failed to load data from: $relativeFile");
        $this->assertNoKeyCorrect($data, $relativeFile);

        /** 字串欄位 / String fields */
        $this->assertStringFields($data, array('name'), $relativeFile, $fullPath);

        /** `data` 應為陣列 / `data` should be array */
        if (array_key_exists('data', $data)) {
            $this->assertInternalType('array', $data['data'],
                "Field 'data' should be array. File: $relativeFile. Path: $fullPath");
        }
    }

    // ================================================================
    // DataProviders / Data Providers
    // ================================================================

    /**
     * 提供 Resource 目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the resource directory
     *
     * @return array
     */
    public function yamlFileProvider()
    {
        return _resource_glob_yaml('.');
    }

    /**
     * @return array
     */
    public function guardYamlFileProvider()
    {
        return _resource_glob_yaml('Guard');
    }

    /**
     * @return array
     */
    public function jobYamlFileProvider()
    {
        return _resource_glob_yaml('Job');
    }

    /**
     * @return array
     */
    public function judgeYamlFileProvider()
    {
        return _resource_glob_yaml('Judge');
    }

    /**
     * @return array
     */
    public function landYamlFileProvider()
    {
        return _resource_glob_yaml('Land');
    }

    /**
     * @return array
     */
    public function monYamlFileProvider()
    {
        return _resource_glob_yaml('Mon');
    }

    /**
     * @return array
     */
    public function unionYamlFileProvider()
    {
        return _resource_glob_yaml('Union');
    }

    /**
     * 提供 Item 目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the Item resource directory
     *
     * @return array
     */
    public function itemYamlFileProvider()
    {
        return $this->globYamlFiles('Item', true);
    }

    /**
     * @return array
     */
    public function charYamlFileProvider()
    {
        return $this->globYamlFiles('Char');
    }

    /**
     * @return array
     */
    public function skillYamlFileProvider()
    {
        return $this->globYamlFiles('Skill');
    }

    /**
     * @return array
     */
    public function skilltreeYamlFileProvider()
    {
        return $this->globYamlFiles('Skilltree');
    }

    /**
     * 通用 YAML 檔案搜尋（靜態，用於 DataProvider）
     * Generic YAML file finder (static, for DataProvider)
     *
     * @param string $subDir Resource 下的子目錄名稱（. 表示 Resource 根目錄）/ Subdirectory name under Resource
     * @param bool $excludeCache 是否排除 cache 目錄 / Whether to exclude cache directory
     * @return array
     */
    private static function globYamlFiles($subDir, $excludeCache = false)
    {
        $basePath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'HOF' . DIRECTORY_SEPARATOR . 'Resource';
        $path = $basePath;
        if ($subDir !== '.') {
            $path .= DIRECTORY_SEPARATOR . $subDir;
        }

        $files = array();
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'yml') {
                    $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    if ($excludeCache && strpos($relativePath, 'cache' . DIRECTORY_SEPARATOR) === 0) {
                        continue;
                    }
                    $files[] = array($relativePath);
                }
            }
        }

        return $files;
    }
}
