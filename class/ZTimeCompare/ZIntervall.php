<?php
class ZIntervall{
	private $inizio;
	private $fine;

	public function __construct($inizio, $fine){
		$this->inizio = $inizio;
		$this->fine = $fine;
	}
	public function getInizio(){
		return $this->inizio;
	}
	public function getFine(){
		return $this->fine;
	}
	public function getDurata(){
		return strtotime($this->fine)-strtotime($this->inizio);
	}

}
?>
