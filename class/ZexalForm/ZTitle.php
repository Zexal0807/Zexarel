<?php
class ZTitle{

  protected $content;

  public function __construct($title, $desc = null){
    if(!isset($desc)){
      $desc = "";
    }
    $this->content = [
      "data-titolo" => $title,
      "data-descrizione" => $desc
		];
  }
  function getHtml(){
    $html = '
    <div class="section-title">
			'.$this->content['data-titolo'].'
      <div class="desc">'.$this->content['data-descrizione'].'</div>
		</div>';
    return $html;
  }
}
?>
