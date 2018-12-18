<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$minx=strtotime(date("M"));
$min=strtotime("-14 month", $minx);
$max=strtotime("12 month", $min);
$arr=array();
while ($min < $max) {
  $month=date("m", $min);
  $monthtxt=date("M", $min);
  $year=date("Y", $min);
		$salary=$db->getOne("select sum(thp) from backup_resource where cmp_id='CMP-000001' and periode='$year$month'");
		$pulsa=$dbproj->getOne("SELECT SUM(AMOUNT) FROM [dbo].[T_PULSA_REAL] WHERE PERIODE='$year$month';");
		$other=$dbproj->getOne("SELECT SUM(NOMINAL) FROM [dbo].[T_OPERATIONAL_REAL] WHERE PERIODE='$year$month';");
		$carpool=$dbproj->getOne("SELECT SUM(NOMINAL) FROM [dbo].[T_CARPOOL_REAL] WHERE PERIODE='$year$month';");
		$rigger=$dbproj->getOne("SELECT SUM(PAYMENT) FROM [dbo].[T_OP_RIGGER_REAL] WHERE PERIODE='$year$month';");
		
		$po=$dbproj->getOne("SELECT SUM(X.PO_PERMONTH) FROM (
SELECT PROJ_CODE,START_DATE,DUE_DATE,PROJECT_SALES,DATEDIFF(MONTH,START_DATE,DUE_DATE)+1 JUM_BULAN,
PROJECT_SALES/(DATEDIFF(MONTH,START_DATE,DUE_DATE)+1) PO_PERMONTH,CONVERT(nvarchar(6), START_DATE, 112) START_PERIODE,
CONVERT(nvarchar(6), DUE_DATE, 112) END_PERIODE
FROM [dbo].[M_PROJECT] where	TYPE_WORK IN ('INSTALASI','OPTIM')
) X WHERE X.START_PERIODE <= '$year$month' AND X.END_PERIODE >= '$year$month'");
		
		
		$total_expense=$salary+$pulsa+$other+$carpool+$rigger;
		$esar=$db->getOne("select sum(grand_total) from lmt_dashboard.dbo.data_esar where periode='$year$month'");
		$arr[]=array("Periode"=>"$monthtxt $year","PO"=>"$po","Esar"=>"$esar","Costproject"=>"$total_expense");
		
  $min = strtotime("+1 month", $min);
}
echo json_encode($arr);
?>
