<?php
require './ProviderInterface.php';

class GenericProvider implements ProviderInterface
{
    private $provider = null;

    public function __construct($providerName, $providerConf)
    {
        $confs = yaml_parse_file('./providers.yml');
        $this->provider = $confs[$providerName];
        $this->provider['name'] = $providerName;
        $this->provider['client_id'] = $providerConf['client_id'];
        $this->provider['client_secret'] = $providerConf['client_secret'];
    }

    public function getProviderName()
    {
        return $this->provider['name'];
    }

    public function getAuthorizationUrl($state)
    {
        $queryString = 'client_id='.$this->provider['client_id'];
        $queryString .= '&redirect_uri='.$this->provider['redirect_uri'];
        $queryString .= '&scope='.urldecode($this->provider['scope']);
        $queryString .= '&response_type='.$this->provider['response_type'];
        $queryString .= '&state='.$state;
        return $this->provider['authorization_url'].$queryString;
    }

    public function getBaseAccessTokenUrl()
    {
        return $this->provider['access_token_url'];
    }

    public function getAccessTokenUrl()
    {
        $code = $_GET['code'];
        $accessTokenUrlArray[] = $this->provider['access_token_url'];
        $accessTokenUrlArray[]=['client_id'=> $this->provider['client_id'],
            'redirect_uri'=>$this->provider['redirect_uri'],
            'client_secret'=>$this->provider['client_secret'],
            'code'=>$code,
            'grant_type'=>'authorization_code'];
        return $accessTokenUrlArray;

    }

    public function getUserInfos()
    {
        $queryString = 'access_token='. $_SESSION['access_token'];
        $response = (array) json_decode(file_get_contents($this->provider['ressources_owner_url'] . $queryString));
        var_dump($response);
        $result = [
            'firstname' => $response[$this->provider['mapping']['firstname']],
            'lastname' => $response[$this->provider['mapping']['lastname']],
            'email' => $response[$this->provider['mapping']['email']],
        ];

        return $result;
    }

    public function getProvider()
    {
        return $this->provider;
    }
}
