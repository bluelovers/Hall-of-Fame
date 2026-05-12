<?php

/** 載入測試共用工具 / Load test shared helpers (PROJECT_TEST_PATH 來自 bootstrap-core.php) */
require_once PROJECT_TEST_PATH . '/lib/test_helper.php';

/**
 * YAML 語法全面測試 — 驗證 Symfony YAML Parser 對各種 YAML 語法的支援
 * Comprehensive YAML syntax test - Verify Symfony YAML Parser support for various YAML syntaxes
 *
 * @author Shadow Monarch
 * @copyright 2026
 */
class YamlSyntaxTest extends PHPUnit_Framework_TestCase
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

        /** 比照 production error_reporting / Match production error_reporting */
        _set_production_error_reporting();

        $this->tempFile = _temp_yaml_path('yaml_syntax_test');
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
     * 輔助函式：雙向測試（解析與序列化） / Helper: roundtrip test (parse and dump)
     *
     * @param mixed $data 原始資料 / Original data
     */
    private function assertRoundtrip($data, $message = 'Roundtrip test failed')
    {
        /** 序列化資料 / Dump data to YAML */
        $yaml = HOF_Class_Yaml::dump($data);
        file_put_contents($this->tempFile, $yaml);

        /** 重新解析 / Parse back */
        $parsed = HOF_Class_Yaml::load($this->tempFile);

        /** 驗證資料一致性 / Verify data consistency */
        $this->assertEquals($data, $parsed, $message);
    }

    /**
     * @test
     * 測試基本純量類型 / Test basic scalar types
     */
    public function testBasicScalars()
    {
        $yaml = <<<YAML
string_value: Hello World
single_quoted: 'single quoted string'
double_quoted: "double quoted string"
integer: 42
float: 3.14159
boolean_true: true
boolean_false: false
null_value: null
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertEquals('Hello World', $data['string_value']);
        $this->assertEquals('single quoted string', $data['single_quoted']);
        $this->assertEquals('double quoted string', $data['double_quoted']);
        $this->assertEquals(42, $data['integer']);
        $this->assertEquals(3.14159, $data['float']);
        $this->assertTrue($data['boolean_true']);
        $this->assertFalse($data['boolean_false']);
        $this->assertNull($data['null_value']);

        // echo "\n--- Basic Scalars Test Passed ---\n";
    }

    /**
     * @test
     * 測試巢狀陣列與物件 / Test nested arrays and objects
     *
     * ⚠️ 此測試暫時跳過 / This test is skipped
     * 原因：測試了「多行縮排列表（- item 格式）混用塊標量」，此語法目前暫不實裝支援
     * Reason: Tests "multi-line indented lists (- item format) mixed with block scalars", currently NOT supported
     *
     * ❌ 不支援的語法內容 / Unsupported syntax:
     * items:
     *   - name: A
     *     desc: |
     *       多行文字
     *   - name: B
     *     desc: |
     *       多行文字
     *
     * ✅ 替代方案：使用行內陣列格式 / Alternative: Use inline array format
     *   items: [{name: "A", desc: "單行文字"}, {name: "B", desc: "另一行"}]
     */
    public function testNestedStructures()
    {
        $this->markTestSkipped(
            'Multi-line indented list format (- item) is not supported. ' .
            'Use inline array format instead: [item1, item2] or [{key: value}, ...]'
        );
    }

    /**
     * @test
     * 測試行內陣列與物件語法（關鍵修復項目） / Test inline array and object syntax (critical fix item)
     */
    public function testInlineArraysAndObjects()
    {
        $yaml = <<<YAML
# 簡單行內陣列 / Simple inline array
simple_array: [apple, banana, cherry]

# 行內物件 / Inline object
simple_object: {name: "Test", value: 123}

# 巢狀行內結構（這是之前出問題的地方） / Nested inline structures (this was the bug)
nested_mixed:
  - {id: 1, name: "Item 1", tags: ["a", "b"]}
  - {id: 2, name: "Item 2", tags: ["c", "d", "e"]}
  - {id: 3, name: "Item 3", nested: {key: "value", num: 42}}

# 複雜巢狀行內陣列（模擬原本的 pattern 結構） / Complex nested inline array (simulating original pattern structure)
complex_pattern:
  behavior:
    position: front
    patterns: [{judge: "1000", quantity: 0, action: "1001"}, {judge: "2000", quantity: 5, action: "2002"}]

# 多層巢狀 / Multi-level nesting
deep_nesting:
  level1:
    - {name: "Level 1A", children: [{name: "Level 2A"}, {name: "Level 2B"}]}
    - {name: "Level 1B", data: {a: 1, b: 2, c: [x, y, z]}}
YAML;

        $data = $this->parseYaml($yaml);

        /** 驗證簡單行內陣列 / Verify simple inline array */
        $this->assertCount(3, $data['simple_array']);
        $this->assertEquals('apple', $data['simple_array'][0]);

        /** 驗證簡單行內物件 / Verify simple inline object */
        $this->assertEquals('Test', $data['simple_object']['name']);
        $this->assertEquals(123, $data['simple_object']['value']);

        /** 驗證巢狀混合結構（關鍵測試） / Verify nested mixed structures (critical test) */
        $this->assertCount(3, $data['nested_mixed']);

        /** 第一個項目包含標籤陣列 / First item has tags array */
        $this->assertEquals(1, $data['nested_mixed'][0]['id']);
        $this->assertCount(2, $data['nested_mixed'][0]['tags']);
        $this->assertEquals('a', $data['nested_mixed'][0]['tags'][0]);

        /** 第二個項目有更多標籤 / Second item has more tags */
        $this->assertEquals(2, $data['nested_mixed'][1]['id']);
        $this->assertCount(3, $data['nested_mixed'][1]['tags']);

        /** 第三個項目有巢狀物件 / Third item has nested object */
        $this->assertEquals(3, $data['nested_mixed'][2]['id']);
        $this->assertEquals('value', $data['nested_mixed'][2]['nested']['key']);
        $this->assertEquals(42, $data['nested_mixed'][2]['nested']['num']);

        /** 驗證複雜 pattern 結構（與原始問題相同的結構） / Verify complex pattern structure (same as original issue) */
        $this->assertCount(2, $data['complex_pattern']['behavior']['patterns']);
        $this->assertEquals('1000', $data['complex_pattern']['behavior']['patterns'][0]['judge']);
        $this->assertEquals(0, $data['complex_pattern']['behavior']['patterns'][0]['quantity']);
        $this->assertEquals('1001', $data['complex_pattern']['behavior']['patterns'][0]['action']);

        /** 驗證深層巢狀 / Verify deep nesting */
        $this->assertCount(2, $data['deep_nesting']['level1']);
        $this->assertCount(2, $data['deep_nesting']['level1'][0]['children']);
        $this->assertCount(3, $data['deep_nesting']['level1'][1]['data']['c']);

        // echo "\n--- Inline Arrays and Objects Test Passed ---\n";
        // echo "  ✓ Simple inline arrays work\n";
        // echo "  ✓ Simple inline objects work\n";
        // echo "  ✓ Nested inline structures with commas work (THE BUG FIX)\n";
        // echo "  ✓ Complex pattern structures work\n";
        // echo "  ✓ Multi-level nesting works\n";
    }

    /**
     * @test
     * 測試各種引號與特殊字元 / Test various quotes and special characters
     *
     * ⚠️ 注意：以下語法目前不支援 / The following are NOT supported:
     * - 多行字串（multi-line strings）
     * - 雙引號內轉義（\" 或 \n）
     * 替代方案：使用塊標量 | 或 > / Alternative: Use block scalars | or >
     */
    public function testQuotesAndEscapes()
    {
        $yaml = <<<YAML
# 單引號（不解析跳脫） / Single quotes (no escape parsing)
single_quote_special: 'Line with ''escaped'' quotes'

# 雙引號（無轉義） / Double quotes (no escaping)
double_quoted: "Hello World"

# 特殊字元 / Special characters
unicode_text: "中文測試 🎉"
url: "https://example.com/path?query=value&foo=bar"
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertEquals("Line with 'escaped' quotes", $data['single_quote_special']);
        $this->assertEquals("Hello World", $data['double_quoted']);
        $this->assertEquals("中文測試 🎉", $data['unicode_text']);
        $this->assertEquals("https://example.com/path?query=value&foo=bar", $data['url']);

        // echo "\n--- Quotes and Escapes Test Passed ---\n";
        // echo "✓ Single quotes with escaping work\n";
        // echo "✓ Double quotes work\n";
        // echo "✓ Unicode characters work\n";
    }

    /**
     * @test
     * 測試邊界情況 / Test edge cases
     */
    public function testEdgeCases()
    {
        $yaml = <<<YAML
# 空值 / Empty values
empty_string: ""
empty_single_quote: ''
tilde_null: ~
null_keyword: null

# 數字邊界 / Numeric boundaries
zero: 0
negative: -42
large_number: 999999999
float_zero: 0.0
negative_float: -3.14
scientific: 1.23e+10

# 布林值變體 / Boolean variants
yes_bool: yes
no_bool: no
true_bool: true
false_bool: false
on_bool: on
off_bool: off

# 空陣列與空物件 / Empty arrays and objects
empty_array: []
empty_object: {}

# 混合空與非空（行內格式）/ Mixed empty and non-empty (inline format)
mixed_empties: ["", null, [], {}, "non-empty"]
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertEquals(0, $data['zero']);
        $this->assertEquals(-42, $data['negative']);
        $this->assertEquals(999999999, $data['large_number']);
        $this->assertEquals(array(), $data['empty_array']);
        $this->assertEquals(array(), $data['empty_object']);

        /** 驗證布林值解析 / Verify boolean parsing */
        /** yes/no/on/off 等會被解析為布林值或字串 / yes/no/on/off are parsed as boolean or string */
        $this->assertTrue($data['true_bool'] === true);
        $this->assertTrue($data['false_bool'] === false);
        /** 驗證變體格式被正確解析 / Verify variant formats are correctly parsed */
        $this->assertContains($data['yes_bool'], array(true, 'yes'), 'yes_bool should be true or "yes"');
        $this->assertContains($data['no_bool'], array(false, 'no'), 'no_bool should be false or "no"');
        $this->assertContains($data['on_bool'], array(true, 'on'), 'on_bool should be true or "on"');
        $this->assertContains($data['off_bool'], array(false, 'off'), 'off_bool should be false or "off"');

        /** 驗證行內格式混合空值 / Verify inline format with mixed empty values */
        $this->assertCount(5, $data['mixed_empties']);

        // echo "\n--- Edge Cases Test Passed ---\n";
        // echo "✓ Empty values work\n";
        // echo "✓ Numeric boundaries work\n";
        // echo "✓ Boolean variants work\n";
        // echo "✓ Empty inline arrays/objects work\n";
    }

    /**
     * @test
     * 測試序列化與反序列化（雙向測試） / Test serialization and deserialization (roundtrip)
     */
    public function testRoundtrip()
    {
        /** 測試簡單資料 / Test simple data */
        $simple = array(
            'name' => 'Test',
            'count' => 5,
            'active' => true,
        );
        $this->assertRoundtrip($simple, 'Simple data roundtrip failed');

        /** 測試巢狀資料（行內格式） / Test nested data (inline format) */
        $nested = array(
            'users' => array(
                array('id' => 1, 'name' => 'Alice', 'tags' => array('dev', 'admin')),
                array('id' => 2, 'name' => 'Bob', 'tags' => array('user')),
            ),
            'config' => array(
                'database' => array('host' => 'localhost', 'port' => 3306),
                'cache' => array('enabled' => true, 'ttl' => 3600),
            ),
        );
        $this->assertRoundtrip($nested, 'Nested data roundtrip failed');

        /** 測試與原始問題相同的結構 / Test same structure as original issue */
        $patternData = array(
            'behavior' => array(
                'position' => 'front',
                'guard' => 'always',
                'patterns' => array(
                    array('judge' => '1000', 'quantity' => 0, 'action' => '1001'),
                    array('judge' => '1000', 'quantity' => 0, 'action' => '3120'),
                ),
            ),
        );
        $this->assertRoundtrip($patternData, 'Pattern data roundtrip failed - THIS WAS THE BUG!');

        // echo "\n--- Roundtrip Test Passed ---\n";
        // echo "  ✓ Simple data roundtrips correctly\n";
        // echo "  ✓ Nested data roundtrips correctly\n";
        // echo "  ✓ Pattern data (original bug case) roundtrips correctly\n";
    }

    /**
     * @test
     * 測試保留原始 YAML 格式（特別針對修復的問題） / Test preserving original YAML format (specific to the fix)
     */
    public function testOriginalBugCase()
    {
        /** 這是原始出問題的 YAML 內容 / This is the original problematic YAML */
        $originalYaml = <<<YAML
id: b3e304903f09e14b8386a49a1e1e01e3
name: Hero1
behavior:
  position: front
  guard: always
  pattern: [{judge: "1000", quantity: 0, action: "1001"}, {judge: "1000", quantity: 0, action: "3120"}]
YAML;

        $data = $this->parseYaml($originalYaml);

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

        // echo "\n--- Original Bug Case Test Passed ---\n";
        // echo "  ✓ Pattern array has exactly 2 items (was 6 string fragments before fix)\n";
        // echo "  ✓ Each pattern item is a proper object with judge, quantity, action\n";
        // echo "  ✓ All values are correctly parsed\n";
    }

    /**
     * @test
     * 測試複雜巢狀逗號場景 / Test complex nested comma scenarios
     */
    public function testComplexCommaScenarios()
    {
        /** 這個測試專門針對 parseSequence 中的逗號處理 / This test specifically targets comma handling in parseSequence */
        $yaml = <<<YAML
# 多層巢狀行內物件，每個都有多個屬性（多個逗號） / Multi-level nested inline objects, each with multiple properties
complex_items:
  - {id: 1, x: 10, y: 20, z: 30, name: "Item A", tags: [a, b, c]}
  - {id: 2, x: 15, y: 25, z: 35, name: "Item B", tags: [d, e]}
  - {id: 3, x: 20, y: 30, z: 40, name: "Item C", tags: [f]}

# 行內陣列內的行內物件 / Inline objects inside inline array
team_members: [{name: "Leader", role: "admin", skills: [php, js, python]}, {name: "Member", role: "user", skills: [js]}]

# 物件內的巢狀陣列 / Nested arrays inside objects
matrix_data:
  rows: [{cells: [1, 2, 3], label: "Row 1"}, {cells: [4, 5, 6], label: "Row 2"}]

# URL 與特殊字元（包含逗號的場景） / URLs and special characters (comma-containing scenarios)
urls_with_params:
  - {url: "https://example.com/api?a=1&b=2", name: "API Endpoint"}
  - {url: "https://example.com/search?q=test,results", name: "Search Page"}
YAML;

        $data = $this->parseYaml($yaml);

        /** 驗證複雜項目 / Verify complex items */
        $this->assertCount(3, $data['complex_items']);
        $this->assertEquals(1, $data['complex_items'][0]['id']);
        $this->assertEquals(10, $data['complex_items'][0]['x']);
        $this->assertEquals("Item A", $data['complex_items'][0]['name']);
        $this->assertCount(3, $data['complex_items'][0]['tags']);

        /** 驗證團隊成員 / Verify team members */
        $this->assertCount(2, $data['team_members']);
        $this->assertEquals("Leader", $data['team_members'][0]['name']);
        $this->assertCount(3, $data['team_members'][0]['skills']);

        /** 驗證矩陣資料 / Verify matrix data */
        $this->assertCount(2, $data['matrix_data']['rows']);
        $this->assertCount(3, $data['matrix_data']['rows'][0]['cells']);

        /** 驗證 URL 正確解析 / Verify URLs are parsed correctly */
        $this->assertCount(2, $data['urls_with_params']);
        $this->assertEquals("https://example.com/api?a=1&b=2", $data['urls_with_params'][0]['url']);

        // echo "\n--- Complex Comma Scenarios Test Passed ---\n";
        // echo "  ✓ Multi-property inline objects with many commas work\n";
        // echo "  ✓ Nested inline arrays inside inline objects work\n";
        // echo "  ✓ Complex nesting levels work correctly\n";
        // echo "  ✓ URLs with special characters preserved\n";
    }
}
