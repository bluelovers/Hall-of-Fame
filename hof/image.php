<?php

/**
 * 画像合成を非常にナンセンスな方法で行う。
 * GDライブラリ→画像の水平反転が不可能。
 * PECL ImageMagic→可能。しかしPEARの知識が無く断念。

 * 従って画像合成する場合,反転済みの画像を別で用意する。

 * sampleURL
 * http://localhost/proj/hof/image.php?f11=mon_018&f12=mon_018&f13=mon_018&f14=mon_018&b11=mon_018&b12=mon_018&f21=mon_018&f22=mon_018&b21=mon_018&b22=mon_018&b23=mon_018&f23=mon_018&f24=mon_018&info=0
 * 最後の[&info=0] は無くてもok

 * ※※※ 魔法陣の表示に未対応！！！！！！！！！
 */

include ('trust_path/bootstrap.php');

//$type = 'gif';
$type = 'png';

$img = new HOF_Class_Battle_Style_Image($type);

$img->SetBackGround();
$img->SetCharFile();
$img->ShowInfo();
$img->CopyChar();
$img->Filter();
$img->OutPutImage();

?>