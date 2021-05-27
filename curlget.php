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

//轉換csv檔案為json格式並輸出
// $array = array_map("str_getcsv", explode("\n", $output));
// echo json_encode($array);
// $json= json_encode($array);

//列印獲得的資料
// echo "<script>console.log(" . $output . ")</script>";

//去除縣市為空值及2021年之前筆數
$remove_empty=[];
foreach (json_decode($output) as $key => $val) {
    // var_dump($val->{'縣市'},"<br>");
    if($val->{'縣市'}!="空值" && $val->{'發病年份'}>2020) $remove_empty[]=$val;
}

//將資料存入json檔，若json已存在，先刪除再存

$covid = 'covid.json';
if (file_exists($covid)) unlink($covid); //若已存在，則將檔案刪除

$open = fopen($covid, 'w+');
fwrite($open, json_encode($remove_empty));
fclose($open);
// echo "file updated";
// echo "<script>console.log(" . json_encode($remove_empty) . ")</script>";

