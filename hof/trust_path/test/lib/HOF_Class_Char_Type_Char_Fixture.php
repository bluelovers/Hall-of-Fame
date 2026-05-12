<?php

/**
 * 測試用角色類別 — 從 Fixture YAML 載入資料
 * Test character class — loads data from fixture YAML
 *
 * 繼承 HOF_Class_Char_Type_Char，覆寫 source() 方法
 * 使其從 test/fixtures/char_test_data.yml 載入資料，
 * 而非從遊戲使用者資料目錄載入。
 *
 * 這樣測試就不會依賴遊戲實際資料，可保持穩定性。
 *
 * Extends HOF_Class_Char_Type_Char, overrides source() method
 * to load from test/fixtures/char_test_data.yml instead of
 * the game user data directory.
 *
 * This ensures the test does not depend on actual game data,
 * maintaining stability.
 *
 * @author Shadow Monarch
 * @copyright 2026
 */
class HOF_Class_Char_Type_Char_Fixture extends HOF_Class_Char_Type_Char
{
    /**
     * 從測試 Fixture 檔案載入原始資料
     * Load raw data from test fixture file
     *
     * @param bool $over - 是否強制重新載入 / Whether to force reload
     * @return HOF_Class_Array 角色資料的 ArrayObject / Character data as ArrayObject
     */
    function source($over = false)
    {
        /** 使用快取避免重複載入 / Use cache to avoid repeated loading */
        $data = &$this->_cache_char_[__FUNCTION__];
        $type = $this->option('type');

        if (!isset($data[$type]) || $over)
        {
            /**
             * 從 Fixture 檔案載入 / Load from fixture file
             *
             * 使用 PROJECT_TEST_PATH（定義於 bootstrap-core.php）作為單一事實來源
             * Uses PROJECT_TEST_PATH (defined in bootstrap-core.php) as single source of truth
             */
            $fixtureFile = PROJECT_TEST_PATH . '/fixtures/char_test_data.yml';
            $this->fp = HOF_Class_File::fplock_file($fixtureFile);
            $data[$type] = HOF_Class_Yaml::load($this->fp);

            /** 確保出生時間存在 / Ensure birth time exists */
            if (!$data[$type]['birth'])
            {
                $data[$type]['birth'] = HOF_Helper_Char::uniqid_birth();
            }

            /** 轉換為 HOF_Class_Array / Convert to HOF_Class_Array */
            $data[$type] = new HOF_Class_Array($data[$type]);
        }

        /**
         * 設定 $this->source 屬性以保持與 Abstract::initCharData() 的相容性
         * Set $this->source property for compatibility with Abstract::initCharData()
         *
         * Abstract::initCharData() 同時使用 source() 方法回傳值和 $this->source 屬性
         * Abstract::initCharData() uses both the source() return value and $this->source property
         */
        $this->source = $data[$type];

        return $data[$type];
    }
}
