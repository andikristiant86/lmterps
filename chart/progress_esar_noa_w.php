<?php
include "koneksi.php";
$min=strtotime("saturday");
$max=strtotime("4 week", $min);
$arr=array();
$sql="SELECT Operator,sum(NOA) as noa, sum(NY_QC_Accepted) as NY_QC_Accepted,sum(not_found) as Not_Found FROM lmt_project.[dbo].[DHS_PROGRESS_ESAR_OA] GROUP BY Operator";
$result=$dberps->Execute($sql);
$arr=array();
	
while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		
  $week=date("W", $min);
  
  $arr[]=array("Operator"=>"$operator","Progress1"=>"$noa","Progress2"=>"$ny_qc_accepted","Progress3"=>"$not_found");	
	
}
echo json_encode($arr);
?>
