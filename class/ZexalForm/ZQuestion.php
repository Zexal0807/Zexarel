<?php
abstract class ZQuestion{

  protected $content;

  public function __construct($name, $domanda, $descrizione, $obbligatoria, $req = null){
    $this->content = [
      "name" => $name,
      "data-domanda" => $domanda,
      "data-descrizione" => $descrizione,
      "data-required" => boolval($obbligatoria),
      "data-valid" => (isset($req) ? $req : (boolval($obbligatoria) ? ".{1,}" : ".*"))
		];
  }

  public function getPreHtml(){
    $html = '
    <div class="section-question"';
    foreach($this->content as $k => $v){
      $html .= $k.'="'.$v.'"';
    }
    $html .= '>
				<div class="question">
					'.$this->content['data-domanda'].'
					<span>'.($this->content['data-required'] ? '*' : '').'</span>
					<div class="desc">'.$this->content['data-descrizione'].'</div>
				</div>';
    return $html;
  }
  public function getPostHtml(){
    $html = ($this->content['data-required'] ? '
      <div class="error">
        Questa è una domanda obbligatoria
      </div>' : '').
    '</div>';
    return $html;
  }
  abstract function getHtml();

}

?>
