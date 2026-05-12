<?php

/** 載入測試共用工具 / Load test shared helpers */
require_once PROJECT_TEST_PATH . '/lib/test_helper.php';

/**
 * YAML Key 規範化與特殊字元測試
 * YAML Key Normalization and Special Characters Test
 */
class YamlKeyNormalizationTest extends PHPUnit_Framework_TestCase
{
    private $tempFile;

    protected function setUp()
    {
        parent::setUp();
        _set_production_error_reporting();
        $this->tempFile = _temp_yaml_path('yaml_key_norm_test');
    }

    protected function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * @test
     * 測試路徑 Key 的規範化儲存與載入 / Test path key normalization save and load
     */
    public function testPathKeyNormalization()
    {
        /** 模擬 Icon.php 的路徑處理 / Simulate Icon.php path handling */
        $rawPaths = array(
            './static/image/char/' => 'char_data',
            'static/image/manual' => 'manual_data',
            './dat/log/' => 'log_data'
        );

        $normalizedData = array();
        foreach ($rawPaths as $path => $val) {
            $key = trim($path, './');
            $normalizedData[$key] = $val;
        }

        HOF_Class_Yaml::save($this->tempFile, $normalizedData);
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        foreach (array_keys($normalizedData) as $expectedKey) {
            $this->assertArrayHasKey($expectedKey, $reloaded, "Key '$expectedKey' should exist in reloaded data");
            /** 不應包含 ./ 或結尾斜槓 / Should not contain ./ or trailing slash */
            $this->assertNotContains('./', $expectedKey);
            $this->assertNotRegExp('/\/$/', $expectedKey);
        }
    }

    /**
     * @test
     * 測試引號 Key 的正確解析 / Test correct parsing of quoted keys
     */
    public function testQuotedKeys()
    {
        $yaml = <<<YAML
'quoted key': value1
"double quoted": value2
normal_key: value3
'key with: colon': value4
YAML;
        file_put_contents($this->tempFile, $yaml);
        $data = HOF_Class_Yaml::load($this->tempFile);

        $this->assertArrayHasKey('quoted key', $data);
        $this->assertEquals('value1', $data['quoted key']);
        $this->assertArrayHasKey('double quoted', $data);
        $this->assertEquals('value2', $data['double quoted']);
        $this->assertArrayHasKey('key with: colon', $data);
        $this->assertEquals('value4', $data['key with: colon']);
    }

    /**
     * @test
     * 測試行內映射中的引號 Key / Test quoted keys in inline mappings
     */
    public function testInlineQuotedKeys()
    {
        $yaml = "inline: {'key 1': val1, \"key 2\": val2}";
        file_put_contents($this->tempFile, $yaml);
        $data = HOF_Class_Yaml::load($this->tempFile);

        $this->assertInternalType('array', $data['inline']);
        $this->assertArrayHasKey('key 1', $data['inline']);
        $this->assertEquals('val1', $data['inline']['key 1']);
        $this->assertArrayHasKey('key 2', $data['inline']);
        $this->assertEquals('val2', $data['inline']['key 2']);
    }

    /**
     * @test
     * 測試 Key 中的特殊字元 / Test special characters in keys
     */
    public function testSpecialCharsInKeys()
    {
        $data = array(
            'key.with.dots' => 1,
            'key-with-dash' => 2,
            'key_with_underscore' => 3,
            'key/with/slash' => 4,
            'key with spaces' => 5,
            '中文鍵名' => 6,
            'key!@#$%^&*()' => 7
        );

        HOF_Class_Yaml::save($this->tempFile, $data);
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($data, $reloaded);
    }
}
