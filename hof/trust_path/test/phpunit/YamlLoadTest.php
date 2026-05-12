<?php

/** 載入測試共用工具 / Load test shared helpers */
require_once PROJECT_TEST_PATH . '/lib/test_helper.php';

/** 載入測試用 Fixture 角色類別 / Load test fixture character class */
require_once PROJECT_TEST_PATH . '/lib/HOF_Class_Char_Type_Char_Fixture.php';

/**
 * YAML 載入測試 — 確認 Symfony YAML Parser 能否正確解析 pattern
 *
 * @author Shadow Monarch
 * @copyright 2026
 */
class YamlLoadTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        /** 比照 production error_reporting + 模擬 CLI 環境 IP / Production error_reporting + simulate CLI IP */
        _init_integration_test_env();

        /**
         * 確保 HOF 檔案系統處於全新狀態（清除前導測試的殘留檔案句柄與鎖定）
         * Ensure HOF file system is in a clean state (clear residual handles and locks from preceding tests)
         * Windows PHP 5.6 上 flock(LOCK_EX) 後的 fclose() 可能無法完整釋放鎖
         * 導致 testDirectYamlLoad 中 file_get_contents() 回傳空字串
         */
        HOF_Class_File::fpclose_all();
        clearstatcache();
    }

    protected function tearDown()
    {
        /** 清除 HOF::$_testIp，避免跨測試污染 / Clear HOF::$_testIp to prevent cross-test contamination */
        _clear_test_ip();

        parent::tearDown();
    }

    /**
     * @test
     * 直接從 YAML 檔案載入資料，檢查 pattern 是否正確
     */
    public function testDirectYamlLoad()
    {
         $helper = new HOF_Helper_Char();
         $file = $helper->char_file(
             'b3e304903f09e14b8386a49a1e1e01e3',
             'demo'
         );

        $this->assertFileExists($file);

        // 直接用 YAML::load
        $data = HOF_Class_Yaml::load($file);

        $this->assertNotEmpty($data, 'YAML data should not be empty');
        $this->assertArrayHasKey('behavior', $data, 'YAML should have behavior key');

        $behavior = $data['behavior'];
         // echo "\n--- behavior from YAML ---\n";
         // var_dump($behavior);

        $this->assertArrayHasKey('pattern', $behavior, 'behavior should have pattern');

        // echo "\n--- YAML pattern count: " . count($behavior['pattern']) . " ---\n";
        // foreach ($behavior['pattern'] as $i => $p) {
        //     echo "  Item $i: judge={$p['judge']}, quantity={$p['quantity']}, action={$p['action']}\n";
        // }
    }

    /**
     * @test
     * 測試 setCharData 之前的 source 資料 — 確認從 YAML 到 source 的轉換是否正確
     */
    public function testSourceDataBeforeSetCharData()
    {
        // 直接建立一個新 char，但在 init 前檢查 source
        $char = HOF_Class_Char::factory(
            HOF_Class_Char::TYPE_CHAR,
            'b3e304903f09e14b8386a49a1e1e01e3',
            null,
            'demo'
        );

        // source() 回傳 HOF_Class_Array wrapping the raw YAML data
        $source = $char->source();

        // echo "\n--- Source data (HOF_Class_Array) ---\n";
        // echo "  source type: " . get_class($source) . "\n";

        // Access source data
        // echo "\n--- Source->behavior ---\n";
        $behavior = $source->behavior;
        // var_dump($behavior);

        $this->assertNotEmpty($behavior, 'Source behavior should not be empty');

        // if (isset($behavior['pattern'])) {
        //     echo "\n--- Pattern count from source: " . count($behavior['pattern']) . " ---\n";
        //     foreach ($behavior['pattern'] as $i => $p) {
        //         echo "  Item $i: judge={$p['judge']}, quantity={$p['quantity']}, action={$p['action']}\n";
        //     }
        // } else {
        //     echo "\n--- WARNING: No 'pattern' key in source behavior! ---\n";
        //     echo "Available keys: " . implode(', ', array_keys($behavior)) . "\n";
        // }
    }

    /**
     * @test
     * 驗證 char 載入流程：YAML → source → setCharData → behavior
     *
     * 使用測試專用 Fixture 資料，避免依賴遊戲實際使用者資料。
     * Uses test-specific fixture data to avoid depending on live game user data.
     *
     * @see HOF_Class_Char_Type_Char_Fixture
     */
    public function testFullLoadChain()
    {
        /**
         * 由 setUp() 內的 _init_integration_test_env() 處理 error_reporting 與 $_SERVER IP 設定
         * error_reporting and $_SERVER IP settings are handled by _init_integration_test_env() in setUp()
         *
         * 使用 Fixture 角色類別（從 test/fixtures/char_test_data.yml 載入）
         * Use fixture character class (loads from test/fixtures/char_test_data.yml)
         *
         * 直接 new 而非透過 HOF_Class_Char::factory()，因為 factory 會動態組合類別名稱
         * Direct instantiation instead of HOF_Class_Char::factory() since factory
         * dynamically constructs class names
         */
        $char = new HOF_Class_Char_Type_Char_Fixture(
            'test-char-fixture-001',
            array(),
            'test-user'
        );

        $behavior = $char->behavior;

        /** === 驗證 behavior 結構 / Verify behavior structure === */
        $this->assertInternalType('array', $behavior, 'behavior should be an array');
        $this->assertArrayHasKey('pattern', $behavior, 'behavior should have pattern key');
        $this->assertArrayHasKey('position', $behavior, 'behavior should have position key');
        $this->assertArrayHasKey('guard', $behavior, 'behavior should have guard key');
        $this->assertEquals('front', $behavior['position']);
        $this->assertEquals('always', $behavior['guard']);

        /** === 驗證 pattern 數量與結構 / Verify pattern count and structure === */
        $this->assertCount(2, $behavior['pattern'], 'pattern should have exactly 2 items');

        /** 驗證 pattern[0] / Verify pattern[0] */
        $pattern0 = $behavior['pattern'][0];
        $this->assertInternalType('array', $pattern0, 'pattern[0] should be an array');
        $this->assertArrayHasKey('judge', $pattern0);
        $this->assertArrayHasKey('quantity', $pattern0);
        $this->assertArrayHasKey('action', $pattern0);
        $this->assertEquals('1000', $pattern0['judge']);
        $this->assertEquals(0, $pattern0['quantity']);
        $this->assertEquals('1001', $pattern0['action']);

        /** 驗證 pattern[1] / Verify pattern[1] */
        $pattern1 = $behavior['pattern'][1];
        $this->assertInternalType('array', $pattern1, 'pattern[1] should be an array');
        $this->assertEquals('2000', $pattern1['judge']);
        $this->assertEquals(5, $pattern1['quantity']);
        $this->assertEquals('2002', $pattern1['action']);

        /** === 驗證 pattern_max() / Verify pattern_max() === */
        $max = $char->pattern_max();
        $this->assertInternalType('int', $max, 'pattern_max() should return int');
        /** int=10, PATTERN_MIN_CHAR=2 → 2 + 1 (int>=10) = 3 */
        $this->assertEquals(3, $max, 'pattern_max() should be 3 for int=10');

        /** === 驗證 pattern_item() / Verify pattern_item() === */
        $item0 = $char->pattern_item(0);
        $this->assertInternalType('array', $item0, 'pattern_item(0) should be array');
        $this->assertArrayHasKey('judge', $item0);
        $this->assertArrayHasKey('quantity', $item0);
        $this->assertArrayHasKey('action', $item0);
        $this->assertEquals('1000', $item0['judge']);
        $this->assertEquals(0, $item0['quantity']);
        $this->assertEquals('1001', $item0['action']);

        $item1 = $char->pattern_item(1);
        $this->assertInternalType('array', $item1, 'pattern_item(1) should be array');
        $this->assertEquals('2000', $item1['judge']);
        $this->assertEquals(5, $item1['quantity']);
        $this->assertEquals('2002', $item1['action']);
    }
}
