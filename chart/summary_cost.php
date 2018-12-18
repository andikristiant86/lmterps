<?php
include "koneksi.php";
	$sql="select * from summary_cost";
	$result=$dberps->Execute($sql);
	$arr=array();
	
	while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$arr[]=array("Type"=>"$type","Amount"=>"$amount_rp");
	}
	
echo json_encode($arr);
?>