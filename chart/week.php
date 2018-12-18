<?
include "koneksi.php";
$max_periode		=	date("Ym");
$jum_star			= 	0 ;
$jum_end			=	1;
$max_thn			=	substr($max_periode,0,4);
$max_bln			=	substr($max_periode,4,6);
$max_periode_strt	=	date("Y-m-d", mktime(0, 0, 0, $max_bln+$jum_star, 1, $max_thn));
$max_periode_end	=	date("Y-m-d", mktime(0, 0, 0, $max_bln+$jum_end, 1, $max_thn));
$start 				= 	strtotime($max_periode_strt);
$end 				= 	strtotime($max_periode_end);
						
while($start < $end)
{
	$week 				= 	date('W', $start);
  
  $sql_1="select top 1 amount_idr_1 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_WEEKLY' and isnull(uncheck,'N') in ('N') and 
			periode='W-$week'";
  $amount_idr_1=$dberps->getOne($sql_1);
  
  $sql_2="select top 1 amount_idr_2 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_WEEKLY' and isnull(uncheck,'N') in ('N') and 
			periode='W-$week'";
  $amount_idr_2=$dberps->getOne($sql_2);
  
  $sql_3="select top 1 amount_idr_3 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_WEEKLY' and isnull(uncheck,'N') in ('N') and 
			periode='W-$week'";
  $amount_idr_3=$dberps->getOne($sql_3);
  
  $sql_4="select top 1 amount_idr_4 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_WEEKLY' and isnull(uncheck,'N') in ('N') and 
			periode='W-$week'";
  $amount_idr_4=$dberps->getOne($sql_4);
  
  $sql_5="select top 1 amount_idr_5 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_WEEKLY' and isnull(uncheck,'N') in ('N') and 
			periode='W-$week'";
  $amount_idr_5=$dberps->getOne($sql_5);
  
  $sql_6="select top 1 amount_idr_6 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_WEEKLY' and isnull(uncheck,'N') in ('N') and 
			periode='W-$week'";
  $amount_idr_6=$dberps->getOne($sql_6);
  
  $arr[]=array("Week"=>"W-$week","Progress1"=>"$amount_idr_1","Progress2"=>"$amount_idr_2","Progress3"=>"$amount_idr_3",
		"Progress4"=>"$amount_idr_4","Progress5"=>"$amount_idr_5","Progress6"=>"$amount_idr_6");
		
		$start = strtotime("+1 week", $start);
	}
	echo json_encode($arr);
?>