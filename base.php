<?php

class database {

const USER="root";
const PASS="root";
const HOST="localhost";
const PORT=8889;
const DB="qwq7qkanjok8p5l4";

private $connection;

private $statement;

public $error;

public $sql;

private function getConnection(){
if($_SERVER['SERVER_NAME']=="localhost"){
/*
$user=self::USER;
$pass=self::PASS;
$host=self::HOST;
$db=self::DB;
$port=self::PORT;
*/
$db="qwq7qkanjok8p5l4";
$host="xefi550t7t6tjn36.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
$user="yxbs25gff1e10jr7";
$pass="ig92rihcol4eudni";
$port=3306;
}else{
$db=getenv("JAWSDB_DB");
$host=getenv("JAWSDB_HOST");
$user=getenv("JAWSDB_USER");
$pass=getenv("JAWSDB_PASS");
$port=getenv("JAWSDB_PORT");
}
$connection=new PDO("mysql:dbname=$db;host=$host;port=$port",$user,$pass);
return $connection;
}

public function __construct(){
try{
$this->connection=$this->getConnection();
$this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
$this->connection->setAttribute(PDO::ATTR_ERRMODE,true);
$this->connection->setAttribute(PDO::ERRMODE_EXCEPTION,true);
}catch(PDOException $PDOexecption){
echo($PDOexecption->getMessage());
}
}

public function commando(array $params){
$this->executa($this->sql,$params);
}

public function commandoquery(){
$this->executaquery($this->sql);
}

private function executa($sql,$params){
try{
$this->statement=$this->connection->prepare($sql);
if(is_object($this->statement)){
$this->statement->execute($params);
}
}catch(PDOException $PDOException){
$this->error=$PDOexecption->getMessage();
}
}

private function executaquery($sql){
try{
$this->statement=$this->connection->query($sql);
}
catch(PDOException $PDOException){
$this->error=$PDOexecption->getMessage();
}
}

public function fetch(){
try{
return $this->statement->fetch();
}
catch(PDOException $PDOException){
echo($PDOexecption->getMessage());
}
}

public function fetchAll(){
try{
return $this->statement->fetchAll();
}
catch(PDOException $PDOException){
echo($PDOexecption->getMessage());
}
}

public function close(){
try{
unset($this->connection);
}
catch(PDOException $PDOException){
echo($PDOexecption->getMessage());
}
}

}

?>