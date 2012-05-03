<?php

/**
 * このファイル名は変更してもOK
 * パスワードは変更してください。
 *
 * 管理しないなら
 * admin.php と adminフォルダを
 * サーバーから消してもOK
 *
 */

include ("trust_path/bootstrap.php");
define("ADMIN_DIR", BASE_TRUST_PATH."./admin/"); //管理用のファイル場所
define("ADMIN_PASSWORD", "password"); //パスワード

include_once CLASS_DIR . 'class.core.php';

include (ADMIN_DIR . "admin.php");


?>