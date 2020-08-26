<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_USER);

//class HOF_Class_User extends user
class HOF_Class_User
{
    /**
     * ファイルポインタ
     */
    public $fp;
    public $file;

    public $id;
    public $pass;
    public $name;
    public $last;
    public $login;
    public $start;
    public $money;
    public $char;
    public $time;

    /**
     * 総消費時間.
     */
    public $wtime;

    /**
     * IPアドレス.
     */
    public $ip;

    public $party_memo;

    /**
     * ランキング用のパーティ.
     */
    public $party_rank;

    /**
     * ランキングPT設定した時間.
     */
    public $rank_set_time;

    /**
     * 次のランク戦に挑戦できる時間.
     */
    public $rank_btl_time;
    /**
     * ランキングの成績
     * = "総戦闘回数<>勝利数<>敗北数<>引き分け<>首位防衛";.
     */
    public $rank_record;

    /**
     * 次のUnion戦に挑戦できる時間.
     */
    public $union_btl_time;

    /**
     * OPTION.
     */
    /*
    var $record_btl_log;
    var $no_JS_itemlist;
    var $UserColor;
    */

    /**
     * ユーザーアイテム用の変数.
     */
    public $fp_item;

    protected static $instance_user;
    //var $item;

    protected $_user_cache_;

    /**
     * 対象のIDのユーザークラスを作成.
     *
     * @param mixed $id
     * @param mixed $noExit
     */
    public function __construct($id, $noExit = false)
    {
        if ((string) $id) {
            $this->id = (string) $id;

            if ($data = $this->LoadData($noExit)) {
                $this->DataUpDate($data); //timeとか増やす
                $this->SetData($data);
            }

            self::$instance_user[$this->id] = &$this;
        }
    }

    public function __destruct()
    {
        if ($this->id) {
            $this->cache()->__destruct();

            $this->cache()->__destruct();
        }

        $this->fpclose_all();

        self::$instance_user[$this->id] = null;
    }

    public function __toString()
    {
        return (string) $this->id;
    }

    public static function getInstance($id = null, $noExit = false)
    {
        if ((string) $id === HOF::user()->id) {
            return HOF::user();
        }

        if (isset(self::$instance_user[(string) $id])) {
            return self::$instance_user[(string) $id];
        }

        return new HOF_Class_User($id, $noExit);
    }

    public function &cache()
    {
        if (!$this->id) {
            return false;
        }

        if (!isset($this->_user_cache_)) {
            $this->_user_cache_ = new HOF_Class_File_Cache([
                'path' => BASE_PATH_CACHE . 'user/' . $this->id . '/',
                'timeout' => 3600,
            ]);
        }

        return $this->_user_cache_;
    }

    /**
     * 時間を経過させる。(Timeの増加).
     *
     * @param mixed $data
     */
    public function DataUpDate(&$data)
    {
        $now = time();
        $diff = $now - $data['timestamp']['last'];
        $data['timestamp']['last'] = $now;
        $gain = $diff / (24 * 60 * 60) * TIME_GAIN_DAY;
        $data['time'] += (int) $gain;
        if (MAX_TIME < $data['time']) {
            $data['time'] = MAX_TIME;
        }
    }

    /**
     * ランキング戦用のパーティ編成を返す.
     */
    public function RankParty()
    {
        if ($this->is_exist() && !empty($this->party_rank)) {
            $party = [];

            foreach ($this->party_rank as $no) {
                $char = $this->char($no);
                if ($char) {
                    $party[] = $char;
                }
            }

            if (!empty($party)) {
                return $party;
            }
        }

        return false;
    }

    /**
     * IDが結局のところ存在しているかたしかめる.
     */
    public function is_exist()
    {
        return (!$this->id || !isset($this->name) || empty($this->name)) ? false : true;
    }

    public function char_list($over = null)
    {
        if (!$over && $list = $this->cache()->data('char_list')) {
            return $list;
        }

        $list = [];

        if ($list_char = HOF_Helper_Char::char_list_by_user($this)) {
            foreach ($list_char as $no => $file) {
                /*
                $char = HOF_Model_Char::newCharFromFile($file);
                */
                $char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, $no, null, $this, $this);

                $list[$no] = $char->name;
            }
        }

        $this->cache()->data('char_list', $list);

        $this->cache()->save('char_list');

        return $list;
    }

    /**
     * 全所持キャラクターをファイルから読んで $this->char に格納.
     */
    public function char_all()
    {
        //配列の初期化だけしておく
        $this->char = [];

        if ($list_char = $this->char_list()) {
            foreach (array_keys($list_char) as $no) {
                $file = HOF_Helper_Char::char_file($no, $this->id);

                if (!file_exists($file)) {
                    continue;
                }

                /*
                $this->char[$no] = HOF_Model_Char::newCharFromFile($file);

                $this->char[$no]->SetUser($this->id);
                */

                $char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, $no, null, $this, $this);

                if (!$char || is_string($char)) {
                    continue;
                }

                $this->char[$no] = $char;
            }
        }

        return $this->char;
    }

    /**
     * 指定の所持キャラクターをファイルから読んで $this->char に格納後 "返す"。
     *
     * @param mixed $CharNo
     */
    public function char($CharNo)
    {
        if ($this->char[$CharNo]) {
            return $this->char[$CharNo];
        }

        $file = HOF_Helper_Char::char_file($CharNo, $this);

        if (!file_exists($file)) {
            return false;
        }

        /*
        $this->char[$CharNo] = HOF_Model_Char::newCharFromFile($file);

        $this->char[$CharNo]->SetUser($this->id);
        */
        $char = HOF_Class_Char::factory(HOF_Class_Char::TYPE_CHAR, $CharNo, null, $this, $this);

        if (!$char || is_string($char)) {
            return false;
        }

        $this->char[$CharNo] = $char;

        $list = $this->cache()->data('char_list');

        $list[$CharNo] = $this->char[$CharNo]->name;

        $this->cache()->data('char_list', $list);

        return $this->char[$CharNo];
    }

    public function &__get($k)
    {
        if ('item' == $k) {
            return $this->{$k}();
        }
    }

    /*
    function __isset($k)
    {
        return isset($this->$k);
    }
    */

    /**
     * アイテムデータを読む
     *
     * @param mixed|null $no
     */
    public function &item($no = null)
    {
        /*
         * 2重に読むのを防止。
         */
        if (!isset($this->item) || true === $no) {
            $file = HOF_Helper_Char::user_file($this, USER_ITEM);

            $this->fp_item = HOF_Class_File::fplock_file($file, true, true);

            $this->item = HOF_Class_Yaml::load($this->fp_item);

            $this->item = (array) $this->item;
        }

        if (null !== $no && true !== $no && false !== $no) {
            return $this->item[$no];
        }

        return $this->item;
    }

    /**
     * アイテムデータを保存する.
     */
    public function item_save()
    {
        if (!isset($this->item)) {
            return false;
        }

        $dir = HOF_Helper_Char::user_path($this);

        if (!is_dir($dir)) {
            return false;
        }

        // アイテムのソート
        ksort($this->item, SORT_STRING);

        foreach ($this->item = array_filter($this->item) as $k => $v) {
            if (!$k || !$v) {
                unset($this->item[$k]);
            }
        }

        $file = HOF_Helper_Char::user_file($this, USER_ITEM);
        HOF_Class_Yaml::save($this->fp_item ? $this->fp_item : $file, (array) $this->item);
        unset($this->fp_item);
    }

    /**
     * ユーザデータを読む
     *
     * @param mixed $noExit
     */
    public function LoadData($noExit = false)
    {
        $file = HOF_Helper_Char::user_file($this, USER_DATA);

        if (file_exists($file)) {
            $this->cache();

            $this->file = $file;

            $this->fp = HOF_Class_File::fplock_file($file, $noExit);
            if (!$this->fp) {
                return false;
            }

            return HOF_Class_Yaml::load($this->fp);
        }

        return false;
    }

    /**
     * データを保存する.
     */
    public function SaveData()
    {
        $this->cache()->__destruct();

        if (file_exists($this->file) && $this->fp) {
            HOF_Class_File::fpwrite_file($this->fp, $this->DataSavingFormat());

            fclose($this->fp);
            unset($this->fp);
        } else {
            $file = HOF_Helper_Char::user_file($this, USER_DATA);

            if (file_exists($file)) {
                HOF_Class_File::WriteFile($file, $this->DataSavingFormat());
            }
        }
    }

    /**
     * データを保存する形式に変換する。(テキスト).
     */
    public function DataSavingFormat()
    {
        $Save = [
            'uniqid',

            'id',
            'pass',
            'ip',
            'name',

            /*
            "last",
            "login",
            "start",
            */
            'timestamp',
            'options',

            'money',
            'time',
            'wtime',
            'party_memo',
            'party_rank',
            'rank_set_time',
            'rank_btl_time',
            'rank_record',
            'union_btl_time',
            /*
            "record_btl_log",
            "no_JS_itemlist",
            "UserColor",
            */
        ];

        $data = [];

        foreach ($Save as $k) {
            if (!isset($this->{$k})) {
                continue;
            }

            $data[$k] = $this->{$k};
        }

        return HOF_Class_Yaml::dump($data);
    }

    /**
     * データをセットする。
     * ※?
     *
     * @param mixed $data
     */
    public function SetData(&$data)
    {
        foreach ($data as $key => $val) {
            $this->{$key} = $val;
        }

        /*
        if (!is_array($this->party_memo))
        {
            $this->party_memo = explode("<>", $this->party_memo);
        }

        if (!is_array($this->party_rank))
        {
            $this->party_rank = explode("<>", $this->party_rank);
        }
        */

        /*
        $this->name	= $data["name"];
        $this->login	= $data["login"];
        $this->last	= $data["last"];
        $this->start	= $data["start"];
        */
    }

    /**
     * ランキングの成績
     * side = ("CHALLENGE","DEFEND").
     *
     * @param mixed $result
     * @param mixed $side
     * @param mixed $DefendMatch
     */
    public function RankRecord($result, $side, $DefendMatch)
    {
        $record = $this->RankRecordLoad();

        ++$record['all'];
        switch (true) {
                // 引き分け
                /*
                case ($result === "d"):
                if($side != "CHALLENGE" && $DefendMatch)
                $record["defend"]++;
                break;
                */
                // 戦闘結果が挑戦者の勝ち
            case 0 === $result:
                if ('CHALLENGER' == $side) {
                    ++$record['win'];
                } else {
                    ++$record['lose'];
                }
                break;
                // 戦闘結果が挑戦者の負け
            case 1 === $result:
                if ('CHALLENGER' == $side) {
                    ++$record['lose'];
                } else {
                    ++$record['win'];
                    if ($DefendMatch) {
                        $record['defend']++;
                    }
                }
                break;
            default: // 引き分け
                if ('CHALLENGER' != $side && $DefendMatch) {
                    $record['defend']++;
                }
                break;
        }

        /*
        $this->rank_record = $record["all"] . "|" . $record["win"] . "|" . $record["lose"] . "|" . $record["defend"];
        */
        $this->rank_record = $record;
    }

    /**
     * ランキング戦の成績を呼び出す.
     */
    public function RankRecordLoad()
    {
        if (!$this->rank_record) {
            return [
                'all' => 0,
                'win' => 0,
                'lose' => 0,
                'defend' => 0,
            ];
        }

        return $this->rank_record;
    }

    /**
     * キャラデータを消す.
     *
     * @param mixed $no
     */
    public function char_delete($no)
    {
        $file = HOF_Helper_Char::char_file($no, $this->id);

        if ($this->char[$no]) {
            $this->char[$no]->fpclose();
        }

        if (file_exists($file)) {
            HOF_Class_File::unlink($file);
        }

        $this->cache()->timeout('char_list', -1);
    }

    /**
     * キャラクターを所持してる数をかぞえる。
     */
    public function char_count()
    {
        $list_char = $this->char_list(true);

        return count($list_char);
    }

    /**
     * データファイル兼キャラファイルのファイルポインタも全部閉じる.
     */
    public function fpclose_all()
    {
        // 基本データ
        HOF_Class_File::fpclose($this->fp);
        unset($this->fp);

        // アイテムデータ
        HOF_Class_File::fpclose($this->fp_item);
        unset($this->fp_item);

        // キャラデータ

        foreach ((array) $this->char as $key => $var) {
            if (method_exists($this->char[$key], 'fpclose')) {
                $this->char[$key]->fpclose();
            }
        }
    }

    /**
     * ユーザーの削除(全ファイル).
     *
     * @param mixed $DeleteFromRank
     */
    public function DeleteUser($DeleteFromRank = true)
    {
        //ランキングからまず消す。
        if ($DeleteFromRank) {
            $Ranking = new HOF_Class_Ranking();
            if ($Ranking->DeleteRank($this->id)) {
                $Ranking->fpsave(1);
            }
        }

        $this->fpclose_all();

        $dir = HOF_Helper_Char::user_path($this);

        HOF_Model_Main::addUserDelList($this->id, $this->name);

        HOF_Class_File::rmdir($dir, true);

        /*
        $files = glob($dir.'*');

        foreach ($files as $val)
        {
        unlink($val);
        }

        rmdir($dir);
        */
    }

    /**
     * 名前を変える。
     *
     * @param mixed $new
     */
    public function ChangeName($new)
    {
        if ($this->name == $new) {
            return false;
        }

        $this->name = $new;

        HOF_Model_Main::addUserList($this->id, $new);

        return true;
    }

    /**
     * 放棄されているかどうか確かめる.
     */
    public function IsAbandoned()
    {
        $now = time();
        // $this->login がおかしければ終了する。
        if (10 !== strlen($this->timestamp['login'])) {
            return false;
        }
        if (($this->timestamp['login'] + ABANDONED) < $now) {
            return true;
        }

        return false;
    }

    /**
     * IPを変更.
     *
     * @param mixed $ip
     */
    public function SetIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * 名前を返す.
     *
     * @param mixed $opt
     */
    public function Name($opt = false)
    {
        if ($this->name) {
            if ($opt) {
                return '<span class="' . $opt . '">' . $this->name . '</span>';
            }

            return $this->name;
        }

        return false;
    }

    /**
     * Union戦闘した時間をセット.
     */
    public function UnionSetTime()
    {
        $this->union_btl_time = time();
    }

    /**
     * UnionBattleができるかどうか確認する。
     */
    public function CanUnionBattle()
    {
        $Now = time();
        $Past = $this->union_btl_time + UNION_BATTLE_NEXT;
        if ($Past <= $Now) {
            return true;
        }

        return abs($Now - $Past);
    }

    /**
     * 次のランク戦に挑戦できる時間を記録する。
     *
     * @param mixed $time
     */
    public function SetRankBattleTime($time)
    {
        $this->rank_btl_time = $time;
    }

    /**
     * ランキング挑戦できるか？(無理なら残り時間を返す).
     */
    public function CanRankBattle()
    {
        $now = time();
        if ($this->rank_btl_time <= $now) {
            return true;
        }

        if (!$this->rank_btl_time) {
            return true;
        }

        $left = $this->rank_btl_time - $now;
        $hour = floor($left / 3600);
        $minutes = floor(($left % 3600) / 60);
        $seconds = floor(($left % 3600) % 60);

        return [
            $hour,
            $minutes,
            $seconds, ];
    }

    /**
     * お金を増やす.
     *
     * @param mixed $no
     */
    public function getMoney($no)
    {
        $this->money += $no;
    }

    /**
     * お金を減らす.
     *
     * @param mixed $no
     */
    public function TakeMoney($no)
    {
        if ($this->money < $no) {
            return false;
        }

        $this->money -= $no;

        return true;
    }

    /**
     * 時間を消費する(総消費時間の加算).
     *
     * @param mixed $time
     */
    public function WasteTime($time)
    {
        if ($this->time < $time) {
            return false;
        }
        $this->time -= $time;
        $this->wtime += $time;

        return true;
    }

    /**
     * アイテムを追加.
     *
     * @param mixed $no
     * @param mixed $amount
     */
    public function item_add($no, $amount = false)
    {
        if ($amount) {
            $this->item[$no] += $amount;
        } else {
            $this->item[$no]++;
        }
    }

    /**
     * アイテムを削除.
     *
     * @param mixed $no
     * @param mixed $amount
     */
    public function item_remove($no, $amount = false)
    {
        // 減らす数。
        if ($this->item[$no] < $amount) {
            $amount = $this->item[$no];
            if (!$amount) {
                $amount = 0;
            }
        }
        if (!is_numeric($amount)) {
            $amount = 1;
        }

        // 減らす。
        $this->item[$no] -= $amount;
        if ($this->item[$no] < 1) {
            unset($this->item[$no]);
        }

        return $amount;
    }
}
