<?php 
$date=date("Ym");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=Request_Pulsa_$date.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");

$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;

$f_start_date	=	$_REQUEST['f_start_date'];
$f_start_date	= 	(empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
$f_end_date		=	$_REQUEST['f_end_date'];
$f_end_date		= 	(empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
$sql="
select x.*, 
	(select nm_peg from SPG_DATA_CURRENT where nip=x.REQ_NIP) as REQ_NAME,
	(select nm_peg from SPG_DATA_CURRENT where nip=x.CREATE_BY) as DT_COOR,
	(SELECT PROJ_CODE from M_PROJECT where ID=x.PROJ_ID) as PROJ_CODE,
	case STS_APP_PM WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_PM_NM,
	case STS_APP_FINANCE WHEN 0 then 'NOT APPROVED' WHEN 1 THEN 'APPROVED' ELSE 'REJECT' END AS STS_APP_FINANCE_NM
	from M_REQUEST_PULSA x where lke_id in (select area from m_topup_admin where admin='$login_nip') and req_date between '$f_start_date' and '$f_end_date' AND STATUS_APPROVAL='CLOSED'";
$sqlExc=$dbproj->Execute($sql);
?>
<style>
.boldtable, .boldtable TD, .boldtable TH
{
	font-family:sans-serif;
	font-size:9pt;
}
</style>
<table border="1" CLASS="boldtable">
        <thead>
			<tr>
				<th width="100">CRP ID</th>
				<th width="150">DATE</th>
				<th width="200">REQ NAME</th>
				<th width="200">DT COORDINATOR</th>
				<th width="150">PROJECT CODE</th>
				<th width="100">PHONE NUMBER</th>
				<th width="200">DESCRIPTION</th>
				<th width="100">PULSA</th>
				<th width="100">STATUS</th>
				
			</tr>
        </thead>
<tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$req_date=$f->convert_date($req_date,1);
		$amount=number_format($amount,0,".",",");
?>
			<style> .f_text{ mso-number-format:\@; } </style>
			<tr>
				<td><?=$ocs_id;?></td>
				<td><?=$req_date;?></td>
				<td><?=$req_name;?></td>
				<td><?=$dt_coor;?></td>
				<td><?=$proj_code;?></td>
				<td class='f_text'><?=$phone_number;?></td>
				<td><?=$description;?></td>
				<td><?=$amount;?></td>
				<td><?=$status_topup;?></td>
			</tr>
<?
}
?>
		<tbody>
</table>