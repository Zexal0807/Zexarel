<?php
class ZCleaveQuestion extends ZQuestion{

  private $data;

  public function __construct($name, $domanda, $descrizione, $obbligatoria, $data, $req = null){
    parent::__construct($name, $domanda, $descrizione, $obbligatoria, $req);
    $this->data = $data;
  }

  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-text">
		  <input class="anser" name="'.$this->content['name'].'" type="tetx" placeholder="La tua risposta">
		<div class="underline"></div>
		</div>';
    $html .= $this->getPostHtml();
    $html .= '<script>$(document).ready(function(){
      var cl'.$this->content["name"].' = new Cleave("input[name='.$this->content['name'].']", {'.implode(", ", $this->data).'}
      );
    });</script>';

    return $html;
  }
}
