<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$m_arr=array("01"=>"Jan","02"=>"Feb","03"=>"Mar","04"=>"Apr","05"=>"May","06"=>"Jun","07"=>"Jul","08"=>"Aug","09"=>"Sep","10"=>"Oct","11"=>"Nov","12"=>"Des");
if($act=='All'){
$sql="SELECT *,(Salary/Employee)/10000 AS Employee_cost FROM (
		SELECT PERIODE,SUM(THP) Salary,count(nip) Employee  FROM [dbo].[BACKUP_RESOURCE]
		WHERE CMP_ID='CMP-000001' AND PERIODE BETWEEN 201701 and 201806 GROUP BY PERIODE
		) AS X order by periode";
}elseif($act=='Region'){
$sql="SELECT *,(Salary/Employee)/10000 AS Employee_cost FROM (
		SELECT PERIODE,SUM(THP) Salary,count(nip) Employee  FROM [dbo].[BACKUP_RESOURCE]
		WHERE CMP_ID='CMP-000001' AND [GROUP]='REGION' AND PERIODE BETWEEN 201701 and 201806 GROUP BY PERIODE
		) AS X order by periode";
}elseif($act=='Management'){
$sql="SELECT *,(Salary/Employee)/10000 AS Employee_cost FROM (
		SELECT PERIODE,SUM(THP) Salary,count(nip) Employee  FROM [dbo].[BACKUP_RESOURCE]
		WHERE CMP_ID='CMP-000001' AND [GROUP]='MANAGEMENT' AND PERIODE BETWEEN 201701 and 201806 GROUP BY PERIODE
		) AS X order by periode";
}else{
$sql="SELECT *,(Salary/Employee)/10000 AS Employee_cost FROM (
		SELECT PERIODE,SUM(THP) Salary,count(nip) Employee  FROM [dbo].[BACKUP_RESOURCE]
		WHERE LOKASI_ID='$act' AND CMP_ID='CMP-000001' AND [GROUP]='REGION' AND PERIODE BETWEEN 201701 and 201806 GROUP BY PERIODE
		) AS X order by periode";
}
	$res=$db->Execute($sql);
	while($row=$res->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$year=substr("$periode",0,4);
		$month=substr("$periode",-2);
		$month=$m_arr[$month];
		$arr[]=array("Periode"=>"$month<br>$year","Salary"=>"$salary","Employee"=>"$employee","Employee_cost"=>"$employee_cost");
	}
	echo json_encode($arr);
?>
