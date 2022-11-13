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

function validateInput($input)
{
    $doctorName = "";
    $diagnosis = 0;
    $data = [];
    if (!isset($input['doctorName']) || $input['doctorName'] == '') {
        $data['errors'] = true;
        $data['message'] = "doctor name is required";
        return $data;
    } else {
        $doctorName = $input['doctorName'];
    }
    if (!isset($input['diagnosis']) || !is_numeric($input['diagnosis'])) {
        $data['errors'] = true;
        $data['message'] = "diagnosis is required and must be numeric";
        return $data;
    } else {
        $diagnosis = $input['diagnosis'];
    }
    return ['errors' => false, 'doctorName' => $doctorName, 'diagnosis' => $diagnosis];
}

$APIResult = CallAPI('GET', 'https://jsonmock.hackerrank.com/api/medical_records');

// if U need search in first page replace variable after = sign 
// to use all pages just keep code as it is 
/*  to search only in first page  
 change (collectAllData($APIResult['total_pages'])) to ($APIResult['data']) */
$allPagesResult = $APIResult['data'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = validateInput($_POST);
    if ($data['errors'] == true) {
        http_response_code(400);
        echo json_encode($data);
        exit;
    }
    $doctorName = $data['doctorName'];
    $diagnosis = $data['diagnosis'];
    $mn = 1000;
    $mx = 0;
    foreach ($allPagesResult as $row) {
        if ($row['doctor']['name'] == $doctorName && $row['diagnosis']['id'] == $diagnosis) {
            $mx = max($mx, $row['vitals']['bodyTemperature']);
            $mn = min($mn, $row['vitals']['bodyTemperature']);
        }
    }
    echo json_encode([$mn, $mx]);
} else {
    http_response_code(405);
    echo json_encode(["message" => "Send method as POST"]);
}
