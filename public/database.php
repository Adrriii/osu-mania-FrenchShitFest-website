<?php

class Database {

	private $pdo;
	private $result;

	public function __construct($db = "shitfest"){
		try {
			$this->pdo = new PDO("mysql:host=localhost;dbname=$db;charset=utf8", "shitfest", trim(file_get_contents("/var/osu/shitfestpass"))); /// mdr c securiser
		} catch(Exception $e){
			die("Connexion à la base de données ECHOUAGE de b.");
		}
	}

	public function query($sql, $opt = null, $mode = PDO::FETCH_BOTH){
		try {
			$query = $this->pdo->prepare($sql);
			$query->execute($opt);
			$this->result = $query->fetchAll($mode);
			return true;
		} catch(Exception $e){
			return false;
		}
	}

	public function getResult($i = null){
		if($i == null){
			return $this->result;
		}
		if(isset($this->result[$i])){
			return $this->result[$i];
		}
		return false;
	}

	public function fast($sql, $opt = null, $i = null, $mode = PDO::FETCH_BOTH){
		$this->query($sql, $opt, $mode);
		return $this->getResult($i);
	}
}

?>