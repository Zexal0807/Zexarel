<?php
class ZBladeCompiler{

	private static $CHAR = '[a-zA-Z0-9àèéìòù\s\-\_\(\)\:\=\<\>\;\,\.\?\$\"\'\[\]]*(\n)?';

	private static $search = [
		'/\{+\{+\{($CHAR)\}+\}+\}/',
		'/\{+\{($CHAR)\}+\}/',
		'/\{+\-+\-($CHAR)\-+\-+\}/',
		'/\@if\(($CHAR)\)/',
		'/\@elseif\(($CHAR)\)/',
		'/\@foreach\(($CHAR)\)/',
		'/\@for\(($CHAR)\)/',
		'/\@while\(($CHAR)\)/',
		'/\@enddowhile\(($CHAR)\)/',
		'/\@dowhile/',
		'/\@else/',
		'/\@endif/',
		'/\@endoforeach/',
		'/\@endfor/',
		'/\@endwhile/'
	];

	private static $replace = [
		'<?php $1; ?>',
		'<?php echo $1; ?>',
		'<?php /* $1 */ ?>',
		'<?php if($1){ ?>',
		'<?php }elseif($1){ ?>',
		'<?php foreach($1){ ?>',
		'<?php for($1){ ?>',
		'<?php while($1){ ?>',
		'<?php }while($1); ?>',
		'<?php do{ ?>',
		'<?php }else{ ?>',
		'<?php } ?>',
		'<?php } ?>',
		'<?php } ?>',
		'<?php } ?>'
	];

	public static function compile($str, $data = null){
		for($i = 0; $i < sizeof(static::$search); $i++){
			$str = preg_replace(str_replace('$CHAR', static::$CHAR, static::$search[$i]), static::$replace[$i], $str);
		}
		ob_start();
		if(sizeof($data) > 0){
			foreach($data as $k => $v){
				${$k} = $v;
			}
		}
		eval("?>".$str);
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
?>
