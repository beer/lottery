<?php
class NumberRow extends Pix_Table_Row
{
    public function postInsert()
    {
        // C 5 取 3
        $this->analyzeThree(true);
        // C 5 取 2
        $this->analyzeTwo(true);
    }

    public function analyzeThree($insert = false)
    {
        $result = array();
        $numbers = array(
            $this->o1,
            $this->o2,
            $this->o3,
            $this->o4,
            $this->o5
        );

        $used = array(0, 0, 0, 0, 0);
        // C 5 取 3
        foreach ( $numbers as $k1 => $v1) {
            $used[$k1] = $v1;
            foreach ( $numbers as $k2 => $v2) {
                if ($used[$k2] or ($k2 < $k1)) { continue; }
                $used[$k2] = $v2;
                foreach ( $numbers as $k3 => $v3) {
                    if ($used[$k3] or ($k3 < $k2) or ($k3 < $k1)) { continue; }
                    $used[$k3] = $v3;

                    array_push($result, array_values(array_filter($used)));

                    $used[$k3] = 0;
                }
                $used[$k2] = 0;
            }
            $used = array(0, 0, 0, 0, 0);
        }
        
        // 存入Table
        if ($insert) {
            foreach ($result as $res) {
                $row = FiveTakeThree::createRow();
                $row->number_id = $this->id;
                $row->o1 = $res[0];
                $row->o2 = $res[1];
                $row->o3 = $res[2];
                $row->save();
            }
        }
        return $result;
    }

    public function analyzeTwo($insert = false)
    {
        $result = array();
        $numbers = array(
            $this->o1,
            $this->o2,
            $this->o3,
            $this->o4,
            $this->o5
        );

        $used = array(0, 0, 0, 0, 0);
        // C 5 取 2
        foreach ( $numbers as $k1 => $v1) {
            $used[$k1] = $v1;
            foreach ( $numbers as $k2 => $v2) {
                if ($used[$k2] or ($k2 < $k1)) { continue; }
                $used[$k2] = $v2;
                array_push($result, array_values(array_filter($used)));
                $used[$k2] = 0;
            }
            $used = array(0, 0, 0, 0, 0);
        }

        // 存入Table
        if ($insert) {
            foreach ($result as $res) {
                $row = FiveTakeTwo::createRow();
                $row->number_id = $this->id;
                $row->o1 = $res[0];
                $row->o2 = $res[1];
                $row->save();
            }
        }
        return $result;
    }

    // 針對這組的數字C5取3找對應
    public function searchThree()
    {
        return $this->analyzeThree();
    }

    // 針對這組的數字C5取2找對應
    public function searchTwo()
    {
        return $this->analyzeTwo();
    }

    //抓下n期開獎資料
    public function next($period = 1)
    {
        // 計算跳過的不開獎的週日
        $pass_sundays = floor((date('N', $this->date) + $period) / 7);
        // 跨過的週日加完又遇到週日再加一天
        $today_sunday = (date('N', $this->date) + $period + $pass_sundays)%7 == 0 ? 1 : 0;
        
        $day = $this->date + 86400*($period + $pass_sundays + $today_sunday);
        $num = Number::search('date = ' . $day);

        return count($num) ? $num->first() : null ;
    }
}

class Number extends Pix_Table
{
    public function init()
    {
        $this->_name = 'numbers';
        $this->_rowClass = 'NumberRow';

        $this->_primary = 'id';

        $this->_columns['id'] = array('type' => 'int', 'size' => 11, 'unsigned' => true);
        $this->_columns['date'] = array('type' => 'int', 'size' => 10);
        $this->_columns['n1'] = array('type' => 'int', 'size' => 2);
        $this->_columns['n2'] = array('type' => 'int', 'size' => 2);
        $this->_columns['n3'] = array('type' => 'int', 'size' => 2);
        $this->_columns['n4'] = array('type' => 'int', 'size' => 2);
        $this->_columns['n5'] = array('type' => 'int', 'size' => 2);
        $this->_columns['o1'] = array('type' => 'int', 'size' => 2);
        $this->_columns['o2'] = array('type' => 'int', 'size' => 2);
        $this->_columns['o3'] = array('type' => 'int', 'size' => 2);
        $this->_columns['o4'] = array('type' => 'int', 'size' => 2);
        $this->_columns['o5'] = array('type' => 'int', 'size' => 2);

    }

    //針對全部組數字，給任二個數字找對應
    public static function searchTwo($n1, $n2, $next = null)
    {
        $two = array($n1, $n2);
        sort($two);
        $matches = FiveTakeTwo::search("`o1` = {$two[0]} AND `o2` = {$two[1]}");

        if (!is_null($next)) {
            return self::getNextArray($matches, $next);
        }
        return $matches;

    }

    //針對全部組數字，給任三個數字找對應
    public static function searchThree($n1, $n2, $n3, $next = null)
    {
        $three = array($n1, $n2, $n3);
        sort($three);
        $matches = FiveTakeThree::search("`o1` = {$three[0]} AND `o2` = {$three[1]} AND `o3` = {$three[2]}");

        if (!is_null($next)) {
            return self::getNextArray($matches, $next);
        }
        return $matches;
    }

    public static function getNextArray($matches, $next)
    {
        // 回傳下N期資料時，會遇到下N期沒資料，所以要先過瀘
        $nexts = array();
        foreach ($matches as $m) {
            $next_record = $m->number->next($next);
            if ($next_record) {
                array_push($nexts, $next_record);
            }
        }
        return $nexts;
    }
}
