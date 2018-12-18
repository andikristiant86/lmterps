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
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='RIGGER' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' GROUP BY sub_group;";
			$jum_rigger=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='DRIVER' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' GROUP BY sub_group;";
			$jum_driver=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='DT' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' GROUP BY sub_group;";
			$jum_dt=$db->getOne($sql1);
	  
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='ENGINEER' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' GROUP BY sub_group;";
			$jum_engineer=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='MANAGEMENT REGION' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' GROUP BY sub_group;";
			$jum_management=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='INSTALASI' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' GROUP BY sub_group;";
			$jum_support=$db->getOne($sql1);
			
			$sql1="";
			$jum_file=$db->getOne($sql1);
		}else{
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='RIGGER' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' and lokasi_id='$act' GROUP BY sub_group;";
			$jum_rigger=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='DRIVER' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' and lokasi_id='$act' GROUP BY sub_group;";
			$jum_driver=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='DT' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' and lokasi_id='$act' GROUP BY sub_group;";
			$jum_dt=$db->getOne($sql1);
	  
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='ENGINEER' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' and lokasi_id='$act' GROUP BY sub_group;";
			$jum_engineer=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='MANAGEMENT REGION' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' and lokasi_id='$act' GROUP BY sub_group;";
			$jum_management=$db->getOne($sql1);
			
			$sql1="SELECT count(NIP) jumlah_resource FROM [dbo].[BACKUP_RESOURCE] WHERE sub_group='INSTALASI' and 
				periode='$year$month' and cmp_id='CMP-000001' and [group]='REGION' and lokasi_id='$act' GROUP BY sub_group;";
			$jum_support=$db->getOne($sql1);
			
			$sql1="";
			$jum_file=$db->getOne($sql1);
		}
		$arr[]=array("Periode"=>"$monthtxt $year","Rigger"=>"$jum_rigger","Driver"=>"$jum_driver","DriveTest"=>"$jum_dt","Management"=>"$jum_management",
		"Engineer"=>"$jum_engineer","Instalasi"=>"$jum_support","Unknow"=>"$jum_file");
		
  $min = strtotime("+1 month", $min);
}
echo json_encode($arr);
?>
