<?php
class ZCheckQuestion extends ZQuestion{
  private $data;

  public function __construct($name, $domanda, $descrizione, $obbligatoria, $data){
    $this->data = $data;
    parent::__construct($name, $domanda, $descrizione, $obbligatoria, null);
  }

  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-check">';
    foreach($this->data as $k => $v){
      $html .='<div class="check-option">
      <div class="outer-check">
        <div class="inner-check"></div>
      </div>
      <div class="check-desc">'.$v.'</div>
      <input class="anser" type="checkbox" name="'.$this->content['name'].'[]" value="'.$k.'">
      </div>';
    }
    $html .= '</div>'.$this->getPostHtml();
    return $html;
  }

}
