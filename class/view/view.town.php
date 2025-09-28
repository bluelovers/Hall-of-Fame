<?php
/**
 * 處理與城鎮相關的視圖。
 */

// 包含城鎮數據文件
include_once(DATA_TOWN);

/**
 * 顯示城鎮的佈局和可用選項。
 *
 * @param object $self 主類對象的引用。
 */
function TownShow(&$self) {
    print('<div style="margin:15px">' . "\n");
    print("<h4>街</h4>");
    print('<div class="town">' . "\n");
    print("<ul>\n");
    $PlaceList = TownAppear($self);
    // 店
    if ($PlaceList["Shop"]) {
?>
        <li>店(Shop)
            <ul>
                <li><a href="?menu=buy">買(Buy)</a></li>
                <li><a href="?menu=sell">賣(Sell)</a></li>
                <li><a href="?menu=work">打工</a></li>
            </ul>
        </li>
<?php
    }
    // 斡旋所
    if ($PlaceList["Recruit"])
        print("<li><p><a href=\"?recruit\">人材斡旋所(Recruit)</a></p></li>");
    // 鍛冶屋
    if ($PlaceList["Smithy"]) {
?>
        <li>鍛冶屋(Smithy)
            <ul>
                <li><a href="?menu=refine">精煉工房(Refine)</a></li>
                <li><a href="?menu=create">製作工房(Create)</a></li>
            </ul>
        </li>
<?php
    }
    // オ一クション會場
    if ($PlaceList["Auction"] && AUCTION_TOGGLE)
        print("<li><a href=\"?menu=auction\">拍賣會場(Auction)</li>");
    // コロシアム
    if ($PlaceList["Colosseum"])
        print("<li><a href=\"?menu=rank\">競技場(Colosseum)</a></li>");
    print("</ul>\n");
    print("</div>\n");
    print("<h4>廣場</h4>");
    TownBBS($self);
    print("</div>\n");
}

/**
 * 處理和顯示城鎮的留言板（BBS）。
 *
 * @param object $self 主類對象的引用。
 * @return bool
 */
function TownBBS(&$self) {
    $file = BBS_TOWN;
?>
    <form action="?town" method="post">
        <input type="text" maxlength="60" name="message" class="text" style="width:300px" />
        <input type="submit" value="post" class="btn" style="width:100px" />
    </form>
<?php
    if (!file_exists($file))
        return false;
    $log = file($file);
    if ($_POST["message"] && strlen($_POST["message"]) < 121) {
        $_POST["message"] = htmlspecialchars($_POST["message"], ENT_QUOTES);
        $_POST["message"] = stripslashes($_POST["message"]);

        $name = "<span class=\"bold\">{$self->name}</span>";
        $message = $name . " > " . $_POST["message"];
        if ($self->UserColor)
            $message = "<span style=\"color:{$self->UserColor}\">" . $message . "</span>";
        $message .= " <span class=\"light\">(" . date("c") . ")</span>\n";
        array_unshift($log, $message);
        while (50 < count($log))
            array_pop($log);
        WriteFile($file, implode(null, $log));
    }
    foreach ($log as $mes)
        print(nl2br($mes));
    return true;
}

