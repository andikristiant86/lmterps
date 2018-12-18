<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$minx=strtotime(date("M"));
$min=strtotime("-13 month", $minx);
$max=strtotime("13 month", $min);
$arr=array();
while ($min < $max) {
  $month=date("m", $min);
  $monthtxt=date("M", $min);
  $year=date("Y", $min);
		if($act=='All' || empty($act)){
			$sql1="SELECT count(*) FROM lmt_project.[dbo].[M_RENTAL_PERIOD] WHERE PERIODE='$year$month'";
			$jum_car=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='DRIVER';";
			$jum_driver=$db->getOne($sql1);
			
			$sql="SELECT SUM(nominal) FROM [dbo].[T_CARPOOL_REAL] WHERE PERIODE='$year$month';";
			$jum_cost=$dbproj->getOne($sql);
			
		}else{
			$sql1="SELECT count(*) FROM lmt_project.[dbo].[M_RENTAL_PERIOD] WHERE PERIODE='$year$month' and lke_id='$lke_id'";
			$jum_car=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='DRIVER' and LOKASI='$act';";
			$jum_driver=$db->getOne($sql1);
			
			$sql="SELECT SUM(nominal) FROM [dbo].[T_CARPOOL_REAL] WHERE PERIODE='$year$month' and LKE_ID='$lke_id';";
			$jum_cost=$dbproj->getOne($sql);
		}
		$arr[]=array("Periode"=>"$monthtxt $year","Car"=>"$jum_car","Driver"=>"$jum_driver","Cost"=>"$jum_cost");
		
  $min = strtotime("+1 month", $min);
}
echo json_encode($arr);
?>
