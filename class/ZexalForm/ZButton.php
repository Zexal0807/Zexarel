<?php
class ZButton{

  protected $content;

  public function __construct($section){
    $this->content = [
      "data-section" => ($section == "submit" ? $section : intval($section))
		];
  }

  public function getHtml(){
    $html = '
    <button class="section-button" ';
    if($this->content['data-section'] == "submit"){
        $html .= 'type="submit"';
    }else{
      $html .= 'data-section="'.$this->content['data-section'].'" type="button"';
    }
    $html .= '>AVANTI</button>';
    return $html;
  }
}
?>
