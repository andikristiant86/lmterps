<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$m_arr=array("01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Des");

$sql="SELECT X.*,(SELECT amount FROM lmt_dashboard.[dbo].[TRX] WHERE PERIODE=X.PERIODE) BBM FROM (
SELECT PERIODE,sum(amount) PULSA FROM [dbo].[T_PULSA_REAL] WHERE PERIODE BETWEEN 201701 and 201801 GROUP BY PERIODE
) X ORDER BY X.PERIODE";

	$res=$dbproj->Execute($sql);
	while($row=$res->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$year=substr("$periode",0,4);
		$month=substr("$periode",-2);
		$month=$m_arr[$month];
		$arr[]=array("Periode"=>"$month $year","Pulsa"=>"$pulsa","BBM"=>"$bbm");
	}
	echo json_encode($arr);
?>
