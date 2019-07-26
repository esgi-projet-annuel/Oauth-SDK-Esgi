<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<h1 class="text-light bg-dark text-center">Connection</h1>
<?php
session_start();

require './OauthSDK.php';
require './User.php';
$credentials = yaml_parse_file('./credentials.yml');

$sdk = new OauthSDK($credentials);

function getLinks($sdk){
    $str= "<div class=\"d-flex align-items-start flex-column bd-highlight mb-3\">";
    foreach ($sdk->getConnectionsLinks() as $providerName => $link) {
        $str.= "<a href=\"{$link}\" class=\"btn btn-primary btn-lg btn-inline m-2 w-25\" role=\"button\" aria-pressed=\"true\"> connect with {$providerName} </a>";
    }
    $str .= "</div>";
    print $str;
}

function registerApp($sdk){
    $state = $_GET['state'];
    // state verification
    $stateExploded = explode('_', $state);
    $providerName= $stateExploded[0];
    if (!($state === $_SESSION[$providerName.'_state']))
        die("State has been modified!");

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