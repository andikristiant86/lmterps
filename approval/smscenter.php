<?php
$no="085692411486";
$c=substr($no,0,1);
if($c=='0'){
	echo substr_replace($no,62,0,1);
}else if($c=='+'){
	echo substr_replace($no,'',0,1);
}
?>