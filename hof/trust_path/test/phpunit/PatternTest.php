<?php

/**
 * Pattern 載入/儲存 測試
 * Pattern load/save tests
 *
 * 測試目標：確認 pattern 從 YAML 載入後透過 char 物件讀取是否正確
 *
 * @author Shadow Monarch
 * @copyright 2026
 */

class PatternTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HOF_Class_Char_Type_Char
     */
    protected $char;

    protected function setUp()
    {
        parent::setUp();

        // 載入 demo 使用者的 Hero1 角色
        $this->char = HOF_Class_Char::factory(
            HOF_Class_Char::TYPE_CHAR,
            'b3e304903f09e14b8386a49a1e1e01e3',
            null,
            'demo'
        );

        $this->assertInstanceOf('HOF_Class_Char_Type_Char', $this->char);
    }

    /**
     * @test
     * 測試原始 YAML behavior 資料是否正確載入
     */
    public function testBehaviorDataFromYaml()
    {
        $behavior = $this->char->behavior;

        $this->assertNotEmpty($behavior, 'behavior should not be empty');
        $this->assertArrayHasKey('position', $behavior, 'behavior should have position');
        $this->assertArrayHasKey('guard', $behavior, 'behavior should have guard');
        $this->assertArrayHasKey('pattern', $behavior, 'behavior should have pattern');

        echo "\n--- behavior from char->behavior ---\n";
        var_dump($behavior);
    }

    /**
     * @test
     * 測試 pattern_item() 透過 __call → Pattern 物件讀取是否正確
     */
    public function testPatternItemViaPatternObject()
    {
        $pattern0 = $this->char->pattern_item(0);
        $pattern1 = $this->char->pattern_item(1);

        echo "\n--- pattern_item(0) ---\n";
        var_dump($pattern0);
        echo "\n--- pattern_item(1) ---\n";
        var_dump($pattern1);

        // 比對 behavior 原始資料
        $rawPattern0 = $this->char->behavior['pattern'][0];
        
        echo "\n--- raw behavior['pattern'][0] ---\n";
        var_dump($rawPattern0);

        $this->assertNotEmpty($pattern0, 'pattern_item(0) should not be empty');
        $this->assertArrayHasKey('judge', $pattern0);
        $this->assertArrayHasKey('quantity', $pattern0);
        $this->assertArrayHasKey('action', $pattern0);

        // 檢查 pattern_item 回傳值與原始 behavior 值一致
        $this->assertEquals(
            $rawPattern0['judge'],
            $pattern0['judge'],
            'pattern_item(0).judge should match raw behavior[0].judge'
        );
        $this->assertEquals(
            $rawPattern0['quantity'],
            $pattern0['quantity'],
            'pattern_item(0).quantity should match raw behavior[0].quantity'
        );
        $this->assertEquals(
            $rawPattern0['action'],
            $pattern0['action'],
            'pattern_item(0).action should match raw behavior[0].action'
        );
    }

    /**
     * @test
     * 測試 pattern_max() 回傳值是否正確
     */
    public function testPatternMax()
    {
        $max = $this->char->pattern_max();

        echo "\n--- pattern_max() = $max ---\n";

        // Hero1: INT=2, Lv=2 → 預期 2
        $this->assertGreaterThanOrEqual(1, $max, 'pattern_max should be at least 1');
    }

    /**
     * @test
     * 測試 CHECK_PATTERN 是否改變原始資料
     */
    public function testCheckPatternDoesNotChangeData()
    {
        // 先記錄原始 behavior pattern
        $originalPattern = $this->char->behavior['pattern'];

        echo "\n--- Original pattern before CHECK_PATTERN ---\n";
        var_dump($originalPattern);

        // 執行 CHECK_PATTERN（在建構流程中已執行一次，這是再次執行）
        $this->char->pattern(HOF_Class_Char_Pattern::CHECK_PATTERN);

        // 再讀取一次
        $afterPattern = $this->char->behavior['pattern'];

        echo "\n--- Pattern after CHECK_PATTERN ---\n";
        var_dump($afterPattern);

        // 比較 JSON 來檢查深層結構是否一致
        $this->assertEquals(
            json_encode($originalPattern),
            json_encode($afterPattern),
            'CHECK_PATTERN should not change pattern data'
        );
    }

    /**
     * @test
     * 測試 pattern_item() 的 & 參考鏈是否完整
     * 
     * 修改 char 物件的 behavior 後，pattern_item 應反映變更
     */
    public function testPatternReferenceChain()
    {
        $originalJudge = $this->char->pattern_item(0)['judge'];

        // 直接修改 char 的 behavior
        $this->char->behavior['pattern'][0]['judge'] = 'TEST9999';

        $modifiedJudge = $this->char->pattern_item(0)['judge'];

        echo "\n--- Reference chain test ---\n";
        echo "Original judge: " . var_export($originalJudge, true) . "\n";
        echo "Modified judge: " . var_export($modifiedJudge, true) . "\n";

        // 如果參考鏈完整，pattern_item 應讀到修改後的值
        $this->assertEquals(
            'TEST9999',
            $modifiedJudge,
            'Pattern reference chain broken: pattern_item() does not reflect direct char modification'
        );

        // 還原
        $this->char->behavior['pattern'][0]['judge'] = $originalJudge;
    }
}
