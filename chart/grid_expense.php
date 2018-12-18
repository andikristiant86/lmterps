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
		$arr[]=array("Projcode"=>"020-HCPT","Budget"=>"100000");
	}
	
echo json_encode($arr);
?>