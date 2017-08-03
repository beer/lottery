<?php
require_once (__DIR__ . '/init.inc.php');
$v = new Pix_Partial();
$numbers = Number::search(1)->order('id DESC')->limit(10);
$type = isset($_POST['type']) ? $_POST['type'] : '';
$number_id = isset($_POST['number_id']) ? $_POST['number_id'] : '';
$take = isset($_POST['take']) ? $_POST['take'] : array();
$next = isset($_POST['next']) ? intval($_POST['next']) : '';
$n1 = isset($_POST['n1']) ? $_POST['n1'] : '';
$n2 = isset($_POST['n2']) ? $_POST['n2'] : '';
$n3 = isset($_POST['n3']) ? $_POST['n3'] : '';

$nums = array_values(array_filter(array($n1, $n2, $n3)));
?>

<!DOCTYPE html>                                                                                                                                                                                                                                                                                                     
<html lang="zh">
<head>
<meta charset="utf-8" />
<link rel="stylesheet" href="/main.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">
$(function() {
});
</script>
</head>
<body>
<form method="post">
    <table border="0">
    <tr>
        <td>
        <input type="radio" name="type" value="num" <?= $type == 'num' ? 'checked' : ''; ?>>數字：
        <select name="n1">
            <option value="0">---</option>
<?php
            for ($i = 1; $i < 40 ; $i++) {
?>
            <option value="<?=$i?>" <?= $n1 == $i ? 'selected="selected"' : ''; ?>><?=$i?></option>
<?php       } ?>
        </select>
        <select name="n2">
            <option value="0">---</option>
<?php
            for ($i = 1; $i < 40 ; $i++) {
?>
            <option value="<?=$i?>" <?= $n2 == $i ? 'selected="selected"' : ''; ?>><?=$i?></option>
<?php       } ?>
        </select>
        <select name="n3">
            <option value="0">---</option>
<?php
            for ($i = 1; $i < 40 ; $i++) {
?>
            <option value="<?=$i?>" <?= $n3 == $i ? 'selected="selected"' : ''; ?>><?=$i?></option>
<?php       } ?>
        </select>
        </td>
        <td rowspan="2">
        下<select name="next">
<?php
            for ($i = 0; $i < 11 ; $i++) {
?>
            <option value="<?=$i?>" <?= $next == $i ? 'selected="selected"' : ''; ?>><?=$i?></option>
<?php       } ?>
        </select>期
        <input type="submit" value="搜尋"/>
        </td>
    </tr>
    <tr>
        <td>
        <input type="radio" name="type" value="id" <?= $type == 'id' ? 'checked' : ''; ?>>期號：
        <select name="number_id">
<?php
            foreach ($numbers as $n) {
?>
            <option value="<?=$n->id?>" <?= $number_id == $n->id ? 'selected="selected"' : ''; ?>><?=$n->id . '/' . date('Y-m-d', $n->date);?></option>
<?php       } ?>
        </select>
        <input type="checkbox" name="take[]" value="3"  <?= in_array(3, $take) ? 'checked' : ''; ?>>三支拖牌
        <input type="checkbox" name="take[]" value="2"  <?= in_array(2, $take) ? 'checked' : ''; ?>>二支拖牌
        </br>
        </td>
    </tr>
    </table>
</form>
<div id="result">
<?php
                if ($type == 'id') {

                    if (!empty($take)) {
                        $number = Number::search("id = {$number_id}")->first();
                        if (in_array(2, $take)) {
                            $arrs = $number->searchTwo();
                            foreach ($arrs as $arr) {
                                $matches = Number::searchTwo($arr[0], $arr[1], $next);
                                echo $v->partial('result.php', array('matches' => $matches, 'nums' => $arr));
                            }
                        }
                        if (in_array(3, $take)) {
                            $arrs = $number->searchThree();
                            foreach ($arrs as $arr) {
                                $matches = Number::searchThree($arr[0], $arr[1], $arr[2], $next);
                                echo $v->partial('result.php', array('matches' => $matches, 'nums' => $arr));
                            }
                        }
                    } else {
                        echo '請選擇拖牌數';
                    }

                }
                if ($type == 'num') {
                    if (count($nums) > 1) {
                        if (count($nums) == 2) {
                            $matches = Number::searchTwo($nums[0], $nums[1], $next);
                        } else {
                            $matches = Number::searchThree($nums[0], $nums[1], $nums[2], $next);
                        }
                    }
                    if (count($matches) and count($nums) > 1) {
                        echo $v->partial('result.php', array('matches' => $matches, 'nums' => $nums));
                    } else {
                        echo '查無資料';
                    }
                }
?>
<table>
</div>
</body>
</html>
