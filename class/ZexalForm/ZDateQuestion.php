<?php
class ZDateQuestion extends ZQuestion{
  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-date">
		  <input class="anser" name="'.$this->content['name'].'" type="date" placeholder="La tua risposta">
		<div class="underline"></div>
		</div>';
    $html .= $this->getPostHtml();
    return $html;
  }
}
