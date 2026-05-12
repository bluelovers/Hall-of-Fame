<?php

/**
 * 資源檔案 Roundtrip 測試
 * Resource File Roundtrip Test
 *
 * 驗證 Resource 目錄下的所有 YAML 檔案在讀取、序列化、再讀取後資料保持一致
 * Verify that all YAML files in the Resource directory maintain data consistency after loading, serialization, and re-parsing.
 */
class ResourceRoundtripTest extends PHPUnit_Framework_TestCase
{
    /** 資源根目錄路徑 / Resource root directory path */
    private $resourcePath;

    /**
     * 測試初始化
     * Test initialization
     */
    protected function setUp()
    {
        $this->resourcePath = BASE_TRUST_PATH . 'HOF/Resource';
    }

    /**
     * 測試 Resource 目錄下的所有 YAML 檔案
     * Test all YAML files in the Resource directory
     *
     * @dataProvider yamlFileProvider
     */
    public function testResourceRoundtrip($relativeFile)
    {
        $fullPath = $this->resourcePath . DIRECTORY_SEPARATOR . $relativeFile;

        /** 載入原始資料 / Load original data */
        $originalData = HOF_Class_Yaml::load($fullPath);
        $this->assertNotEmpty($originalData, "Failed to load data from: $relativeFile");

        /** 序列化為 YAML / Serialize to YAML */
        $dumpedYaml = HOF_Class_Yaml::dump($originalData);
        $this->assertNotEmpty($dumpedYaml, "Failed to dump data from: $relativeFile");

        /** 重新解析 YAML / Re-parse YAML */
        $reparsedData = HOF_Class_Yaml::parse($dumpedYaml);

        /**
         * 驗證資料一致性
         * Verify data consistency
         *
         * 使用 assertEquals 而非 assertSame，因為 YAML 序列化可能會影響深層結構的類型（如物件變陣列）
         * 但 HOF_Class_Yaml::load 已處理過 addslashes 等，Roundtrip 應保持內容相等
         * Use assertEquals instead of assertSame because YAML serialization might affect deep structure types (e.g. object to array)
         * But HOF_Class_Yaml::load already handles addslashes, so roundtrip should maintain value equality
         */
        $this->assertEquals($originalData, $reparsedData, "Data mismatch after roundtrip in: $relativeFile");
    }

    /**
     * 提供資源目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the resource directory
     *
     * @return array
     */
    public function yamlFileProvider()
    {
        /**
         * 如果環境尚未完全初始化（如剛啟動），手動定義路徑
         * If environment is not fully initialized, define path manually
         */
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'HOF' . DIRECTORY_SEPARATOR . 'Resource';

        $files = array();
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'yml') {
                    /** 取得相對於 Resource 的路徑 / Get path relative to Resource */
                    $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $files[] = array($relativePath);
                }
            }
        }

        return $files;
    }
}
