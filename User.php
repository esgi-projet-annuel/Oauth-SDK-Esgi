<?php
require './BaseSQL.php';
class User extends BaseSQL{

    public $firstname;
    public $lastname;
    public $email;
    public $provider;

    public function __construct(){
        parent::__construct();
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
    }




}