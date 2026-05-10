<?php

/**
 * YAML 載入測試 — 確認 Symfony YAML Parser 能否正確解析 pattern
 *
 * @author Shadow Monarch
 * @copyright 2026
 */
class YamlLoadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * 直接從 YAML 檔案載入資料，檢查 pattern 是否正確
     */
    public function testDirectYamlLoad()
    {
        $file = HOF_Helper_Char::char_file(
            'b3e304903f09e14b8386a49a1e1e01e3',
            'demo'
        );

        echo "\n--- YAML file path: $file ---\n";
        $this->assertFileExists($file);

        // 直接用 YAML::load
        $data = HOF_Class_Yaml::load($file);

        echo "\n--- Raw YAML data ---\n";
        var_dump($data);

        $this->assertNotEmpty($data, 'YAML data should not be empty');
        $this->assertArrayHasKey('behavior', $data, 'YAML should have behavior key');

        $behavior = $data['behavior'];
        echo "\n--- behavior from YAML ---\n";
        var_dump($behavior);

        $this->assertArrayHasKey('pattern', $behavior, 'behavior should have pattern');

        echo "\n--- YAML pattern count: " . count($behavior['pattern']) . " ---\n";
        foreach ($behavior['pattern'] as $i => $p) {
            echo "  Item $i: judge={$p['judge']}, quantity={$p['quantity']}, action={$p['action']}\n";
        }
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

        echo "\n--- Source data (HOF_Class_Array) ---\n";
        echo "  source type: " . get_class($source) . "\n";

        // Access source data
        echo "\n--- Source->behavior ---\n";
        $behavior = $source->behavior;
        var_dump($behavior);

        $this->assertNotEmpty($behavior, 'Source behavior should not be empty');

        if (isset($behavior['pattern'])) {
            echo "\n--- Pattern count from source: " . count($behavior['pattern']) . " ---\n";
            foreach ($behavior['pattern'] as $i => $p) {
                echo "  Item $i: judge={$p['judge']}, quantity={$p['quantity']}, action={$p['action']}\n";
            }
        } else {
            echo "\n--- WARNING: No 'pattern' key in source behavior! ---\n";
            echo "Available keys: " . implode(', ', array_keys($behavior)) . "\n";
        }
    }

    /**
     * @test
     * 驗證 char 載入流程：YAML → source → setCharData → behavior
     */
    public function testFullLoadChain()
    {
        $char = HOF_Class_Char::factory(
            HOF_Class_Char::TYPE_CHAR,
            'b3e304903f09e14b8386a49a1e1e01e3',
            null,
            'demo'
        );

        $behavior = $char->behavior;

        echo "\n--- Final char->behavior after construction ---\n";
        var_dump($behavior);

        echo "\n--- Pattern count from char->behavior: " . count($behavior['pattern']) . " ---\n";

        // 檢查 pattern_max
        $max = $char->pattern_max();
        echo "\n--- pattern_max() = $max ---\n";

        echo "\n--- pattern_item(0) ---\n";
        var_dump($char->pattern_item(0));

        echo "\n--- pattern_item(1) ---\n";
        var_dump($char->pattern_item(1));
    }
}
