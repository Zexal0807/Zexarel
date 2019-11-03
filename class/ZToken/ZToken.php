<?php
class ZToken{

	private static $N1 = 10003794;
	private static $N2 = 239746;

	private static $Letter = [
		"p", "q", "e", "h", "l",
		"o", "x", "b", "w", "f",
		"r", "m", "u", "d", "g",
		"i", "a", "z", "s", "j",
		"t", "n", "v", "c", "k", "y"
	];

	public function encript64Bit($num){
		$n = $num + ZToken::$N1;
		$tmp = str_split($n."");
		$n = [];
		$n[0] = $tmp[6];
		$n[1] = $tmp[2];
		$n[2] = $tmp[5];
		$n[3] = $tmp[3];
		$n[4] = $tmp[7];
		$n[5] = $tmp[1];
		$n[6] = $tmp[4];
		$n[7] = $tmp[0];
		$n = intval(implode("", $n));
		$n = $n + ZToken::$N2;
		$tmp = str_split($n."");
		$n = [];
		$n[0] = $tmp[6];
		$n[1] = $tmp[2];
		$n[2] = $tmp[5];
		$n[3] = $tmp[3];
		$n[4] = $tmp[7];
		$n[5] = $tmp[1];
		$n[6] = $tmp[4];
		$n[7] = $tmp[0];
		$tmp = time();
		for($i = 0; $i < 8;  $i++){
			$n[$i] = ZToken::$Letter[$tmp%26].$n[$i];
			$tmp += rand(5, 9);
		}
		$n = implode("", $n);
		return $n;
	}

	public function decrypt64Bit($num){
		$tmp = str_split($num."");
        $tmp = str_split($tmp[1].$tmp[3].$tmp[5].$tmp[7].$tmp[9].$tmp[11].$tmp[13].$tmp[15]);
        $n = [];
		$n[0] = $tmp[7];
		$n[1] = $tmp[5];
		$n[2] = $tmp[1];
		$n[3] = $tmp[3];
		$n[4] = $tmp[6];
		$n[5] = $tmp[2];
		$n[6] = $tmp[0];
		$n[7] = $tmp[4];
		$n = intval(implode("", $n));
		$n = $n - ZToken::$N2;
		$tmp = str_split($n);
		$n = [];
    	$n[0] = $tmp[7];
		$n[1] = $tmp[5];
		$n[2] = $tmp[1];
		$n[3] = $tmp[3];
		$n[4] = $tmp[6];
		$n[5] = $tmp[2];
		$n[6] = $tmp[0];
		$n[7] = $tmp[4];
		$n = intval(implode("", $n));
		$n = $n - ZToken::$N1;
		return $n;
	}

	public function encript128Bit($num){
		$n = $this->encript64Bit($num);
		$tmp = str_split($n."");
		$n = [];
		$t = time();
		for($i = 0; $i < 8; $i++){
			$r = rand(0, 9);
			$t += $r;
			$n[$i*8]=$tmp[$i*2];
			$n[$i*8+1]=$r;
			$n[$i*8+2]=ZToken::$Letter[$t%26];
			$n[$i*8+3]=$tmp[$i*2+1];
		}
		$n = implode("", $n);
		return $n;
	}

	public function decrypt128Bit($num){
		$n = str_split($num."");
        $n = $n[0].$n[3].$n[4].$n[7].$n[8].$n[11].$n[12].$n[15].$n[16].$n[19].$n[20].$n[23].$n[24].$n[27].$n[28].$n[31];
		return $this->decrypt64Bit($n);
	}

	public function encript256Bit($num){
		$n = $this->encript128Bit($num);
		$tmp = str_split($n."");
		$n = [];
		for($i = 0; $i < 8; $i++){
			$n[$i*8] = $tmp[$i*4];
			$n[$i*8+1] = ($tmp[$i*4+1]+6 > 10 ? $tmp[$i*4+1]-4 : $tmp[$i*4+1]+6);
			$n[$i*8+2] = (chr(ord($tmp[$i*4+2])-intval($tmp[$i*4+1])) < "a" ? chr(ord($tmp[$i*4+2])+26-intval($tmp[$i*4+1])) : chr(ord($tmp[$i*4+2])-intval($tmp[$i*4+1])));
			$n[$i*8+3] = $tmp[$i*4+1];
			$n[$i*8+4] = $tmp[$i*4+2];
			$n[$i*8+5] = ($tmp[$i*4+1]-6 < 0 ? $tmp[$i*4+1]+4 : $tmp[$i*4+1]-6);
			$n[$i*8+6] = (chr(ord($tmp[$i*4])+intval($tmp[$i*4+1])) > "z" ? chr(ord($tmp[$i*4])-26+intval($tmp[$i*4+1])) : chr(ord($tmp[$i*4])+intval($tmp[$i*4+1])));
			$n[$i*8+7] = $tmp[$i*4+3];
		}
		$n = implode("", $n);
		return $n;
	}

	public function decrypt256Bit($num){
		$n = str_split($num."");
        $n = $n[0].$n[3].$n[4].$n[7].$n[8].$n[11].$n[12].$n[15].$n[16].$n[19].$n[20].$n[23].$n[24].$n[27].$n[28].$n[31].$n[32].$n[35].$n[36].$n[39].$n[40].$n[43].$n[44].$n[47].$n[48].$n[51].$n[52].$n[55].$n[56].$n[59].$n[60].$n[63];
		return $this->decrypt128Bit($n);
	}
  
}
