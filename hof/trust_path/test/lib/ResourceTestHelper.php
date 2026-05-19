<?php

/**
 * Resource 測試共用工具函數
 * Resource Test Shared Helper Functions
 *
 * 提供 YAML 檔案掃描、資料類型驗證、圖片存在性檢查等共用功能，
 * 避免各測試檔案重複實作相同邏輯。
 *
 * Provides shared utilities for YAML file scanning, data type validation,
 * and image existence checking to avoid duplicated logic across test files.
 *
 * ⚠️ 此為純函數式輔助工具，不使用物件導向包裝。
 *    若功能需要狀態管理或生命週期，再考慮封裝為類別。
 *
 * @author Shadow Monarch
 * @copyright 2026
 */

/**
 * ============================================================
 * Resource 類型與圖片路徑對應（共用的外部可視資料）
 * Resource Type to Image Path Mapping (Shared Data Sources)
 * ============================================================
 *
 * 以下函數由 ResourceImageTest 與 _resource_check_missing_images 共用，
 * 集中定義於此處以維護單一事實來源。
 *
 * The following functions are shared between ResourceImageTest
 * and _resource_check_missing_images, centralized here for
 * single source of truth.
 */

/**
 * Resource 類型 → IMG 子目錄對應
 * Resource type → image sub-directory mapping
 *
 * 每個 Resource 類型（Item, Skill, Char 等）的 img 欄位值，
 * 應存在於對應的 static/image/ 子目錄中。
 *
 * Each Resource type's img field values should exist in the
 * corresponding static/image/ sub-directory.
 *
 * @return array
 */
function _resource_type_img_mapping()
{
	return array(
		'Item' => 'icon/item/',
		'Skill' => 'icon/skill/',
		'Char' => 'char/',
		'Mon' => 'char/',
		'Job' => 'char/',
		'Union' => 'char/',
	);
}

/**
 * Resource 掃描類型列表
 * Resource type list for scanning
 *
 * 用於完整掃描所有 Resource YAML 中引用的 img 欄位。
 * Used for comprehensive scanning of all img fields across Resource YAML files.
 *
 * @return array
 */
function _resource_type_dirs()
{
	return array('Item', 'Skill', 'Char', 'Mon', 'Job', 'Union', 'Land');
}

/**
 * 圖片掃描目錄列表
 * Image directory list for scanning
 *
 * 用於反向檢查 static/image/ 下未被任何 YAML 引用的圖片。
 * Used for reverse-checking unreferenced images in static/image/.
 *
 * @return array
 */
function _resource_img_dirs()
{
	return array(
		'icon/item/',
		'icon/skill/',
		'char/',
		'char_rev/',
		'land/',
	);
}

/**
 * 通用 YAML 檔案搜尋（支援遞迴子目錄）
 * Generic YAML file finder (recursive, for DataProvider)
 *
 * @param string $subDir Resource 下的子目錄名稱（. 表示 Resource 根目錄）/ Subdirectory name under Resource
 * @param bool $excludeCache 是否排除 cache 目錄 / Whether to exclude cache directory
 * @return array
 */
function _resource_glob_yaml($subDir, $excludeCache = false)
{
	$path = PROJECT_GAME_DATA_RESOURCE_PATH;
	if ($subDir !== '.')
	{
		$path .= DIRECTORY_SEPARATOR . $subDir;
	}

	$files = array();
	if (is_dir($path))
	{
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		foreach ($iterator as $file)
		{
			if ($file->getExtension() === 'yml')
			{
				$relativePath = str_replace($path . DIRECTORY_SEPARATOR, '', $file->getRealPath());
				if ($excludeCache && strpos($relativePath, 'cache' . DIRECTORY_SEPARATOR) === 0)
				{
					continue;
				}
				$files[] = array($relativePath);
			}
		}
	}

	return $files;
}

/**
 * 遞迴提取所有 img 欄位值
 * Recursively extract all img field values from parsed YAML data
 *
 * 支援巢狀結構（如 Job YAML 的 gender.1.img, gender.2.img）
 * 和深層路徑（如 Union YAML 的 data_ex.img）。
 *
 * Supports nested structures (e.g., Job YAML gender.1.img, gender.2.img)
 * and deep paths (e.g., Union YAML data_ex.img).
 *
 * @param mixed $data - 解析後的 YAML 資料 / Parsed YAML data
 * @return array 所有 img 欄位的值 / All img field values
 */
function _resource_extract_img_fields($data)
{
	$imgs = array();
	if (!is_array($data))
	{
		return $imgs;
	}

	foreach ($data as $key => $value)
	{
		if ($key === 'img' && is_string($value))
		{
			$imgs[] = $value;
		}
		elseif (is_array($value))
		{
			$imgs = array_merge($imgs, _resource_extract_img_fields($value));
		}
	}

	return $imgs;
}

/**
 * 檢查圖片檔案是否存在於 static/image/ 下的子目錄
 * Check if an image file exists in a sub-directory of static/image/
 *
 * @param string $imgName 圖片名稱（不含副檔名）/ Image name without extension
 * @param string $subDir 相對於 static/image/ 的子目錄路徑 / Sub-directory relative to static/image/
 * @return bool
 */
function _resource_img_exists($imgName, $subDir)
{
	$extensions = array('png', 'gif', 'jpg', 'bmp');
	foreach ($extensions as $ext)
	{
		if (file_exists(PROJECT_STATIC_IMAGE_PATH . '/' . $subDir . $imgName . '.' . $ext))
		{
			return true;
		}
	}
	return false;
}

/**
 * 掃描所有 Resource 類型的 YAML，回傳缺少的圖片清單
 * Scan all Resource types and return missing image list
 *
 * 對每個 Resource 類型，載入所有 YAML 檔案，提取 img 欄位，
 * 檢查對應目錄下是否存在該檔案。
 *
 * For each Resource type, loads all YAML files, extracts img fields,
 * and checks if the file exists in the corresponding directory.
 *
 * @return array 缺少的圖片描述陣列 / Array of missing image descriptions
 */
function _resource_check_missing_images()
{
	$typeImgMapping = _resource_type_img_mapping();

	$missing = array();

	foreach ($typeImgMapping as $type => $imgSubDir)
	{
		$typeDir = PROJECT_GAME_DATA_RESOURCE_PATH . DIRECTORY_SEPARATOR . $type;
		$files = glob($typeDir . DIRECTORY_SEPARATOR . '*.yml');

		foreach ($files as $file)
		{
			$data = HOF_Class_Yaml::load($file);
			$imgValues = _resource_extract_img_fields($data);
			$relativeFile = basename($file);

			foreach ($imgValues as $img)
			{
				if (!_resource_img_exists($img, $imgSubDir))
				{
					$missing[] = "$type/$relativeFile: img='$img' (expected in static/image/$imgSubDir)";
				}
			}
		}
	}

	/** 檢查 Land 圖片：從 land.land 欄位生成 bg_{type} 和 land_{type} */
	$landDir = PROJECT_GAME_DATA_RESOURCE_PATH . DIRECTORY_SEPARATOR . 'Land';
	$landFiles = glob($landDir . DIRECTORY_SEPARATOR . '*.yml');
	$landPrefixes = array('bg_', 'land_');

	foreach ($landFiles as $file)
	{
		$data = HOF_Class_Yaml::load($file);
		$relativeFile = basename($file);

		if (isset($data['land']['land']))
		{
			$landType = $data['land']['land'];

			foreach ($landPrefixes as $prefix)
			{
				$imgName = $prefix . $landType;
				if (!_resource_img_exists($imgName, 'land/'))
				{
					$missing[] = "Land/$relativeFile: img='$imgName' (expected in static/image/land/)";
				}
			}
		}
	}

	return $missing;
}
