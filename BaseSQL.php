<?php
require './conf.inc.php';
class BaseSQL{

    public $id = null;
    public $created_at;
    private $table;
    private $pdo;

    public function __construct(){
        try{
            $this->pdo = new PDO(DBDRIVER.":host=".DBHOST.";dbname=".DBNAME.";port=".DBPORT,DBUSER,DBPWD);
        }catch(Exception $e){
            die("Erreur SQL : ".$e->getMessage());
        }
        $this->table = get_called_class();
    }

    public function setId( $id){
        $this->id = $id;
    }

    public function save(){
        $reflect = new ReflectionClass($this);
        $properties = $reflect->getProperties();
        $propertiesValue = [];
        foreach ($properties as $property) {
            if($property->name != "id") {
                if($property->isPrivate() || $property->isProtected()) {
                    $property->setAccessible(true);
                }
                $key= $property->getName();
                $value= $property->getValue($this);
                $propertiesValue[$key]= $value;
            }
        }

        if($this->id == null){
            //INSERT
            //array_keys($properties) -> [id, firstname, lastname, email]
            $sql ="INSERT INTO ".$this->table." ( ".
                implode(",", array_keys($propertiesValue) ) .") VALUES ( :".
                implode(",:", array_keys($propertiesValue) ) .")";

            $query = $this->pdo->prepare($sql);
            $query->execute($propertiesValue);
            $this->id = $this->pdo->lastInsertId();

        } else {
            //UPDATE
            $sqlUpdate = [];
            foreach ($propertiesValue as $key => $value) {
                if( $key != "id")
                    $sqlUpdate[]=$key."=:".$key;
            }
            $sql ="UPDATE ".$this->table." SET ".implode(",", $sqlUpdate)." WHERE id=:id";
            $query = $this->pdo->prepare($sql);
            $propertiesValue['id'] = $this->id;
            $query->execute($propertiesValue);

        }
    }
}