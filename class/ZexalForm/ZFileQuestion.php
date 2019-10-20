<?php
class ZFileQuestion extends ZQuestion{

  private $multiple;
  private $accept;

  public function __construct($name, $domanda, $descrizione, $obbligatoria, $accept = null, $multiple = null){
    $this->multiple = boolval($multiple);
    $this->accept = isset($accept) ? $accept : false;
    parent::__construct($name, $domanda, $descrizione, $obbligatoria);
  }

  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-file">
      <button class="file-upload" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"><g transform="translate(-3, -3)"><path d="M6,14.25 L7.5,14.25 L7.5,16.5 L16.5,16.5 L16.5,14.25 L18,14.25 L18,16.5 C18,17.325 17.325,18 16.5,18 L7.5,18 C6.675,18 6,17.325 6,16.5 L6,14.25 Z M9.3075,10.8075 L11.25,8.8725 L11.25,15 L12.75,15 L12.75,8.8725 L14.6925,10.815 L15.75,9.75 L12,6 L8.25,9.75 L9.3075,10.8075 Z"></path><path fill="none"></path></g></svg>
        Upload file
      </button>
      <button class="file-uploaded" data-name="nome.png" type="button">
        <svg height="18" width="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 352 512"><path fill="currentColor" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg>
      </button>
		  <input class="anser" name="'.$this->content['name'].'" type="file" ';
      if($this->accept != false){
        $html .= 'accept="'.$this->accept.'" ';
      }
      if($this->multiple){
        $html .= "multiple ";
      }
      $html .='/>
		</div>';
    $html .= $this->getPostHtml();
    return $html;
  }
}
