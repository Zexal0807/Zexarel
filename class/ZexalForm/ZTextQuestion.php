<?php
class ZTextQuestion extends ZQuestion{
  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-text">
		  <input class="anser" name="'.$this->content['name'].'" type="text" placeholder="La tua risposta">
		<div class="underline"></div>
		</div>';
    $html .= $this->getPostHtml();
    return $html;
  }
}
