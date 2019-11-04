<?php
class ZButton{

  protected $content;

  public function __construct($section){
    $this->content = [
      "data-section" => ($section == "submit" ? $section : $section)
		];
  }

  public function getHtml(){
    $html = '
    <button class="section-button" ';
    $html .= 'data-section="'.$this->content['data-section'].'" type="button"';
    $html .= '>AVANTI</button>';
    return $html;
  }
}
?>
