<?php

/**
 * @author bluelovers
 * @copyright 2012
 */

//include_once (CLASS_RANKING);

/**
 * 処理手順(ランキング戦)
 * 1. 挑戦者のIDを渡す
 * 2.
 * 1位の人。
 * 戦闘できませんエラー。
 * 2-最下位の人。
 * 1個上の人を探す。
 * ランク外の人。
 * 最下位の人を探す。
 * 3. 自分の相手と戦闘
 * 4. 勝利者、敗者の順位変動
 * 5. 保存。
 * ----------------------------
 * エラー怖いよ、怖いよー
 * 起こりうる全ての(?)事象。
 * ◎|1位が居ない時(ランク自体が無いとき)挑戦者が1位になる。
 * ◎|1位は挑戦できない。
 * ◎|正常な2位-最下位の者が上に挑戦して勝つ。
 * ◎|正常な2位-最下位の者が上に挑戦して負ける。
 * ◎|正常な2位-最下位の者が上に挑戦して1位になる。
 * △|チーム登録されて無い者は挑戦できない。
 * ○|チーム登録はしたけど、ランキングに参加してない者が挑戦する。
 * ◎|挑戦した相手のチームがおかしい(数名欠けている)。
 * ◎|挑戦した相手のチームがおかしい(全員欠けている)。
 * ○|挑戦した相手のID自体が消えている。
 * ○|IDを消したときランキングからも消滅する。
 * △|時間制限がある場合は挑戦できない。
 * ◎|相手が時間制限中(→たぶん無関係).
 */
class HOF_Class_Ranking extends HOF_Class_Base
{
    const RANKING = RANKING;

    const RANK_MAX = 3;

    /**
     * 相手が既に存在していませんでした(不戦勝)
     * 受けた側のIDが存在しない.
     */
    const DEFENDER_NO_ID = 'DEFENDER_NO_ID';
    /**
     * 挑戦側PT無し
     * 戦うメンバーがいません。
     */
    const CHALLENGER_NO_PARTY = 'CHALLENGER_NO_PARTY';
    /**
     * 受けた側PT無し
     * $Rival->name は対戦キャラが設定されていませんでした<br />(不戦勝).
     */
    const DEFENDER_NO_PARTY = 'DEFENDER_NO_PARTY';
    /**
     * 挑戦者勝ち
     */
    const CHALLENGER_WIN = 'CHALLENGER_WIN';
    /**
     * 受けた側勝ち
     */
    const DEFENDER_WIN = 'DEFENDER_WIN';
    /**
     * 引分け.
     */
    const DRAW_GAME = 'DRAW_GAME';

    const RESULT_TRUE = true;
    const RESULT_FALSE = false;
    const RESULT_BATTLE = 'BATTLE';

    /**
     * ランキングのチーム設定できる周期
     * 60 * 60 * 48.
     */
    const RANK_TEAM_SET_TIME = RANK_TEAM_SET_TIME;

    /**
     * ランキング戦 負けたとき次戦えるまで
     * 60 * 60 * 24.
     */
    const RANK_BATTLE_NEXT_LOSE = RANK_BATTLE_NEXT_LOSE;

    /**
     * ランキング戦 勝ったとき次戦えるまで
     * 60 * 1.
     */
    const RANK_BATTLE_NEXT_WIN = RANK_BATTLE_NEXT_WIN;

    const PLACE_PUSH = 2;
    const PLACE_BACK = -1;
    const PLACE_EX = 1;

    /**
     * Ranking.
     *
     * ファイルから読み込んでランキングを配列にする
     * $this->content->data[0][0]= *********;// 首位
     *
     * $this->content->data[1][0]= *********;// 同一 2位
     * $this->content->data[1][1]= *********;
     *
     * $this->content->data[2][0]= *********;// 同一 3位
     * $this->content->data[2][1]= *********;
     * $this->content->data[2][2]= *********;
     *
     * $this->content->data[3][0]= *********;// 同一 4位
     * $this->content->data[3][1]= *********;
     * $this->content->data[3][2]= *********;
     * $this->content->data[3][3]= *********;
     *
     * ...........
     *
     * @var array
     */
    public $data = [];

    public $content = [];

    public $UserName;
    public $UserRecord;

    public $file = RANKING;

    public $DataType = 'yml';

    public function _fpopen($over, $file)
    {
        /*
        debug($this, $over, $file);

        $this->file = $file . '.yml';
        $path = $file . '.dat';
        */

        if (0 && !file_exists($this->file) && file_exists($path)) {
            $this->fp = HOF_Class_File::fplock_file($path);

            $fp = fopen($this->file, 'w+');

            $this->_fpread_dat();

            HOF_Class_Yaml::save($fp, ['data' => $this->content['data']]);

            $this->content['data'] = [];

            $this->fpclose();

            $this->fp = $fp;

            return $this->fp;
        }
    }

    public function _fpread()
    {
        unset($this->content->data, $this->content);

        $this->content = HOF_Class_Yaml::load($this->fp);

        $this->content = new HOF_Class_Array($this->content);

        if (empty($this->content->data)) {
            $this->content->data = [];
        }

        $this->data = &$this->content->data;

        if (empty($this->content->data)) {
            return false;
        }

        //error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
    }

    public function _fpread_dat()
    {
        $this->content['data'] = [];

        $Place = 0;
        while ($line = fgets($this->fp)) {
            $line = trim($line);
            if ('' == $line) {
                continue;
            }
            if (count($this->content['data'][$Place]) === $this->SamePlaceAmount($Place)) {
                $Place++;
            }
            $this->content['data'][$Place][] = $line;
        }

        //$this->content->data	= file($this->file);

        // 区切って文字列を分割
        foreach ($this->content['data'] as $Rank => $SamePlaces) {
            if (!is_array($SamePlaces)) {
                continue;
            }
            foreach ($SamePlaces as $key => $val) {
                $list = explode('<>', $val);
                $this->content['data'][$Rank][$key] = [];
                $this->content['data'][$Rank][$key]['id'] = $list['0'];
            }
        }
    }

    public function _sortRank()
    {
        ksort($this->content->data);
        $this->content->data = array_values($this->content->data);

        for ($i = 0; $i < self::RANK_MAX; ++$i) {
            $next = $i + 1;

            if (empty($this->content->data[$i])) {
                $this->content->data[$i] = [];
            }

            if (empty($this->content->data[$next])) {
                $this->content->data[$next] = [];
            }

            $max = $this->SamePlaceAmount($i);

            $next_idx = $next;

            while (count($this->content->data[$next_idx]) > 0 && (count($this->content->data[$i]) < $max)) {
                $data = array_shift($this->content->data[$next]);

                if (!empty($data)) {
                    array_push($this->content->data[$i], $data);
                }

                if (empty($this->content->data[$next_idx])) {
                    --$next_idx;
                }
            }

            $next_idx = $next;

            while (count($this->content->data[$i]) > $max) {
                $data = array_pop($this->content->data[$i]);

                if (!empty($data)) {
                    array_unshift($this->content->data[$next], $data);
                }
            }
        }

        $this->content->data = array_filter($this->content->data);
        $this->content->data = array_slice($this->content->data, 0, self::RANK_MAX, true);

        $this->content->data = array_values($this->content->data);

        $this->data = &$this->content->data;

        $this->content['data.id'] = [];

        foreach ($this->content->data as $r => $v) {
            ksort($this->content->data[$r]);
            $this->content->data[$r] = array_values($this->content->data[$r]);

            foreach ($v as $k => $data) {
                $this->content['data.id'][$data['id']] = ['rank' => $r, 'key' => $k];
            }
        }
    }

    public function _fpsave()
    {
        //		error_reporting(E_ALL);

        $this->content->info['update_last'] = (int) $this->content->info['update'];
        $this->content->info['update'] = time();

        if ($this->_rank_update) {
            $this->content->info['change_last'] = (int) $this->content->info['change'];
            $this->content->info['change'] = time();
        }

        $this->content->join_newid = $this->join_newid;

        $this->_sortRank();

        $this->content->rankPlaceAmount = $this->rankPlaceAmount;

        //$dump = $this->content->toArray();
        $dump = $this->content;

        $this->content->ksort();

        //		var_dump($dump);
        //		debug($dump);
        //
        //		error_reporting(0);
        //
        //		exit();

        HOF_Class_Yaml::save($this->fp, $dump);
    }

    /**
     * ランキングを保存する.
     */
    public function _fpsave_dat()
    {
        foreach ($this->content->data as $rank => $val) {
            foreach ($val as $key => $val2) {
                $ranking .= $val2['id'] . "\n";
            }
        }

        HOF_Class_File::fpwrite_file($this->fp, $ranking);
    }

    public function _fpinit()
    {
        $this->content = new HOF_Class_Array(['data' => []], 0, 1);

        if (!$this->fpopen(true, true)) {
            return false;
        }

        $this->fpread();

        // 配列が0なら終了
        if (!$this->content->data) {
            return false;
        }

        //$this->JoinRanking("yqyqqq","last");
        //$this->dump($this->content->data);

        return $this;
    }

    /**
     * ランキング戦する。戦う。
     *
     * @param mixed $user
     */
    public function Challenge(&$user)
    {
        // ランキングが無いとき(1位になる)
        if (empty($this->content->data)) {
            $this->JoinRanking($user->id, $user);
            $this->fpsave();

            return [-1, 'Rank starts.'];
        }

        //自分の順位
        $MyRank = $this->SearchID($user->id);

        // 1位の場合。
        if (0 === $MyRank[0]) {
            return [false, "First place can't challenge."];
        }

        // お互いのランキンぐ用のパーティーを読み込む
        $Party_Challenger = $user->RankParty();

        // ランク用パーティーがありません！！！
        if (!is_array($Party_Challenger) || empty($Party_Challenger)) {
            return [false, '戦うメンバーがいません。'];
        }

        if (!$MyRank) {
            $this->JoinRanking($user->id, $user);
        }

        $MyRank = $this->SearchID($user->id);

        if ($MyRank) {
            list($RivalPlace, $RivalRankKey, $RivalID) = $this->searchPrev($MyRank[0], $MyRank[1], $user->id);

            if ($RivalID && $RivalRankKey >= ($MyRank[0] - 1)) {
                $DefendMatch = true;
            } else {
                $DefendMatch = false;
            }

            $Rival = new HOF_Class_User($RivalID);
            $Result = $this->RankBattle($user, $Rival, $MyRank[0], $RivalPlace);

            $Return = $this->ProcessByResult($Result, $user, $Rival, $DefendMatch);

            $message = null;

            if ($this->place_change_result) {
                switch ($this->place_change_result) {
                    case self::PLACE_BACK:
                        $message = '順位減少';
                        break;
                    case self::PLACE_PUSH:
                        $message = '順位変更';
                        break;
                    case self::PLACE_EX:
                        $message = '順位交換';
                        break;
                }
            }

            return [$Return, $message];
        }
    }

    /**
     * 戦わせる.
     *
     * @param mixed $user
     * @param mixed $Rival
     * @param mixed $UserPlace
     * @param mixed $RivalPlace
     */
    public function RankBattle(&$user, &$Rival, $UserPlace, $RivalPlace)
    {
        $UserPlace = '[' . ($UserPlace + 1) . '位]';
        $RivalPlace = '[' . ($RivalPlace + 1) . '位]';

        /*
         * ■ 相手のユーザ自体が既に存在しない場合の処理
         * アカウントが削除処理された時にランキングからも消えるようにしたから
         * 本来出ないエラーかもしれない。
         */
        if (!$Rival->is_exist()) {
            HOF_Helper_Global::ShowError('相手が既に存在していませんでした(不戦勝)');

            $this->DeleteRank($Rival->id);
            $this->fpsave(1);

            return self::DEFENDER_NO_ID;
        }

        // お互いのランキンぐ用のパーティーを読み込む
        $Party_Challenger = $user->RankParty();
        $Party_Defender = $Rival->RankParty();

        // ランク用パーティーがありません！！！
        if (!is_array($Party_Challenger) || empty($Party_Challenger)) {
            HOF_Helper_Global::ShowError('戦うメンバーがいません。');

            return self::CHALLENGER_NO_PARTY;
        }

        // ランク用パーティーがありません！！！
        if (false === $Party_Defender) {
            //$defender->RankRecord(0,"DEFEND",$DefendMatch);
            //$defender->SaveData();
            HOF_Helper_Global::ShowError($Rival->name . ' は対戦キャラが設定されていませんでした<br />(不戦勝)');

            return self::DEFENDER_NO_PARTY; //不戦勝とする
        }

        //$this->dump($Party_Challenger);
        //$this->dump($Party_Defender);

        $battle = new HOF_Class_Battle($Party_Challenger, $Party_Defender);
        $battle->SetBackGround('colosseum');
        $battle->SetResultType(1); // 決着つかない場合は生存者の数で決めるようにする
        $battle->SetTeamName($user->name . $UserPlace, $Rival->name . $RivalPlace);
        $battle->Process(); //戦闘開始
        $battle->RecordLog('RANK');
        $Result = $battle->ReturnBattleResult(); // 戦闘結果

        // 戦闘を受けて立った側の成績はここで変える。
        //$defender->RankRecord($Result,"DEFEND",$DefendMatch);
        //$defender->SaveData();

        //return array("Battle",$Result);
        if (TEAM_0 === $Result) {
            return self::CHALLENGER_WIN;
        }
        if (TEAM_1 === $Result) {
            return self::DEFENDER_WIN;
        }
        if (BATTLE_DRAW === $Result) {
            return self::DRAW_GAME;
        }

        return self::DRAW_GAME; //(エラー)予定では出ないエラー(回避用)
    }

    /**
     * 結果によって処理を変える.
     *
     * @param mixed $Result
     * @param mixed $user
     * @param mixed $Rival
     * @param mixed $DefendMatch
     */
    public function ProcessByResult($Result, &$user, &$Rival, $DefendMatch)
    {
        switch ($Result) {
                // 受けた側のIDが存在しない
            case self::DEFENDER_NO_ID:
                $this->place_change_result = $this->ChangePlace($user->id, $Rival->id);
                $this->DeleteRank($Rival->id);
                $this->fpsave();

                return self::RESULT_FALSE;
                break;

                // 挑戦側PT無し
            case self::CHALLENGER_NO_PARTY:
                return self::RESULT_FALSE;
                break;

                // 受けた側PT無し
            case self::DEFENDER_NO_PARTY:
                $this->place_change_result = $this->ChangePlace($user->id, $Rival->id);

                $this->fpsave();
                //$user->RankRecord(0,"CHALLENGER",$DefendMatch);
                $user->SetRankBattleTime(time() + HOF_Class_Ranking::RANK_BATTLE_NEXT_WIN);
                $Rival->RankRecord(0, 'DEFEND', $DefendMatch);
                $Rival->SaveData();

                return self::RESULT_TRUE;
                break;

                // 挑戦者勝ち
            case self::CHALLENGER_WIN:
                $this->place_change_result = $this->ChangePlace($user->id, $Rival->id);
                $this->fpsave();
                $user->RankRecord(0, 'CHALLENGER', $DefendMatch);
                $user->SetRankBattleTime(time() + HOF_Class_Ranking::RANK_BATTLE_NEXT_WIN);
                $Rival->RankRecord(0, 'DEFEND', $DefendMatch);
                $Rival->SaveData();

                return self::RESULT_BATTLE;
                break;

                // 受けた側勝ち
            case self::DEFENDER_WIN:
                //$this->fpsave();

                $this->place_change_result = $this->ChangePlace($user->id, $Rival->id, self::PLACE_BACK);

                $user->RankRecord(1, 'CHALLENGER', $DefendMatch);
                $user->SetRankBattleTime(time() + HOF_Class_Ranking::RANK_BATTLE_NEXT_LOSE);
                $Rival->RankRecord(1, 'DEFEND', $DefendMatch);
                $Rival->SaveData();

                $this->fpsave();

                return self::RESULT_BATTLE;
                break;

                // 引分け
            case self::DRAW_GAME:
                //$this->fpsave();
                $user->RankRecord('d', 'CHALLENGER', $DefendMatch);
                $user->SetRankBattleTime(time() + HOF_Class_Ranking::RANK_BATTLE_NEXT_LOSE);
                $Rival->RankRecord('d', 'DEFEND', $DefendMatch);
                $Rival->SaveData();

                return self::RESULT_BATTLE;
                break;
            default:
                return true;
                break;
        }
    }

    /**
     * 引数の順位 と 同じ順位の人数.
     *
     * @param mixed $Place
     */
    public function SamePlaceAmount($Place)
    {
        if (!isset($this->rankPlaceAmount[$Place])) {
            $i = HOF_Helper_Math::minmax(floor(($Place + 1) * 1.5), 1, self::RANK_MAX * 2);

            $this->rankPlaceAmount[$Place] = $i;
        }

        return $this->rankPlaceAmount[$Place];
    }

    /**
     * ランキングの最下位に参加させる.
     *
     * @param mixed $id
     */
    public function JoinRanking($id)
    {
        $this->_rank_update = true;

        $data = [];
        $data['id'] = $id;

        $last = count($this->content->data) - 1;

        if (empty($this->content->data[$last])) {
            $this->content->data[$last] = [];
        }

        array_push($this->content->data[$last], $data);

        $this->join_newid[] = $data;
    }

    /**
     * ランキングから消す.
     *
     * @param mixed $id
     */
    public function DeleteRank($id)
    {
        $this->_rank_update = true;

        $place = $this->SearchID($id);

        if (false === $place) {
            // 削除失敗
            return false;
        }

        unset($this->content->data[$place[0]][$place[1]]);

        return true; //削除成功
    }

    /**
     * 順位を入れ替える.
     *
     * @param mixed $id_0
     * @param mixed $id_1
     * @param mixed $mode
     */
    public function ChangePlace($id_0, $id_1, $mode = self::PLACE_PUSH)
    {
        $this->_rank_update = true;

        $data = $rank = $key = [];

        list($rank[0], $key[0]) = $this->SearchID($id_0);
        list($rank[1], $key[1]) = $this->SearchID($id_1);

        $data[0] = $this->content->data[$rank[0]][$key[0]];
        $data[1] = $this->content->data[$rank[1]][$key[1]];

        if (self::PLACE_BACK == $mode) {
            if (isset($this->content->data[$rank[0]][$key[0] + 1])) {
                $this->content->data[$rank[0]][$key[0]] = $this->content->data[$rank[0]][$key[0] + 1];
                $this->content->data[$rank[0]][$key[0] + 1] = $data[0];

                return self::PLACE_BACK;
            }
            if ($rank[0] > (self::RANK_MAX - 1)) {
                unset($this->content->data[$rank[0]][$key[0]]);

                if (empty($this->content->data[$rank[0] + 1])) {
                    $this->content->data[$rank[0] + 1] = [];
                }

                array_unshift($this->content->data[$rank[0] + 1], $data[0]);

                return self::PLACE_BACK;
            }
        } elseif (($rank[0] > $rank[1]) || ($rank[0] == $rank[1] && $key[0] > $key[1])) {
            $this->content->data[$rank[1]][$key[1]] = $data[0];

            if (self::PLACE_PUSH == $mode && $rank[0] != (self::RANK_MAX - 1)) {
                unset($this->content->data[$rank[0]][$key[0]]);

                array_push($this->content->data[$rank[1]], $data[1]);

                return self::PLACE_PUSH;
            }

            $this->content->data[$rank[0]][$key[0]] = $data[1];

            return self::PLACE_EX;
        }

        return false;
    }

    public function searchPrev($rank, $key, $randid = null)
    {
        do {
            if ($key > 0) {
                --$key;

                $RivalID = $this->content->data[$rank][$key]['id'];
            } else {
                --$rank;

                $key = count($this->content->data[$rank]);
            }
        } while (!$RivalID && ($rank >= 0));

        if ($RivalID) {
            if ($randid) {
                do {
                    $key = array_rand($this->content->data[$rank]);
                    $RivalID = $this->content->data[$rank][$key]['id'];
                } while ($RivalID === $randid);

                if ($RivalID === $randid || !$RivalID) {
                    return false;
                }
            }

            $ret = [
                $rank,
                $key,
                $RivalID, ];
        } else {
            $ret = false;
        }

        return $ret;
    }

    /**
     * $id のランク位置を探す.
     *
     * @param mixed $id
     */
    public function SearchID($id)
    {
        if (!$this->_rank_update && !empty($this->content['data.id'][$id])) {
            $cache = $this->content['data.id'][$id];

            return [$cache['rank'], $cache['key']];
        }

        foreach ((array) $this->content->data as $rank => $val) {
            foreach ((array) $val as $key => $val2) {
                // 順位無いの何番目か。
                if ($val2['id'] == $id) {
                    $this->content['data.id'][$id] = ['rank' => (int) $rank, 'rank' => (int) $key];

                    return [(int) $rank, (int) $key];
                }
            }
        }

        return false;
    }

    /**
     * ランキングの表示.
     *
     * @param mixed $from
     * @param mixed $to
     * @param mixed $bold_id
     */
    public function ShowRanking($from = false, $to = false, $bold_id = false)
    {
        // 範囲が無い場合は全ランキングを表示
        if (false === $from or false === $to) {
            $from = 0; //首位
            $to = count($this->content->data); //最下位
        }

        // 太字にするランク
        if ($bold_id) {
            $BoldRank = $this->SearchID($bold_id);
        }

        $LastPlace = count($this->content->data) - 1; // 最下位

        echo "<table cellspacing=\"0\">\n";
        echo "<tr><td class=\"td6\" style=\"text-align:center\">順位</td><td  class=\"td6\" style=\"text-align:center\">チーム</td></tr>\n";
        for ($Place = $from; $Place < $to + 1; ++$Place) {
            if (!$this->content->data["{$Place}"]) {
                break;
            }
            echo "<tr><td class=\"td7\" valign=\"middle\" style=\"text-align:center\">\n";
            // 順位アイコン
            switch ($Place) {
                case 0:
                    print '<img src="' . HOF_Class_Icon::getImageUrl('crown01', HOF_Class_Icon::IMG_ICON) . '" class="vcent" />';
                    break;
                case 1:
                    print '<img src="' . HOF_Class_Icon::getImageUrl('crown02', HOF_Class_Icon::IMG_ICON) . '" class="vcent" />';
                    break;
                case 2:
                    print '<img src="' . HOF_Class_Icon::getImageUrl('crown03', HOF_Class_Icon::IMG_ICON) . '" class="vcent" />';
                    break;
                default:
                    if ($Place == $LastPlace) {
                        print '底';
                    } else {
                        print($Place + 1) . '位';
                    }
            }
            echo "</td><td class=\"td8\">\n";
            foreach ($this->content->data["{$Place}"] as $SubRank => $data) {
                list($Name, $R) = $this->LoadUserName($data['id'], true); //成績も読み込む
                $WinProb = $R[all] ? sprintf('%0.0f', ($R[win] / $R[all]) * 100) : '--';
                $Record = '(' . ($R[all] ? $R[all] : '0') . '戦 ' . ($R[win] ? $R[win] : '0') . '勝 ' . ($R[lose] ? $R[lose] : '0') . '敗 ' . ($R[all] - $R[win] - $R[lose]) . '引 ' . ($R[defend] ? $R[defend] : '0') . '防 ' . '勝率' . $WinProb . '%' . ')';
                if (isset($BoldRank) && $BoldRank['0'] == $Place && $BoldRank['1'] == $SubRank) {
                    echo '<span class="bold u">' . $Name . "</span> {$Record}";
                } else {
                    echo $Name . ' ' . $Record;
                }
                echo "<br />\n";
            }
            echo "</td></tr>\n";
        }
        echo "</table>\n";
    }

    /**
     * ±ランク 対象ID.
     *
     * @param mixed $id
     * @param mixed $Amount
     */
    public function ShowRankingRange($id, $Amount)
    {
        $RankAmount = count($this->content->data);
        $Last = $RankAmount - 1;
        do {
            // ランキングがAmount以上ないとき
            if ($RankAmount <= $Amount) {
                $start = 0;
                $end = $Last;
                break;
            }

            $Rank = $this->SearchID($id);
            if (false === $Rank) {
                echo 'ランキング不明';

                return 0;
            }
            $Range = floor($Amount / 2);

            // 首位に近いか首位
            if (($Rank[0] - $Range) <= 0) {
                $start = 0;
                $end = $Amount - 1;
            // 最下位にちかいか最下位
            } else {
                if ($Last < ($Rank[0] + $Range)) {
                    $start = $RankAmount - $Amount;
                    $end = $RankAmount;
                // 範囲内におさまる
                } else {
                    $start = $Rank[0] - $Range;
                    $end = $Rank[0] + $Range;
                }
            }
        } while (0);

        $this->ShowRanking($start, $end, $id);
    }

    /**
     * ユーザの名前を呼び出す.
     *
     * @param mixed $id
     * @param mixed $rank
     */
    public function LoadUserName($id, $rank = false)
    {
        if (!$this->UserName["{$id}"]) {
            $User = new HOF_Class_User($id);
            $Name = $User->Name();
            $Record = $User->RankRecordLoad();
            if (false !== $Name) {
                $this->UserName["{$id}"] = $Name;
                $this->UserRecord["{$id}"] = $Record;
            } else {
                $this->UserName["{$id}"] = '-';

                $this->DeleteRank($id);

                $this->fpsave(true);
            }
        }

        if ($rank) {
            return [$this->UserName["{$id}"], $this->UserRecord["{$id}"]];
        }

        return $this->UserName["{$id}"];
    }
}
