<?php
class ZRadioQuestion extends ZQuestion{
  private $data;

  public function __construct($name, $domanda, $descrizione, $obbligatoria, $data){
    $this->data = $data;
    parent::__construct($name, $domanda, $descrizione, $obbligatoria, null);
  }

  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-radio">';
    foreach($this->data as $k => $v){
      $html .='<div class="radio-option">
        <div class="outer-radio">
          <div class="inner-radio"></div>
        </div>
        <div class="radio-desc">'.$v.'</div>
        <input class="anser" type="radio" name="'.$this->content['name'].'" value="'.$k.'">
      </div>';
    }
    $html .= '</div>'.$this->getPostHtml();
    return $html;
  }

}
