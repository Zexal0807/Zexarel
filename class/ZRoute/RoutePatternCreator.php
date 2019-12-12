<?php
class RoutePatternCreator{
  public static function create($p, &$parameter = null){
    $p = trim($p, '/\^$');
    $p = explode("/", $p);
    $pattern = "";
    $f = false;
    for($i = 0; $i < sizeof($p); $i++){
      if(preg_match('/\<+[a-zA-Z0-9]*+\>/', $p[$i])){
        if($f){
          $pattern .= "+\/";
        }
        $f = true;
        $pattern .= '+[a-zA-Z0-9]*';
        $parameter[] = $p[$i];
      }elseif(preg_match('/\\[+[a-zA-Z0-9]*+\]/', $p[$i])){
        $pattern .= "+(\/+".str_replace(["[", "]"], "", $p[$i]).')?';
      }else{
        if($f){
          $pattern .= "+\/";
        }
        $pattern .= $p[$i];
        $f = true;
      }
    }
    return $pattern;
  }
}
?>
