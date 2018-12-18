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
		
			$sql1="SELECT count(*)  FROM [dbo].[M_PROJECT] A
					LEFT JOIN M_PROJECT_AREA B ON B.LKE_ID=A.LKE_ID
					WHERE LEFT(CONVERT(varchar, A.START_DATE,112),6)<='$year$month' AND LEFT(CONVERT(varchar, A.DUE_DATE,112),6)>='$year$month' and A.LKE_ID='ARA-000001'
					GROUP BY A.LKE_ID,B.LOKASI_KERJA";
			$jabodetabek=$dbproj->getOne($sql1);
			
			$sql1="SELECT count(*)  FROM [dbo].[M_PROJECT] A
					LEFT JOIN M_PROJECT_AREA B ON B.LKE_ID=A.LKE_ID
					WHERE LEFT(CONVERT(varchar, A.START_DATE,112),6)<='$year$month' AND LEFT(CONVERT(varchar, A.DUE_DATE,112),6)>='$year$month' and A.LKE_ID='ARA-000002'
					GROUP BY A.LKE_ID,B.LOKASI_KERJA";
			$ej=$dbproj->getOne($sql1);
			
			$sql1="SELECT count(*)  FROM [dbo].[M_PROJECT] A
					LEFT JOIN M_PROJECT_AREA B ON B.LKE_ID=A.LKE_ID
					WHERE LEFT(CONVERT(varchar, A.START_DATE,112),6)<='$year$month' AND LEFT(CONVERT(varchar, A.DUE_DATE,112),6)>='$year$month' and A.LKE_ID='ARA-000003'
					GROUP BY A.LKE_ID,B.LOKASI_KERJA";
			$cj=$dbproj->getOne($sql1);
		
			$sql1="SELECT count(*)  FROM [dbo].[M_PROJECT] A
					LEFT JOIN M_PROJECT_AREA B ON B.LKE_ID=A.LKE_ID
					WHERE LEFT(CONVERT(varchar, A.START_DATE,112),6)<='$year$month' AND LEFT(CONVERT(varchar, A.DUE_DATE,112),6)>='$year$month' and A.LKE_ID='ARA-000004'
					GROUP BY A.LKE_ID,B.LOKASI_KERJA";
			$wj=$dbproj->getOne($sql1);
		
			$sql1="SELECT count(*)  FROM [dbo].[M_PROJECT] A
					LEFT JOIN M_PROJECT_AREA B ON B.LKE_ID=A.LKE_ID
					WHERE LEFT(CONVERT(varchar, A.START_DATE,112),6)<='$year$month' AND LEFT(CONVERT(varchar, A.DUE_DATE,112),6)>='$year$month' and A.LKE_ID='ARA-000006'
					GROUP BY A.LKE_ID,B.LOKASI_KERJA";
			$balom=$dbproj->getOne($sql1);
			
			$sql1="SELECT count(*)  FROM [dbo].[M_PROJECT] A
					LEFT JOIN M_PROJECT_AREA B ON B.LKE_ID=A.LKE_ID
					WHERE LEFT(CONVERT(varchar, A.START_DATE,112),6)<='$year$month' AND LEFT(CONVERT(varchar, A.DUE_DATE,112),6)>='$year$month' and A.LKE_ID='ARA-000007'
					GROUP BY A.LKE_ID,B.LOKASI_KERJA";
			$ncs=$dbproj->getOne($sql1);
			
		$arr[]=array("Periode"=>"$monthtxt $year","Jabodetabek"=>"$jabodetabek","East_java"=>"$ej","Central_java"=>"$cj","West_java"=>"$wj","Balom"=>"$balom","NCS"=>"$ncs");
		
  $min = strtotime("+1 month", $min);
}
echo json_encode($arr);
?>
