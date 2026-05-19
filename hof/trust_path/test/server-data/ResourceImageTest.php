<?php

/**
 * Resource 圖片檔案存在性測試
 * Resource Image File Existence Test
 *
 * 驗證 Resource 目錄下所有 YAML 檔案中引用的 img 欄位，
 * 在對應的 static/image/ 子目錄中確實存在實際的圖片檔案。
 *
 * Verifies that all img fields referenced in Resource YAML files
 * have corresponding image files in the static/image/ sub-directories.
 */

/** 載入 Resource 測試共用工具 / Load Resource test shared utilities */
require_once PROJECT_TEST_PATH . '/lib/ResourceTestHelper.php';

class ResourceImageTest extends PHPUnit_Framework_TestCase
{
    /** 支援的圖片副檔名 / Supported image extensions */
    const IMG_EXTENSIONS = array('png', 'gif', 'jpg', 'bmp');

    /**
     * 測試所有 Resource 類型 YAML 中引用的圖片檔案都存在
     * Test that all images referenced in Resource YAML files exist
     *
     * 涵蓋類型（Covered types）：
     * - Item → static/image/icon/item/
     * - Skill → static/image/icon/skill/
     * - Char / Mon / Job / Union → static/image/char/
     */
    public function testResourceTypeImagesExist()
    {
        $typeImgMapping = _resource_type_img_mapping();
        $missingImages = array();

        foreach ($typeImgMapping as $type => $imgSubDir)
        {
            $typeDir = PROJECT_GAME_DATA_RESOURCE_PATH . DIRECTORY_SEPARATOR . $type;
            $files = glob($typeDir . DIRECTORY_SEPARATOR . '*.yml');

            foreach ($files as $file)
            {
                /** 載入 YAML / Load YAML */
                $data = HOF_Class_Yaml::load($file);
                /** 遞迴提取所有 img 欄位 / Recursively extract all img fields */
                $imgValues = _resource_extract_img_fields($data);
                $relativeFile = basename($file);

                foreach ($imgValues as $img)
                {
                    $found = false;
                    foreach (self::IMG_EXTENSIONS as $ext)
                    {
                        if (file_exists(PROJECT_STATIC_IMAGE_PATH . '/' . $imgSubDir . $img . '.' . $ext))
                        {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found)
                    {
                        $missingImages[] = "$type/$relativeFile: img='$img' not found in static/image/$imgSubDir";
                    }
                }
            }
        }

        $this->assertEmpty($missingImages,
            "Missing images (" . count($missingImages) . " total):\n" . implode("\n", $missingImages));
    }

    /**
     * 測試所有 Land YAML 中引用的背景圖片都存在
     * Test that all background images referenced in Land YAML files exist
     *
     * Land 圖片命名規則為 bg_{land.land}.png 和 land_{land.land}.png，
     * 存放於 static/image/land/ 目錄。
     *
     * Land image naming convention: bg_{land.land}.png and land_{land.land}.png,
     * stored in static/image/land/ directory.
     */
    public function testLandImagesExist()
    {
        $landDir = PROJECT_GAME_DATA_RESOURCE_PATH . DIRECTORY_SEPARATOR . 'Land';
        $files = glob($landDir . DIRECTORY_SEPARATOR . '*.yml');
        $landPrefixes = array('bg_', 'land_');
        $missingImages = array();

        foreach ($files as $file)
        {
            $data = HOF_Class_Yaml::load($file);
            $relativeFile = basename($file);

            if (!isset($data['land']['land']))
            {
                $missingImages[] = "Land/$relativeFile: missing 'land.land' field";
                continue;
            }

            $landType = $data['land']['land'];

            foreach ($landPrefixes as $prefix)
            {
                $imgName = $prefix . $landType;
                $found = false;

                foreach (self::IMG_EXTENSIONS as $ext)
                {
                    if (file_exists(PROJECT_STATIC_IMAGE_PATH . '/land/' . $imgName . '.' . $ext))
                    {
                        $found = true;
                        break;
                    }
                }

                if (!$found)
                {
                    $missingImages[] = "Land/$relativeFile: img='$imgName' not found in static/image/land/";
                }
            }
        }

        $this->assertEmpty($missingImages,
            "Missing Land images (" . count($missingImages) . " total):\n" . implode("\n", $missingImages));
    }

    /**
     * 測試 static/image/ 目錄中有未被任何 YAML 引用的圖片檔案
     * Test for unreferenced image files in static/image/ directories
     *
     * 此為反向檢查：找出在目錄中存在但未被任何 Resource YAML 使用的圖片。
     * This is a reverse check: find images that exist on disk but are not referenced by any Resource YAML.
     *
     * 注意：有些圖片可能由模板或控制器直接引用（不透過 YAML），
     * 因此此測試的結果僅供參考。
     *
     * Note: Some images may be referenced directly by templates or controllers (not through YAML),
     * so the results should be treated as reference only.
     */
    public function testNoUnreferencedImages()
    {
        /** 掃描所有 Resource YAML 收集引用圖片 / Scan all Resource YAML to collect referenced images */
        $referencedImages = array();

        $typeDirs = _resource_type_dirs();

        foreach ($typeDirs as $type)
        {
            $typeDir = PROJECT_GAME_DATA_RESOURCE_PATH . DIRECTORY_SEPARATOR . $type;
            $files = glob($typeDir . DIRECTORY_SEPARATOR . '*.yml');

            foreach ($files as $file)
            {
                $data = HOF_Class_Yaml::load($file);
                $imgValues = _resource_extract_img_fields($data);
                $referencedImages = array_merge($referencedImages, $imgValues);
            }
        }

        /** 加入 Land 的 bg_ / land_ 前綴圖片 / Include Land bg_ / land_ prefix images */
        $landDir = PROJECT_GAME_DATA_RESOURCE_PATH . DIRECTORY_SEPARATOR . 'Land';
        $landFiles = glob($landDir . DIRECTORY_SEPARATOR . '*.yml');
        foreach ($landFiles as $file)
        {
            $data = HOF_Class_Yaml::load($file);
            if (isset($data['land']['land']))
            {
                $landType = $data['land']['land'];
                $referencedImages[] = 'bg_' . $landType;
                $referencedImages[] = 'land_' . $landType;
            }
        }

        $referencedImages = array_unique($referencedImages);

        /** 掃描圖片目錄找出未被引用的檔案 / Scan image directories for unreferenced files */
        $unreferenced = array();

        $imgDirs = _resource_img_dirs();

        foreach ($imgDirs as $imgSubDir)
        {
            $dirPath = PROJECT_STATIC_IMAGE_PATH . '/' . $imgSubDir;
            if (!is_dir($dirPath))
            {
                continue;
            }

            $files = scandir($dirPath);
            foreach ($files as $file)
            {
                if ($file === '.' || $file === '..' || $file === 'index.htm')
                {
                    continue;
                }

                $pathInfo = pathinfo($file);
                $name = $pathInfo['filename'];

                /** 跳過 noimage（fallback 圖片）/ Skip noimage (fallback image) */
                if ($name === 'noimage')
                {
                    continue;
                }

                if (!in_array($name, $referencedImages))
                {
                    $unreferenced[] = $imgSubDir . $file;
                }
            }
        }

        /** 僅在有不被引用的圖片時記錄，此測試不為失敗（僅供參考）*/
        /** Only log unreferenced images; this test does not fail (reference only) */
        if (count($unreferenced) > 0)
        {
            $this->markTestSkipped(
                "Unreferenced images (" . count($unreferenced) . " total):\n" .
                "These images exist on disk but are not referenced by any Resource YAML:\n" .
                implode("\n", $unreferenced) . "\n\n" .
                "Note: Some may be used directly by templates/controllers."
            );
        }
    }
}
