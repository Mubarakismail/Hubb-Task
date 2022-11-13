<?php

function CallAPI($method, $url)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return json_decode($result, true);
}

function collectAllData($times)
{
    $data = [];
    for ($i = 1; $i <= $times; $i++) {
        $callResult = CallAPI('GET', 'https://jsonmock.hackerrank.com/api/medical_records?page=' . $i);
        $data = array_merge($data, $callResult['data']);
    }
    return $data;
}

$APIResult = CallAPI('GET', 'https://jsonmock.hackerrank.com/api/medical_records');

// if U need search in first page replace variable after = sign 
// to use all pages just keep code as it is 
/*  to search only in first page  
 change (collectAllData($APIResult['total_pages'])) to ($APIResult['data']) */
$allPagesResult = $APIResult['data'];

$doctorName = $_POST['doctorName'];
$diagnosis = $_POST['diagnosis'];

$mn = 1000;
$mx = 0;
foreach ($allPagesResult as $row) {
    if ($row['doctor']['name'] == $doctorName && $row['diagnosis']['id'] == $diagnosis) {
        $mx = max($mx, $row['vitals']['bodyTemperature']);
        $mn = min($mn, $row['vitals']['bodyTemperature']);
    }
}

echo json_encode([$mn, $mx]);
