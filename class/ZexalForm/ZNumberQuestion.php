<?php
class ZNumberQuestion extends ZQuestion{
  public function getHtml(){
    $html = $this->getPreHtml();
    $html .= '<div class="question-number">
		  <input class="anser" name="'.$this->content['name'].'" type="number" placeholder="La tua risposta">
		<div class="underline"></div>
		</div>';
    $html .= $this->getPostHtml();
    return $html;
  }
}
