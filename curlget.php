<?php
//初始化

$ch = curl_init();
//設定選項，包括URL

// curl_setopt($ch, CURLOPT_URL, "https://od.cdc.gov.tw/eic/Weekly_Age_County_Gender_19CoV.csv");
curl_setopt($ch, CURLOPT_URL, "https://od.cdc.gov.tw/eic/Weekly_Age_County_Gender_19CoV.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);

//執行並獲取HTML文件內容
$output = curl_exec($ch);

//釋放curl控制代碼
curl_close($ch);

//列印獲得的資料
// echo "<script>console.log(" . $output . ")</script>";

//去除縣市為空值及2021年之前筆數
$remove_empty = [];
foreach (json_decode($output) as $key => $val) {
    // var_dump($val->{'縣市'},"<br>");
    if ($val->{'縣市'} != "空值" && $val->{'發病年份'} > 2020) $remove_empty[] = $val;
}
// print_r($remove_empty);

// 分別處理各縣市按週別加總之發病人數
// $new_array = json_encode([
//     'city'=>'',
//     'county'=>'',
//     'year'=>'',
//     'week'=>0,
//     'number'=>0
// ]);
$new_array=[];

foreach ($remove_empty as $k => $v) {
    // var_dump($v);
    // $vFlag = $v['縣市'].$v['鄉鎮'].$v['發病年份'].$v['發病週別'];
    $vFlag = $v->{'縣市'} . $v->{'鄉鎮'} . $v->{'發病年份'} . $v->{'發病週別'};

    if (isset($new_array[$vFlag])) {
        // $new_array[$vFlag]['number'] += $v['確定病例數'];
        $new_array['number'] += $v['確定病例數'];
        // $new_array['city'] = $v->{'縣市'};
        // $new_array['county'] = $v->{'鄉鎮'};
        // $new_array['year'] = $v->{'發病年份'};
        // $new_array['week'] = $v->{'發病週別'};
        // $new_array['number'] = $v->{'確定病例數'};
    } else {
        // $new_array->{'city'} = $v->{'縣市'};
        // $new_array->{'county'} = $v->{'鄉鎮'};
        // $new_array->{'year'} = $v->{'發病年份'};
        // $new_array->{'week'} = $v->{'發病週別'};
        // $new_array->{'number'} = $v->{'確定病例數'};
        $new_array['city'] = $v->{'縣市'};
        $new_array['county'] = $v->{'鄉鎮'};
        $new_array['year'] = $v->{'發病年份'};
        $new_array['week'] = $v->{'發病週別'};
        $new_array['number'] = $v->{'確定病例數'};
    }
}
var_dump($new_array);
// print_r(array_values($new_array));

// $strOUT = json_encode($arrOUT);
// echo $strOUT;

//將資料存入json檔，若json已存在，先刪除再存

$covid = 'covid.json';
if (file_exists($covid)) unlink($covid); //若已存在，則將檔案刪除

$open = fopen($covid, 'w+');
fwrite($open, json_encode($remove_empty));
fclose($open);
// echo "file updated";
// echo "<script>console.log(" . json_encode($remove_empty) . ")</script>";
