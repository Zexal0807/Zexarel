<?php
include("Zexarel/class/ZexalForm/ZTitle.php");
include("Zexarel/class/ZexalForm/ZQuestion.php");
include("Zexarel/class/ZexalForm/ZTextQuestion.php");
include("Zexarel/class/ZexalForm/ZNumberQuestion.php");
include("Zexarel/class/ZexalForm/ZEmailQuestion.php");
include("Zexarel/class/ZexalForm/ZPhoneQuestion.php");
include("Zexarel/class/ZexalForm/ZRadioQuestion.php");
include("Zexarel/class/ZexalForm/ZCheckQuestion.php");
include("Zexarel/class/ZexalForm/ZFileQuestion.php");
include("Zexarel/class/ZexalForm/ZButton.php");
class ZexalForm{

	private $formId;
  private $countSection;
	private $content;

	public function __construct($action, $method){
		$this->formId = rand(0, 100);
		$this->content = [
			"data" => [
				"action" => isset($action) ? $action : "",
				"method" => isset($method) ? $method : 'GET',
				"id" => "zexalform".$this->formId,
				"enctype" => "multipart/form-data"
			],
			"section" => []
		];
    $this->countSection = 0;
	}

  public function add($obj){
    $this->content['section'][$this->countSection][] = $obj;
  }
  public function addSection(){
    $this->countSection++;
    $this->content['section'][$this->countSection] = [];
  }

  public function getHtml(){
    $html = '<form class="zexal-form" ';
    foreach($this->content['data'] as $k => $v){
      if(find("data", $v) < 0){
        $html .= $k.'="'.$v.'"';
      }
    }
    $html .= '>
			<div class="col-11 col-md-7"></div>
			<div id="carouselExampleSlidesOnly" class="carousel slide col-11 col-md-7 p-4" data-interval="false">
				<div class="carousel-inner">';
    for($i = 0; $i < sizeof($this->content['section']); $i++){
      $html .= '<div class="carousel-item'.($i == 0 ? ' active"' : '"').' data-value="'.$i.'">';
      foreach($this->content['section'][$i] as $obj){
        $html .= $obj->getHtml();
      }
      $html .= '</div>';
    }
    $html .= '
        </div>
			</div>
		</form>';
		$js = file_get_contents("Zexarel/class/ZexalForm/zexalForm.js");
		$js = str_replace("#FORMID", "#".$this->content['data']['id'], '<script>'.$js.'</script>');
		$html .= $js;
    return $html;
  }

}
