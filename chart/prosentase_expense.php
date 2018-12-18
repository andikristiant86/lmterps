<?php
include "koneksi.php";
$total=$dberps->getOne("select sum(amount_rp) from summary_cost");

	$sql="select * from summary_cost";
	$result=$dberps->Execute($sql);
	$arr=array();
	
	while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$prosentase=($amount_rp/$total)*100;
		$arr[]=array("Browser"=>"$type","Share"=>"$prosentase");
	}
	
echo json_encode($arr);
?>