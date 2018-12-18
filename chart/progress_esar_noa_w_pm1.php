<?php
include "koneksi.php";
$min=strtotime("saturday");
$max=strtotime("4 week", $min);
$arr=array();
$sql="SELECT * FROM lmt_project.dbo.DASH_HERU";
$result=$dberps->Execute($sql);
$arr=array();
	
while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		
  $week=date("W", $min);
  
  $arr[]=array("PM"=>"$pm","Progress1"=>"$telkomsel","Progress2"=>"$indosat","Progress3"=>"$xl","Progress4"=>"$hcpt");	
	
}
echo json_encode($arr);
?>
