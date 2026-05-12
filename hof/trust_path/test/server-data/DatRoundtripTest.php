<?php

/**
 * 永續資料檔案 Roundtrip 測試
 * Persistent Data File Roundtrip Test
 *
 * 驗證 dat 目錄下的所有 YAML 檔案在讀取、序列化、再讀取後資料保持一致
 * Verify that all YAML files in the dat directory maintain data consistency after loading, serialization, and re-parsing.
 */
class DatRoundtripTest extends PHPUnit_Framework_TestCase
{
    /** 資料根目錄路徑 / Data root directory path */
    private $dataPath;

    /**
     * 測試初始化
     * Test initialization
     */
    protected function setUp()
    {
        $this->dataPath = BASE_TRUST_PATH . 'dat';
    }

    /**
     * 測試 dat 目錄下的所有 YAML 檔案
     * Test all YAML files in the dat directory
     *
     * @dataProvider yamlFileProvider
     */
    public function testDatRoundtrip($relativeFile)
    {
        $fullPath = $this->dataPath . DIRECTORY_SEPARATOR . $relativeFile;

        /**
         * 允許找不到檔案
         * Allow missing files
         */
        if (!file_exists($fullPath)) {
            $this->markTestSkipped("Dat file disappeared: $relativeFile");
            return;
        }

        /** 載入原始資料 / Load original data */
        $originalData = HOF_Class_Yaml::load($fullPath);

        /** 序列化為 YAML / Serialize to YAML */
        $dumpedYaml = HOF_Class_Yaml::dump($originalData);

        /** 重新解析 YAML / Re-parse YAML */
        $reparsedData = HOF_Class_Yaml::parse($dumpedYaml);

        /**
         * 驗證資料一致性
         * Verify data consistency
         */
        $this->assertEquals($originalData, $reparsedData, "Data mismatch after roundtrip in dat: $relativeFile");
    }

    /**
     * 提供資料目錄下的所有 YAML 檔案路徑
     * Provides all YAML file paths in the dat directory
     *
     * @return array
     */
    public function yamlFileProvider()
    {
        /** 手動定義路徑 / Define path manually */
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'dat';

        $files = array();
        if (is_dir($path)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() === 'yml') {
                    /** 取得相對於 dat 的路徑 / Get path relative to dat */
                    $relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $files[] = array($relativePath);
                }
            }
        }

        return $files;
    }
}
