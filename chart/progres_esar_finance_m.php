<?php
include "koneksi.php";
$minx=strtotime(date("m"));
$min=strtotime("5 month", $minx);
$max=strtotime("3 month", $min);
$arr=array();
while ($min < $max) {
  $month=date("m", $min);
  $monthtxt=date("M", $min);
  $year=date("Y", $min)+48;
  
  $sql_1="select top 1 amount_idr_1 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_1=$dberps->getOne($sql_1);
  
  $sql_2="select top 1 amount_idr_2 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_2=$dberps->getOne($sql_2);
  
  $sql_3="select top 1 amount_idr_3 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_3=$dberps->getOne($sql_3);
  
  $sql_4="select top 1 amount_idr_4 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_4=$dberps->getOne($sql_4);
  
  $sql_5="select top 1 amount_idr_5 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_5=$dberps->getOne($sql_5);
  
  $sql_6="select top 1 amount_idr_6 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_6=$dberps->getOne($sql_6);
  
  $arr[]=array("Month"=>"$monthtxt $year","Progress1"=>"$amount_idr_1","Progress2"=>"$amount_idr_2","Progress3"=>"$amount_idr_3",
		"Progress4"=>"$amount_idr_4","Progress5"=>"$amount_idr_5","Progress6"=>"$amount_idr_6");
		
  $min = strtotime("+1 month", $min);
}
echo json_encode($arr);
?>
