<?php
$name = $_POST['name'];
$last_name = $_POST['last_name'];
$phone = $_POST['phone'];

$name = htmlspecialchars($name);
$last_name = htmlspecialchars($last_name);
$phone = htmlspecialchars($phone);

$name = urldecode($name);
$last_name = urldecode($last_name);
$phone = urldecode($phone);

$name = trim($name);
$last_name = trim($last_name);
$phone = trim($phone);
$staticBitrix ="вебхук";

function request($site, $method, $params)
{
    $queryUrl = $site . $method;
    $queryData = http_build_query($params);
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER => 0, 
        CURLOPT_POST => 1, 
        CURLOPT_HEADER => 0, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_URL => $queryUrl, 
        CURLOPT_POSTFIELDS => $queryData,
    ));
    $res = json_decode(curl_exec($curl), true);
    curl_close($curl);
    return $res;
}
$method = 'crm.contact.add';
$params = [
    'fields' => [
        'NAME' => $name,
        'LAST_NAME' => $last_name,
        'PHONE' => [['VALUE' => $phone], ['VALUE_TYPE' => 'WORK']]
    ],
    'params' => [
        'REGISTER_SONET_EVENT' => "Y"
    ]
];
$req = request($staticBitrix, $method, $params);
$contact = $req['result'];

$method = 'crm.lead.add';
$params = [
    'fields' => [
        'CONTACT_ID' => $contact
    ]
];
$req = request($staticBitrix, $method, $params);
$lead = $req['result'];

$method = 'crm.deal.add';
$params = [
    'fields' => [
        'TITLE' => "Внеплановая продажа",
        'CONTACT_ID' => $contact,
        'LEAD_ID'=> $lead
    ]
];
$req = request($staticBitrix, $method, $params);

$deal = $req['result'];
var_dump($req['result']);

$method = 'crm.contact.list';
$params = [
    'filter'=>
    [
        'NAME'=>"Иван"
    ],
    'select'=>['ID']
];
$arr_contacts = [];
$req = request($staticBitrix, $method, $params);
foreach ($req['result'] as $item) {
    $arr_contacts[] = $item['ID'];
}
echo '<pre>';
var_dump($arr_contacts);
echo '</pre>';

$method='crm.deal.update';
$params = [
    'ID'=> $deal,
    'fields'=>[
        'CONTACT_IDS'=> $arr_contacts
    ],
         'params'=> [
             "REGISTER_SONET_EVENT" => "Y"
            ]
];
$req = request($staticBitrix, $method, $params);
var_dump($req['result']);
