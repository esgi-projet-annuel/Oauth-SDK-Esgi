<?php
session_start();
require './OauthSDK.php';
require './User.php';
$credentials = yaml_parse_file('./credentials.yml');

$sdk = new OauthSDK($credentials);

function getLinks($sdk){
    $str= "";
    foreach ($sdk->getConnectionsLinks() as $providerName => $link) {
        $str.= "<p><a href=\"{$link}\"> connect with {$providerName} </a></p>";
    }
}

function registerApp($sdk){
    $state = $_GET['state'];
    // state verification
    print($_GET['code']);
    if (!($state === $_SESSION['state']))
        die("State has been modified!");

    $stateExploded = explode('_', $state);
    $providerName= $stateExploded[0];
    $accessTokenUrlArray = $sdk->getAccessTokenUrl($providerName);
    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($accessTokenUrlArray[1])
        )
    );
    $context  = stream_context_create($options);
    $response = file_get_contents($accessTokenUrlArray[0], false, $context);
    $obj_response = json_decode($response);
    $access_token = $obj_response->access_token;
    $_SESSION['access_token'] = $access_token;
    $userInfos = $sdk->getUserInfos($providerName);
    var_dump($userInfos);
    saveUser($userInfos, $providerName);
}

function saveUser($userInfos, $providerName){
  $user = new User();
  $user->setFirstname($userInfos['firstname']);
  $user->setLastname($userInfos['lastname']);
  $user->setEmail($userInfos['email']);
  $user->setProvider($providerName);
  $user->save();
}


$path = strtok($_SERVER['REQUEST_URI'], '?');
switch($path) {
    case '/':
        getLinks($sdk);
        break;
    case '/register':
        registerApp($sdk);
        break;
}