<?php
class ZEmailQuestion extends ZQuestion{
  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-text">
		  <input class="anser" name="'.$this->content['name'].'" type="email" placeholder="La tua risposta">
		<div class="underline"></div>
		</div>';
    $html .= $this->getPostHtml();
    return $html;
  }
}
