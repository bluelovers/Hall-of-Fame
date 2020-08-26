<?php

/**
 * @author bluelovers
 * @copyright 2012
 *
 * @see 01-Templating-In-Five-Minutes.markdown
 */

require_once './Bootstrap.php';

/**
 * 裝載機和模板的邏輯名稱

當調用render（）的方法，發動機的第一次嘗試加載模板。的render（）方法的第一個參數是模板的邏輯名稱。
模板的邏輯名稱是獨立使用的模板加載器。這就是為什麼我們稱之為“邏輯”模板名稱（對比的“物理”模板名稱）。
隨著文件系統加載程序，名稱是可能的完整路徑的一部分。
但是如果你使用的數據庫模板裝載機，名稱可以是模板實例記錄的主鍵。可以是任何模板裝載機是能夠理解的邏輯名稱。

組件捆綁在一起，與幾個模板裝載機和 sfTemplateLoaderFilesystem是一個使用文件系統上存儲的模板：

裝載機的構造函數作為其第一個參數的路徑模式。 ％名稱％佔位符在運行時被替換模板的邏輯名稱。
*/
$loader = new sfTemplateLoaderFilesystem(dirname(__FILE__).'/templates/%name%.php');

$engine = new sfTemplateEngine($loader);

/**
 * 呈現一個模板，sfTemplateEngine首先需要加載它。
 * 默認情況下，模板引擎，模板存儲，以及如何使它們沒有假設。
 * 模板可以存儲在數據庫中的文件系統，在內存中，或任何你想。
 * 和模板可以在PHP，PHPTAL，Smarty，或任何其他自定義的模板語言。
 */
echo $engine->render('index', array('name' => 'Fabien'));