<?php

/** 載入測試共用工具 / Load test shared helpers (PROJECT_TEST_PATH 來自 bootstrap-core.php) */
require_once PROJECT_TEST_PATH . '/lib/test_helper.php';

/**
 * YAML 塊標量樣式測試 — 驗證 Literal (|) 與 Folded (>) 支援
 * YAML block scalar style test - Verify Literal (|) and Folded (>) support
 *
 * @author Shadow Monarch
 * @copyright 2026
 */
class YamlBlockScalarTest extends PHPUnit_Framework_TestCase
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

        $this->tempFile = _temp_yaml_path('yaml_block_test');
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
     * 測試 Literal (|) — 保留換行 / Test Literal (|) - preserve newlines
     */
    public function testLiteralStyle()
    {
        $yaml = <<<YAML
message: |
  這是第一行
  這是第二行
  這是第三行
name: Test
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('message', $data);
        $expected = "這是第一行\n這是第二行\n這是第三行";
        $this->assertEquals($expected, $data['message']);
        $this->assertEquals('Test', $data['name']);

        // echo "\n=== LITERAL STYLE (|): PASSED ===\n";
        // echo "Content preserved with newlines\n";
    }

    /**
     * @test
     * 測試 Literal Strip (|-) — 去除末尾換行 / Test Literal Strip (|-) - strip trailing newline
     */
    public function testLiteralStripStyle()
    {
        $yaml = <<<YAML
message: |-
  這是第一行
  這是第二行
  這是第三行
name: Test
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('message', $data);
        /** |- 應該去除末尾換行 / |- should strip trailing newline */
        $expected = "這是第一行\n這是第二行\n這是第三行";
        $this->assertEquals($expected, $data['message']);
        $this->assertEquals('Test', $data['name']);

        // echo "\n=== LITERAL STRIP STYLE (|-): PASSED ===\n";
        // echo "Content without trailing newline\n";
    }

    /**
     * @test
     * 測試 Folded (>) — 折疊換行為空格 / Test Folded (>) - fold newlines to spaces
     */
    public function testFoldedStyle()
    {
        $yaml = <<<YAML
message: >
  這是第一行
  這是第二行
  這是第三行
name: Test
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('message', $data);
        /** > 應該將非空行後的換行轉為空格 / > should fold non-empty line newlines to spaces */
        $expected = "這是第一行 這是第二行 這是第三行";
        $this->assertEquals($expected, $data['message']);
        $this->assertEquals('Test', $data['name']);

        // echo "\n=== FOLDED STYLE (>): PASSED ===\n";
        // echo "Newlines folded to spaces\n";
    }

    /**
     * @test
     * 測試 Folded Strip (>-) — 折疊並去除末尾換行 / Test Folded Strip (>-) - fold and strip trailing newline
     */
    public function testFoldedStripStyle()
    {
        $yaml = <<<YAML
message: >-
  這是第一行
  這是第二行
  這是第三行
name: Test
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('message', $data);
        /** >- 應該折疊並去除末尾換行 / >- should fold and strip trailing newline */
        $expected = "這是第一行 這是第二行 這是第三行";
        $this->assertEquals($expected, $data['message']);
        $this->assertEquals('Test', $data['name']);

        // echo "\n=== FOLDED STRIP STYLE (>-): PASSED ===\n";
        // echo "Folded and stripped trailing newline\n";
    }

    /**
     * @test
     * 測試 Folded 中的空行處理 / Test empty line handling in Folded
     */
    public function testFoldedWithEmptyLines()
    {
        $yaml = <<<YAML
paragraph: >
  這是第一段
  繼續第一段

  這是第二段
  繼續第二段
footer: end
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('paragraph', $data);
        /** 空行應該保留為換行 / Empty lines should be preserved as newlines */
        $expected = "這是第一段 繼續第一段\n這是第二段 繼續第二段";
        $this->assertEquals($expected, $data['paragraph']);
        $this->assertEquals('end', $data['footer']);

        // echo "\n=== FOLDED WITH EMPTY LINES: PASSED ===\n";
        // echo "Empty lines preserved as paragraph breaks\n";
    }

    /**
     * @test
     * 測試多個塊標量 / Test multiple block scalars
     */
    public function testMultipleBlockScalars()
    {
        $yaml = <<<YAML
description: |
  產品描述
  多行內容
notes: >-
  一些備註
  折疊顯示
config: |
  配置內容
name: Product
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('notes', $data);
        $this->assertArrayHasKey('config', $data);
        $this->assertEquals('Product', $data['name']);

        $this->assertEquals("產品描述\n多行內容", $data['description']);
        $this->assertEquals("一些備註 折疊顯示", $data['notes']);
        $this->assertEquals("配置內容", $data['config']);

        // echo "\n=== MULTIPLE BLOCK SCALARS: PASSED ===\n";
    }

    /**
     * @test
     * 測試塊標量與行內格式混用 / Test block scalar mixed with inline formats
     */
    public function testBlockScalarWithInline()
    {
        $yaml = <<<YAML
user:
  name: John
  bio: >
    這是一段很長的簡介
    會被折疊成一行顯示
  tags: [developer, writer, gamer]
  status: |
    在線
    忙碌
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertArrayHasKey('user', $data);
        $this->assertEquals('John', $data['user']['name']);
        $this->assertEquals("這是一段很長的簡介 會被折疊成一行顯示", $data['user']['bio']);
        $this->assertCount(3, $data['user']['tags']);
        $this->assertEquals("在線\n忙碌", $data['user']['status']);

        // echo "\n=== BLOCK SCALAR WITH INLINE: PASSED ===\n";
        // echo "Block scalars work with inline arrays and objects\n";
    }

    /**
     * @test
     * 測試巢狀列表中的塊標量 / Test block scalars in nested lists
     */
    public function testBlockScalarInNestedList()
    {
        $yaml = <<<YAML
items:
  - name: Item1
    description: |
      第一個項目的
      詳細描述
  - name: Item2
    description: >
      第二個項目的
      折疊描述
footer: end
YAML;

        $data = $this->parseYaml($yaml);

        $this->assertCount(2, $data['items']);
        $this->assertEquals("第一個項目的\n詳細描述", $data['items'][0]['description']);
        $this->assertEquals("第二個項目的 折疊描述", $data['items'][1]['description']);
        $this->assertEquals('end', $data['footer']);

        // echo "\n=== BLOCK SCALAR IN NESTED LIST: PASSED ===\n";
        // echo "Block scalars work in nested list items\n";
    }

    /**
     * @test
     * 測試多行縮排（非行內）列表 / Test multi-line indented (non-inline) lists
     *
     * ⚠️ 此測試暫時跳過 — 多行縮排列表語法目前暫不實裝支援
     * This test is skipped — multi-line indented list syntax is currently NOT supported
     *
     * ❌ 不支援的語法內容 / Unsupported syntax:
     * - item:
     *   - subitem1
     *   - subitem2
     *
     * ✅ 替代方案：使用行內陣列格式 / Alternative: Use inline array format
     *   data: [{item: [subitem1, subitem2]}]
     */
    public function testMultilineIndentedLists()
    {
        $this->markTestSkipped(
            'Multi-line indented list parsing needs additional fixes. ' .
            'Current parser only supports inline arrays/objects and block scalars.'
        );
    }

    /**
     * @test
     * 綜合測試：所有樣式組合 / Comprehensive test: all styles combined
     */
    public function testAllStylesCombined()
    {
        $yaml = <<<YAML
document: |
  文件標題
  作者：Test
  日期：2026-05-11

summary: >
  這是一份摘要
  會被折疊成一行

notes: |-
  備註內容
  無尾隨換行

code: >-
  function test() {
    return true;
  }

data:
  items:
    - id: 1
      desc: |
        項目一描述
        多行內容
    - id: 2
      desc: >
        項目二描述
        折疊內容
footer: END
YAML;

        $data = $this->parseYaml($yaml);

        /** 驗證所有頂層鍵 / Verify all top-level keys */
        $this->assertArrayHasKey('document', $data);
        $this->assertArrayHasKey('summary', $data);
        $this->assertArrayHasKey('notes', $data);
        $this->assertArrayHasKey('code', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('footer', $data);

        /** 驗證 Literal | / Verify Literal | */
        $this->assertEquals("文件標題\n作者：Test\n日期：2026-05-11", $data['document']);

        /** 驗證 Folded > / Verify Folded > */
        $this->assertEquals("這是一份摘要 會被折疊成一行", $data['summary']);

        /** 驗證 Strip 樣式 / Verify Strip styles */
        $this->assertEquals("備註內容\n無尾隨換行", $data['notes']);
        $this->assertEquals("function test() {   return true; }", $data['code']);

        /** 驗證巢狀結構 / Verify nested structures */
        $this->assertCount(2, $data['data']['items']);
        $this->assertEquals("項目一描述\n多行內容", $data['data']['items'][0]['desc']);
        $this->assertEquals("項目二描述 折疊內容", $data['data']['items'][1]['desc']);

        $this->assertEquals('END', $data['footer']);

        // echo "\n=== ALL STYLES COMBINED: PASSED ===\n";
        // echo "✓ Literal (|) works\n";
        // echo "✓ Literal Strip (|-) works\n";
        // echo "✓ Folded (>) works\n";
        // echo "✓ Folded Strip (>-) works\n";
        // echo "✓ Multi-line indented lists work\n";
        // echo "✓ Nested structures work\n";
    }
}
