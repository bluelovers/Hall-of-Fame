<?php

/**
 * 快取檔案 Roundtrip 測試
 * Cache File Roundtrip Test
 *
 * 驗證 cache 目錄下的所有 YAML 檔案在讀取、序列化、再讀取後資料保持一致
 * 注意：允許檔案不存在（可能在測試過程中被刪除）
 * Verify that all YAML files in the cache directory maintain data consistency after loading, serialization, and re-parsing.
 * Note: Allows files to be missing (may be deleted during testing).
 */
class CacheRoundtripTest extends PHPUnit_Framework_TestCase
{
    /** 快取根目錄路徑 / Cache root directory path */
    private $cachePath;

    /**
     * 測試初始化
     * Test initialization
     */
    protected function setUp()
    {
        $this->cachePath = BASE_TRUST_PATH . 'cache';
    }

    /**
     * 測試 cache 目錄下的所有 YAML 檔案
     * Test all YAML files in the cache directory
     *
     * @dataProvider yamlFileProvider
     */
    public function testCacheRoundtrip($relativeFile)
    {
        $fullPath = $this->cachePath . DIRECTORY_SEPARATOR . $relativeFile;

        /**
         * 允許找不到檔案，因為快取可能隨時被刪除
         * Allow missing files as cache can be deleted anytime
         */
        if (!file_exists($fullPath)) {
            $this->markTestSkipped("Cache file disappeared: $relativeFile");
            return;
        }

        /** 載入原始資料 / Load original data */
        $originalData = HOF_Class_Yaml::load($fullPath);

        /**
         * 如果載入結果為空且檔案此時不存在，則跳過
         * If load result is empty and file doesn't exist now, skip
         */
        if (empty($originalData) && !file_exists($fullPath)) {
            $this->markTestSkipped("Cache file disappeared during load: $relativeFile");
            return;
        }

        /** 序列化為 YAML / Serialize to YAML */
        $dumpedYaml = HOF_Class_Yaml::dump($originalData);

        /** 重新解析 YAML / Re-parse YAML */
        $reparsedData = HOF_Class_Yaml::parse($dumpedYaml);

        /**
         * 驗證資料一致性
         * Verify data consistency
         */
        $this->assertEquals($originalData, $reparsedData, "Data mismatch after roundtrip in cache: $relativeFile");
    }

    /**
     * 提供快取目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the cache directory
     *
     * @return array
     */
    public function yamlFileProvider()
    {
        /** 手動定義路徑 / Define path manually */
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'cache';

        $files = array();
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'yml') {
                    /** 取得相對於 cache 的路徑 / Get path relative to cache */
                    $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $files[] = array($relativePath);
                }
            }
        }

        return $files;
    }
}
