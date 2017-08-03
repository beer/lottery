#!/usr/bin/php
<?php
require_once (__DIR__ . '/init.inc.php');
require_once (__DIR__ . '/extsrc/simple_html_dom.php');

$url = 'http://www.taiwanlottery.com.tw/lotto/DailyCash/history.aspx';

$last_number = Number::search(1)->order('date DESC')->first();
$last_number_day = date('j', $last_number->date);
$last_number_week = date('N', $last_number->date);

if (($last_number_day == 31 or $last_number_day == 30) and $last_number_week == 6) {
    $id = (date('Y', $last_number->date) - 1911) . '000001';
} else {
    $id = $last_number->id + 1;
}

list($curl, $fields) = init_curl($url, $id);
// post
$post_response = post_curl($curl, $fields);
$pageHtml = str_get_html($post_response);

$tds = $pageHtml->find('.table_org td[class=td_w]');
if (!count($tds)) {
    echo $id . '找不到資料' . PHP_EOL;
    exit;
}

$check_id = $tds[0]->find('span', 0)->innertext;

// 如何 cookie 失效，就再抓一次, 抓3次失敗就停止
$i = 0;
while($i < 3) {
    if ($check_id == $id) {
        break;
    } else {
        list($curl, $fields) = init_curl($url, $id);
        $post_response = post_curl($curl, $fields);
        $tds = $pageHtml->find('.table_org td[class=td_w]');
        $check_id = $tds[0]->find('span', 0)->innertext;
    }
    if ($i == 2) {
        echo 'Cookie 抓三次失效';
        exit;
    }
    $i++;
}

// 日期轉換 民國轉西元
$day = $tds[1]->find('span span', 0)->innertext;
$pattern = '/^(\d+)/';
$day = preg_replace_callback($pattern, function ($match) { return $match[1] + 1911; }, $day);
$day = strtotime($day);
$row = array(
    'id' => $id,
    'date' => $day,
    'n1' => $tds[3]->find('span', 0)->innertext,
    'n2' => $tds[4]->find('span', 0)->innertext,
    'n3' => $tds[5]->find('span', 0)->innertext,
    'n4' => $tds[6]->find('span', 0)->innertext,
    'n5' => $tds[7]->find('span', 0)->innertext,
    'o1' => $tds[10]->find('span', 0)->innertext,
    'o2' => $tds[11]->find('span', 0)->innertext,
    'o3' => $tds[12]->find('span', 0)->innertext,
    'o4' => $tds[13]->find('span', 0)->innertext,
    'o5' => $tds[14]->find('span', 0)->innertext,
);

Number::insert($row);
echo $id . PHP_EOL; 

function init_curl( $url, $id = '' ){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_COOKIEFILE, "");
    $response = curl_exec($curl);

    $pageHtml = str_get_html($response);

    $trs = $pageHtml->find('#form1 input');

    $fields = array();

    foreach ($trs as $t) {
        if ($t->type == 'hidden') {
            $fields[] = urlencode($t->name) . '=' . urlencode($t->value);
        }
    }
    // 以上先戳一次抓hidden 的cookie 資訊, 下面再重送一次包含要查的id

    $fields[] = 'D539Control_history1%24DropDownList1=5';
    $fields[] = 'D539Control_history1%24chk=radNO';
    //$fields[] = 'Lotto649Control_history%24txtNO=106000047';
    $fields[] = 'D539Control_history1%24btnSubmit=%E6%9F%A5%E8%A9%A2';
    $fields[] = 'D539Control_history1%24txtNO=' . $id;
    //print_r($fields);
    //return array('curl' => $curl, 'fields' => $fields);
    return array($curl,$fields);

}

function post_curl($curl, $fields) {
    //curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, implode('&', $fields));
    return curl_exec($curl);
}

//echo $response;
exit;
