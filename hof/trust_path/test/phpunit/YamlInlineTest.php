<?php

//@noUnusedParameters:false
/// <reference types="php" />
/// <reference types="phpunit" />

/**
 * YAML 行內語法測試 — 專注於 Symfony YAML Parser 支援的內聯格式
 * YAML inline syntax test - Focus on inline formats supported by Symfony YAML Parser
 *
 * 此測試專門驗證修復後的 parseSequence() 能正確處理巢狀逗號
 * This test specifically verifies the fixed parseSequence() handles nested commas correctly
 *
 * @author Shadow Monarch
 * @copyright 2026
 */
class YamlInlineTest extends PHPUnit_Framework_TestCase
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
        $this->tempFile = sys_get_temp_dir() . '/yaml_inline_test_' . uniqid() . '.yml';
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
     * 輔助函式：將 YAML 內容寫入檔案並解析 / Helper: write YAML content to file and parse
     *
     * @param string $yaml YAML 內容 / YAML content
     * @return array 解析後的資料 / Parsed data
     */
    private function parseYaml($yaml)
    {
        file_put_contents($this->tempFile, $yaml);
        return HOF_Class_Yaml::load($this->tempFile);
    }

    /**
     * @test
     * 測試原始問題案例（關鍵修復驗證） / Test original bug case (critical fix verification)
     */
    public function testOriginalBugCase()
    {
        /** 這是原始出問題的 YAML 內容 / This is the original problematic YAML */
        $yaml = <<<YAML
id: b3e304903f09e14b8386a49a1e1e01e3
name: Hero1
behavior:
  position: front
  guard: always
  pattern: [{judge: "1000", quantity: 0, action: "1001"}, {judge: "1000", quantity: 0, action: "3120"}]
YAML;

        $data = $this->parseYaml($yaml);

        /** 驗證基本結構 / Verify basic structure */
        $this->assertEquals('b3e304903f09e14b8386a49a1e1e01e3', $data['id']);
        $this->assertEquals('Hero1', $data['name']);

        /** 關鍵驗證：pattern 應該是 2 個物件，不是 6 個字串 / Critical verification: pattern should be 2 objects, not 6 strings */
        $this->assertArrayHasKey('behavior', $data, 'Missing behavior key');
        $this->assertArrayHasKey('pattern', $data['behavior'], 'Missing pattern key');
        $this->assertCount(2, $data['behavior']['pattern'], 'Pattern should have exactly 2 items (not 6 string fragments!)');

        /** 驗證每個 pattern 項目都是完整的物件 / Verify each pattern item is a complete object */
        foreach ($data['behavior']['pattern'] as $index => $pattern) {
            $this->assertInternalType('array', $pattern, "Pattern item $index should be an array/object");
            $this->assertArrayHasKey('judge', $pattern, "Pattern item $index should have 'judge' key");
            $this->assertArrayHasKey('quantity', $pattern, "Pattern item $index should have 'quantity' key");
            $this->assertArrayHasKey('action', $pattern, "Pattern item $index should have 'action' key");
        }

        /** 驗證具體值 / Verify specific values */
        $this->assertEquals('1000', $data['behavior']['pattern'][0]['judge']);
        $this->assertEquals(0, $data['behavior']['pattern'][0]['quantity']);
        $this->assertEquals('1001', $data['behavior']['pattern'][0]['action']);
        $this->assertEquals('3120', $data['behavior']['pattern'][1]['action']);

        // echo "\n=== ORIGINAL BUG CASE: PASSED ===\n";
        // echo "Before fix: 6 string fragments\n";
        // echo "After fix: 2 proper objects with correct values\n";
    }

    /**
     * @test
     * 測試簡單行內陣列 / Test simple inline arrays
     */
    public function testSimpleInlineArrays()
    {
        $yaml = <<<YAML
fruits: [apple, banana, cherry]
numbers: [1, 2, 3, 4, 5]
mixed: [one, 2, three, 4.5]
empty: []
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertCount(3, $data['fruits']);
        $this->assertEquals('apple', $data['fruits'][0]);
        $this->assertEquals('banana', $data['fruits'][1]);
        $this->assertEquals('cherry', $data['fruits'][2]);

        $this->assertCount(5, $data['numbers']);
        $this->assertEquals(1, $data['numbers'][0]);
        $this->assertEquals(5, $data['numbers'][4]);

        $this->assertCount(4, $data['mixed']);
        $this->assertEquals('one', $data['mixed'][0]);
        $this->assertEquals(2, $data['mixed'][1]);

        $this->assertCount(0, $data['empty']);

        // echo "\n=== SIMPLE INLINE ARRAYS: PASSED ===\n";
    }

    /**
     * @test
     * 測試簡單行內物件 / Test simple inline objects
     */
    public function testSimpleInlineObjects()
    {
        $yaml = <<<YAML
user: {name: "Alice", age: 30, active: true}
config: {host: "localhost", port: 3306}
nested: {a: 1, b: 2, c: {d: 3, e: 4}}
empty: {}
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertEquals('Alice', $data['user']['name']);
        $this->assertEquals(30, $data['user']['age']);
        $this->assertTrue($data['user']['active']);

        $this->assertEquals('localhost', $data['config']['host']);
        $this->assertEquals(3306, $data['config']['port']);

        $this->assertEquals(1, $data['nested']['a']);
        $this->assertEquals(2, $data['nested']['b']);
        $this->assertEquals(3, $data['nested']['c']['d']);
        $this->assertEquals(4, $data['nested']['c']['e']);

        // echo "\n=== SIMPLE INLINE OBJECTS: PASSED ===\n";
    }

    /**
     * @test
     * 測試複雜巢狀行內結構（修復的核心） / Test complex nested inline structures (core of the fix)
     */
    public function testComplexNestedInlineStructures()
    {
        $yaml = <<<YAML
# 多屬性行內物件陣列 / Multi-property inline object arrays
items:
  - {id: 1, name: "Item A", x: 10, y: 20, z: 30}
  - {id: 2, name: "Item B", x: 15, y: 25, z: 35}
  - {id: 3, name: "Item C", x: 20, y: 30, z: 40}

# 物件內的行內陣列 / Inline arrays inside objects
tags:
  tag1: [a, b, c, d]
  tag2: [e, f]
  tag3: []

# 多層巢狀 / Multi-level nesting
data:
  level1:
    - {name: "First", nested: {value: 100, arr: [1, 2, 3]}}
    - {name: "Second", nested: {value: 200, arr: [4, 5, 6]}}

# 混合結構 / Mixed structures
complex:
  users: [{id: 1, skills: [php, js]}, {id: 2, skills: [python, go]}]
  matrix: [{row: 1, data: [a, b]}, {row: 2, data: [c, d]}]
YAML;

        $data = $this->parseYaml($yaml);

        /** 驗證多屬性物件 / Verify multi-property objects */
        $this->assertCount(3, $data['items']);
        $this->assertEquals(1, $data['items'][0]['id']);
        $this->assertEquals("Item A", $data['items'][0]['name']);
        $this->assertEquals(10, $data['items'][0]['x']);
        $this->assertEquals(20, $data['items'][0]['y']);
        $this->assertEquals(30, $data['items'][0]['z']);

        /** 驗證行內陣列在物件內 / Verify inline arrays inside objects */
        $this->assertCount(4, $data['tags']['tag1']);
        $this->assertEquals('a', $data['tags']['tag1'][0]);

        /** 驗證多層巢狀 / Verify multi-level nesting */
        $this->assertCount(2, $data['data']['level1']);
        $this->assertEquals("First", $data['data']['level1'][0]['name']);
        $this->assertEquals(100, $data['data']['level1'][0]['nested']['value']);
        $this->assertCount(3, $data['data']['level1'][0]['nested']['arr']);

        /** 驗證混合結構（關鍵測試：物件內的物件陣列） / Verify mixed structures (key test: object arrays inside objects) */
        $this->assertCount(2, $data['complex']['users']);
        $this->assertEquals(1, $data['complex']['users'][0]['id']);
        $this->assertCount(2, $data['complex']['users'][0]['skills']);
        $this->assertEquals('php', $data['complex']['users'][0]['skills'][0]);

        // echo "\n=== COMPLEX NESTED INLINE STRUCTURES: PASSED ===\n";
        // echo "This is the core fix: objects with multiple properties inside arrays\n";
    }

    /**
     * @test
     * 測試帶特殊字元的行內結構 / Test inline structures with special characters
     */
    public function testSpecialCharactersInInline()
    {
        $yaml = <<<YAML
# 包含逗號的字串（在行內陣列/物件中） / Strings containing commas
items_with_commas:
  - {name: "Item, with comma", desc: "Description, also has comma"}
  - {name: "Another, item", tags: ["tag,1", "tag,2"]}

# 引號內的冒號 / Colons in quotes
urls:
  - {url: "https://example.com:8080/path", name: "API"}
  - {url: "http://localhost:3000", name: "Local"}

# 中文與 Unicode / Chinese and Unicode
unicode:
  - {name: "中文測試", emoji: "🎉", tags: [中文, 測試]}
  - {name: "日本語テスト", value: 123}
YAML;

        $data = $this->parseYaml($yaml);

        /** 驗證逗號在行內結構中正確處理 / Verify commas in inline structures are handled correctly */
        $this->assertEquals("Item, with comma", $data['items_with_commas'][0]['name']);
        $this->assertEquals("Description, also has comma", $data['items_with_commas'][0]['desc']);

        /** 驗證引號保護內容 / Verify quotes protect content */
        $this->assertEquals("https://example.com:8080/path", $data['urls'][0]['url']);

        /** 驗證 Unicode / Verify Unicode */
        $this->assertEquals("中文測試", $data['unicode'][0]['name']);
        $this->assertEquals("🎉", $data['unicode'][0]['emoji']);

        // echo "\n=== SPECIAL CHARACTERS IN INLINE: PASSED ===\n";
    }

    /**
     * @test
     * 測試序列化與反序列化雙向測試 / Test serialization/deserialization roundtrip
     */
    public function testRoundtrip()
    {
        /** 測試簡單資料 / Test simple data */
        $simple = array(
            'name' => 'Test',
            'count' => 5,
            'active' => true,
        );

        $yaml = HOF_Class_Yaml::dump($simple);
        file_put_contents($this->tempFile, $yaml);
        $parsed = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($simple, $parsed, 'Simple data roundtrip failed');

        /** 測試行內物件陣列（核心修復測試） / Test inline object arrays (core fix test) */
        $inlineObjects = array(
            'patterns' => array(
                array('judge' => '1000', 'quantity' => 0, 'action' => '1001'),
                array('judge' => '2000', 'quantity' => 5, 'action' => '2002'),
            ),
            'tags' => array('php', 'yaml', 'test'),
        );

        $yaml2 = HOF_Class_Yaml::dump($inlineObjects);
        file_put_contents($this->tempFile, $yaml2);
        $parsed2 = HOF_Class_Yaml::load($this->tempFile);

        $this->assertEquals($inlineObjects, $parsed2, 'Inline object array roundtrip failed');

        /** 驗證解析後的 pattern 是正確的物件數組 / Verify parsed patterns are correct object arrays */
        $this->assertCount(2, $parsed2['patterns']);
        $this->assertInternalType('array', $parsed2['patterns'][0]);
        $this->assertEquals('1000', $parsed2['patterns'][0]['judge']);

        // echo "\n=== ROUNDTRIP TEST: PASSED ===\n";
        // echo "Data can be dumped and loaded back correctly\n";
    }

    /**
     * @test
     * 測試各種引號組合 / Test various quote combinations
     */
    public function testQuoteCombinations()
    {
        $yaml = <<<YAML
# 單引號 / Single quotes
single: {value: 'hello world', num: 42}

# 雙引號 / Double quotes
double: {value: "hello world", num: 42}

# 混合引號 / Mixed quotes
mixed:
  - {name: 'Single', desc: "Double", extra: 'More single'}
  - {name: "Double", desc: 'Single', extra: "More double"}

# 引號內的逗號和特殊字元 / Commas and special chars inside quotes
quoted_special:
  - {text: "This, has, commas", value: 100}
  - {text: 'Also, has, commas', value: 200}
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertEquals('hello world', $data['single']['value']);
        $this->assertEquals('hello world', $data['double']['value']);

        $this->assertEquals('Single', $data['mixed'][0]['name']);
        $this->assertEquals('Double', $data['mixed'][0]['desc']);

        $this->assertEquals('This, has, commas', $data['quoted_special'][0]['text']);
        $this->assertEquals('Also, has, commas', $data['quoted_special'][1]['text']);

        // echo "\n=== QUOTE COMBINATIONS: PASSED ===\n";
    }
}
