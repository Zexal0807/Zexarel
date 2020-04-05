<?php
function build_query($sql, $param){
  foreach ($param as $p) {
    $sql = _b($sql, $p);
  }
  return $sql;
}

function _b($sql, $p){
  if(is_array($p)){
    $t = [];
    foreach ($p as $pp) {
      $t[] = _b("?", $pp);
    }
    $sql = implode(implode(", ", $t), explode("?", $sql, 2));
  }else{
    // TODO: escape

    if(is_null($p)){
      $p = "null";
    }elseif(!(
      is_numeric($p) ||
      is_bool($p) ||
      is_null($p)
    )){
      $p = addslashes($p);
      $p = "'".$p."'";
    }
    $sql = implode($p, explode("?", $sql, 2));
  }
  return $sql;
}
?>
