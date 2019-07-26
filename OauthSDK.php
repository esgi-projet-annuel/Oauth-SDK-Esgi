<?php
require './GenericProvider.php';

class OauthSDK {

    /**
     *@var GenericProvider[]
     */
    private $providers= [];

    public function __construct(array $credentials){
        foreach ($credentials as $provider => $confs) {
            $this->providers[] = new GenericProvider($provider, $confs);
        }
    }

    public function loadProvider(){

    }

    public function addProvider(){

    }

    public function getConnectionsLinks(){
        $connectionsLinks = [];
        foreach ($this->providers as $provider) {
            $state = $this->generateState($provider->getProviderName());
            $connectionsLinks[$provider->getProviderName()] = $provider->getAuthorizationUrl($state);
        }
        return $connectionsLinks;
    }

    public function getAccessTokenUrl($providerName){
        foreach ($this->providers as $provider) {
            if ($provider->getProviderName() === $providerName) {
                return $provider->getAccessTokenUrl();
            }
        }
    }

    public function getUserInfos($providerName){
        foreach ($this->providers as $provider) {
            if ($provider->getProviderName() === $providerName)
            {
                return $provider->getUserInfos();
            }
        }
    }

    public function generateState($providerName)
    {
        $state = uniqid($providerName.'_state');
        $_SESSION[$providerName.'_state'] = $state;
        return $state;
    }
}