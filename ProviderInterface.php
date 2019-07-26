<?php

interface ProviderInterface
{
    public function getProviderName();

    public function getAuthorizationUrl($state);

    public function getAccessTokenUrl();

    public function getUserInfos();

    public function getProvider();
}