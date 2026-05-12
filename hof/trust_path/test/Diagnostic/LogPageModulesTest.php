<?php
/**
 * Log Page Diagnostic Test
 * 診斷測試：針對 Log 頁面加載的模組進行測試
 */

//@noUnusedParameters:false
/// <reference types="node" />
/// <reference types="jest" />

class Diagnostic_LogPageModulesTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * 模擬使用者登入 (直接設定屬性)
     * Simulate user login (Direct property assignment)
     * 
     * @param string $id 使用者帳號 / User ID
     */
    protected function simulateLogin($id = 'demo')
    {
        $user = HOF::user();
        $user->id = $id;
        $user->islogin = true;
        
        // 載入資料以補完 name 等屬性
        $data = $user->LoadData();
        if ($data) {
            $user->SetData($data);
        }
    }

    /**
     * @test
     * 測試：模組載入是否正常
     */
    public function testModulesCanBeLoaded()
    {
        $this->assertTrue(class_exists('HOF_Controller_Log'), 'HOF_Controller_Log class should exist');
        $this->assertTrue(class_exists('HOF_Model_Data'), 'HOF_Model_Data class should exist');
        $this->assertTrue(class_exists('HOF_Class_File'), 'HOF_Class_File class should exist');
    }

    /**
     * @test
     * 測試：常數定義
     */
    public function testConstantsAreDefined()
    {
        $this->assertTrue(defined('BASE_TRUST_PATH'), 'BASE_TRUST_PATH should be defined');
        $this->assertTrue(defined('DAT_DIR'), 'DAT_DIR should be defined');
    }

    /**
     * @test
     * 測試：模型檔案讀取邏輯
     */
    public function testModelDataFileAccess()
    {
        $path = DAT_DIR . 'log/battle/';
        $files = glob($path . '*.log');

        if (!empty($files)) {
            $testFile = basename($files[0]);
            $result = HOF_Model_Data::getLogBattleFile($testFile, $path);

            if ($result !== false) {
                $this->assertArrayHasKey('time', $result, 'Log data should have time');
                $this->assertArrayHasKey('team', $result, 'Log data should have team');
            }
        } else {
            $this->markTestSkipped('No log files found in ' . $path);
        }
    }

    /**
     * @test
     * 測試：模擬 Controller 的初始化邏輯
     */
    public function testControllerInputLogic()
    {
        $controller = new HOF_Controller_Log('log');
        $this->assertInstanceOf('HOF_Controller_Log', $controller);
        $this->assertArrayHasKey('log', HOF_Controller_Log::$map_logtype);
    }

    /**
     * @test
     * 測試：HOF_Class_Icon 獲取 t001
     */
    public function testIconGetT001()
    {
        $url = HOF_Class_Icon::getImageUrl('t001', "./static/image/manual/");
        $this->assertInternalType('string', $url);
        
        // 檢查快取狀態
        $cache = HOF::cache()->data('icon_cache');
        $this->assertInternalType('array', $cache, 'Icon cache should be an array');
    }

    /**
     * @test
     * 測試：模擬 YAML 解析錯誤的情境 (Illegal string offset)
     */
    public function testYamlParserCorruption()
    {
        $parser = new Symfony_Component_Yaml_Parser();
        $yaml = "scalar_string\nkey: val";
        
        // 捕獲警告並轉為異常
        set_error_handler(function($errno, $errstr) {
            throw new Exception($errstr);
        });
        
        try {
            $parser->parse($yaml);
            $this->fail('Should have triggered an error');
        } catch (Exception $e) {
            $this->assertContains('Illegal string offset', $e->getMessage());
        }
        
        restore_error_handler();
    }

    /**
     * @test
     * 測試：登入狀態下的 Log 頁面
     */
    public function testLogPageAsLoggedInUser()
    {
        $this->simulateLogin('demo');
        
        $this->assertTrue(HOF::user()->islogin, 'User should be logged in');
        $this->assertEquals('demo', HOF::user()->id);
        $this->assertNotEmpty(HOF::user()->name, 'User name should be set');

        $controller = new HOF_Controller_Log('log');
        
        ob_start();
        try {
            $controller->main();
        } catch (Exception $e) {
            ob_end_clean();
            $this->fail('Controller main() crashed: ' . $e->getMessage());
        }
        $output = ob_get_clean();
        
        $this->assertNotEmpty($output, 'Output should not be empty');
    }
}
