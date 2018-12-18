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
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='RIGGER';";
			$jum_rigger=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='DRIVER';";
			$jum_driver=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='DT';";
			$jum_dt=$db->getOne($sql1);
	  
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='ENGINEER';";
			$jum_engineer=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='MANAGEMENT';";
			$jum_management=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='SUPPORT';";
			$jum_support=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE] where periode='$year$month' and LEVEL_JABATAN='FILE';";
			$jum_file=$db->getOne($sql1);
		}else{
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='RIGGER' and LOKASI='$act';";
			$jum_rigger=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='DRIVER' and LOKASI='$act';";
			$jum_driver=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='DT' and LOKASI='$act';";
			$jum_dt=$db->getOne($sql1);
	  
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='ENGINEER' and LOKASI='$act';";
			$jum_engineer=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='MANAGEMENT' and LOKASI='$act';";
			$jum_management=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='SUPPORT' and LOKASI='$act';";
			$jum_support=$db->getOne($sql1);
			
			$sql1="SELECT jum_resource FROM lmt_dashboard.[dbo].[DASH_RESOURCE1] where periode='$year$month' and LEVEL_JABATAN='FILE' and LOKASI='$act';";
			$jum_file=$db->getOne($sql1);
		}
		$arr[]=array("Periode"=>"$monthtxt $year","Rigger"=>"$jum_rigger","Driver"=>"$jum_driver","DriveTest"=>"$jum_dt","Management"=>"$jum_management",
		"Engineer"=>"$jum_engineer","Support"=>"$jum_support","Unknow"=>"$jum_file");
		
  $min = strtotime("+1 month", $min);
}
echo json_encode($arr);
?>
