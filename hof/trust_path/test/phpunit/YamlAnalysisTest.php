<?php

/** 載入測試共用工具 / Load test shared helpers */
require_once PROJECT_TEST_PATH . '/lib/test_helper.php';

/**
 * YAML 複雜資料結構分析與字串化測試
 * YAML Complex Data Structure Analysis and Stringification Test
 */
class YamlAnalysisTest extends PHPUnit_Framework_TestCase
{
    private $tempFile;

    protected function setUp()
    {
        parent::setUp();
        _set_production_error_reporting();
        $this->tempFile = _temp_yaml_path('yaml_analysis_test');
    }

    protected function tearDown()
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * @test
     * 測試深層巢狀混合結構 / Test deep nested mixed structure
     */
    public function testDeepNestedMixed()
    {
        $data = array(
            'level1' => array(
                'level2' => array(
                    'level3' => array(
                        'list' => array(
                            array('id' => 1, 'tags' => array('a', 'b')),
                            array('id' => 2, 'tags' => array('c', 'd'))
                        ),
                        'metadata' => array(
                            'created' => 123456789,
                            'author' => 'System'
                        )
                    )
                )
            )
        );

        HOF_Class_Yaml::save($this->tempFile, $data);
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($data, $reloaded);
    }

    /**
     * @test
     * 測試長字串與換行處理 / Test long string and newline handling
     */
    public function testLongStringsAndNewlines()
    {
        $data = array(
            'short' => 'Hello',
            'long' => "This is a very long string that should probably stay on one line if possible, but YAML might wrap it depending on settings.",
            'with_newlines' => "Line 1\nLine 2\nLine 3",
            'with_special' => "Quotes: ' \" , Brackets: [ ] { }"
        );

        HOF_Class_Yaml::save($this->tempFile, $data);
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($data, $reloaded);
    }

    /**
     * @test
     * 測試各種類型邊界情況 / Test various type edge cases
     */
    public function testTypeEdgeCases()
    {
        $data = array(
            'null_val' => null,
            'empty_str' => '',
            'zero' => 0,
            'false_val' => false,
            'true_val' => true,
            'float_val' => 3.14,
            'numeric_str' => '123'
        );

        HOF_Class_Yaml::save($this->tempFile, $data);
        $reloaded = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($data, $reloaded);
        $this->assertSame(null, $reloaded['null_val']);
        $this->assertSame('', $reloaded['empty_str']);
        $this->assertSame(0, $reloaded['zero']);
        $this->assertSame(false, $reloaded['false_val']);
        $this->assertSame(true, $reloaded['true_val']);
        /** PHP 5.6 might return numeric string as string or int depending on parser settings, but HOF_Class_Yaml should be consistent */
        $this->assertEquals(3.14, $reloaded['float_val']);
    }

    /**
     * @test
     * 測試快照穩定性（Roundtrip）/ Test snapshot stability (Roundtrip)
     *
     * 確保多次儲存載入後資料結構不會變形
     * Ensure data structure doesn't morph after multiple save/load cycles
     */
    public function testRoundtripStability()
    {
        $data = array(
            'user' => array(
                'name' => 'Demo',
                'stats' => array('HP' => 100, 'MP' => 50)
            ),
            'items' => array('potion', 'ether')
        );

        /** 第一輪 / Round 1 */
        HOF_Class_Yaml::save($this->tempFile, $data);
        $data1 = HOF_Class_Yaml::load($this->tempFile);
        $this->assertEquals($data, $data1);

        /** 第二輪 / Round 2 */
        HOF_Class_Yaml::save($this->tempFile, $data1);
        $data2 = HOF_Class_Yaml::load($this->tempFile);
        $this->assertEquals($data, $data2);

        /** 檔案內容應該保持一致 / File content should be identical */
        $content1 = file_get_contents($this->tempFile);
        HOF_Class_Yaml::save($this->tempFile, $data2);
        $content2 = file_get_contents($this->tempFile);
        $this->assertEquals($content1, $content2);
    }
}
