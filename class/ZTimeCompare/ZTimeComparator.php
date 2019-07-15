<?php
class ZTimeComparator{

  private $inizi;
  private $fini;

  public function __construct(){
    $this->inizi = [];
    $this->fini = [];
    $arg = func_get_args();
    foreach($arg as $key => $value){
      $this->add($value);
    }
  }

  public function add($int){
    if(!($int instanceof ZIntervall)){
      return;
    }
    $this->inizi[] = $int->getInizio();
    $this->fini[] = $int->getFine();
  }

  public function getIntersezione(){
    $inizio = max($this->inizi);
    $s = false;
    for($i = 0; $i < sizeof($this->fini); $i++){
      if($this->fini[$i] < $inizio){
        $s = true;
      }
    }
    if($s){
      $fine = $inizio;
    }else{
      $fine = min($this->fini);
    }
    var_dump($inizio);
    var_dump($fine);
	  return new ZIntervall($inzio, $fine);
  }
}
?>
