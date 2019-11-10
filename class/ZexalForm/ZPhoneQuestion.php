<?php
class ZPhoneQuestion extends ZQuestion{

  public function __construct($name, $domanda, $descrizione, $obbligatoria){
    parent::__construct($name, $domanda, $descrizione, $obbligatoria, null, 10);
  }

  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-number">
		  <input class="anser" name="'.$this->content['name'].'" type="tel" placeholder="La tua risposta">
		<div class="underline"></div>
		</div>';
    $html .= $this->getPostHtml();
    return $html;
  }
}
