<?php

/** 載入測試共用工具 / Load test shared helpers (PROJECT_TEST_PATH 來自 bootstrap-core.php) */
require_once PROJECT_TEST_PATH . '/lib/test_helper.php';

/**
 * YAML 遞迴與物件處理測試 — 驗證 HOF_Class_Array 遞迴轉換與快取穩定性
 * YAML recursive and object handling test - Verify HOF_Class_Array recursive conversion and cache stability
 */
class YamlRecursiveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string 測試用的臨時檔案路徑 / Temporary file path for testing
     */
    private $tempFile;

    /**
     * 每個測試前初始化 / Setup before each test
     */
    protected function setUp()
    {
        parent::setUp();
        _set_production_error_reporting();
        $this->tempFile = _temp_yaml_path('yaml_recursive_test');
    }

    /**
     * 每個測試後清理 / Cleanup after each test
     */
    protected function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * @test
     * 測試 HOF_Class_Array 遞迴轉換為陣列 / Test HOF_Class_Array recursive conversion to array
     *
     * 驗證巢狀物件在 dump 之前是否被正確展平，避免 YAML 轉義 Key
     * Verify if nested objects are flattened before dump to avoid YAML escaped keys
     */
    public function testRecursiveToArray()
    {
        /** 建立多層巢狀物件 / Create multi-level nested objects */
        $level3 = new HOF_Class_Array(array('key3' => 'value3'));
        $level2 = new HOF_Class_Array(array('key2' => $level3, 'simple' => 'data'));
        $level1 = new HOF_Class_Array(array('key1' => $level2));

        /** 執行遞迴轉換 / Perform recursive conversion */
        $data = $level1->toArray();

        /** 驗證結果為純陣列 / Verify result is plain array */
        $this->assertInternalType('array', $data);
        $this->assertInternalType('array', $data['key1']);
        $this->assertInternalType('array', $data['key1']['key2']);
        $this->assertEquals('value3', $data['key1']['key2']['key3']);

        /** 驗證 dump 結構是否乾淨 / Verify dump structure is clean */
        $yaml = HOF_Class_Yaml::dump($data);

        /** 不應包含轉義的 YAML 標籤或物件序列化特徵 / Should not contain escaped YAML tags or object serialization markers */
        $this->assertNotContains('HOF_Class_Array', $yaml);
        $this->assertNotContains('\"', $yaml); // 確保沒有因為物件被當作字串而產生的轉義引號
        $this->assertContains('key3: value3', $yaml);
    }

    /**
     * @test
     * 測試 HOF_Class_Yaml::save 的自動遞迴修復 / Test HOF_Class_Yaml::save auto-recursive fix
     */
    public function testYamlSaveRecursiveFix()
    {
        $level2 = new HOF_Class_Array(array('inner' => 'data'));
        $data = array(
            'top' => array(
                'mid' => $level2
            )
        );

        /** 使用 HOF_Class_Yaml::save 儲存 / Save using HOF_Class_Yaml::save */
        HOF_Class_Yaml::save($this->tempFile, $data);

        /** 重新載入 / Reload */
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        /** 驗證巢狀物件已被修復為陣列 / Verify nested object was fixed to array */
        $this->assertInternalType('array', $reloaded['top']['mid']);
        $this->assertEquals('data', $reloaded['top']['mid']['inner']);
    }

    /**
     * @test
     * 測試規範化 Key 的字串化一致性 / Test stringification consistency of normalized keys
     *
     * 模擬 Icon.php 的行為 / Simulate Icon.php behavior
     */
    public function testNormalizedKeyConsistency()
    {
        $dir = './static/image/char/';
        $dir_key = trim($dir, './'); // static/image/char

        $cache_data = array(
            $dir_key => array(
                'mon_001' => 'static/image/char/mon_001.png'
            )
        );

        HOF_Class_Yaml::save($this->tempFile, $cache_data);

        /** 檢查檔案內容 / Check file content */
        $content = file_get_contents($this->tempFile);

        /** Key 不應包含 ./ 或結尾斜槓 / Key should not contain ./ or trailing slash */
        $this->assertContains('static/image/char:', $content);
        $this->assertNotContains('./static/image/char/:', $content);

        /** 重新載入驗證 / Reload and verify */
        $reloaded = HOF_Class_Yaml::load($this->tempFile);
        $this->assertArrayHasKey($dir_key, $reloaded);
        $this->assertEquals('static/image/char/mon_001.png', $reloaded[$dir_key]['mon_001']);
    }

    /**
     * @test
     * 測試深層巢狀資料的分析與字串化狀態 / Test analysis and stringification of deep nested data
     */
    public function testDeepAnalysis()
    {
        $complexData = array(
            'a' => array(
                'b' => array(
                    'c' => array(
                        'd' => array(
                            'e' => "Deep Value",
                            'list' => array(1, 2, array(3, 4))
                        )
                    )
                )
            ),
            'special_keys' => array(
                'path/with/slash' => 'ok',
                'dots.in.key' => 'ok',
                'space in key' => 'ok'
            )
        );

        HOF_Class_Yaml::save($this->tempFile, $complexData);
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($complexData, $reloaded);

        /** 驗證 YAML 語法分析後的狀態 / Verify YAML syntax after analysis */
        $content = file_get_contents($this->tempFile);

        /** 確保特殊字元 Key 被適當引號包圍（Symfony YAML Parser 行為） / Ensure special keys are quoted if needed */
        $this->assertContains('Deep Value', $content);
    }
}
