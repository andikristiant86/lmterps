<?php 
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$nip=$dbproj->getOne("select pemohon from t_pengajuan_biaya where no='$pengajuan_id'");
$id=$dbproj->getOne("select id from t_pengajuan_biaya where no='$pengajuan_id'");
$nm_peg=$db->getOne("select nm_peg from spg_data_current where nip='$nip'");
$jumlah=$dbproj->getOne("select jumlah from t_pengajuan_biaya where no='$pengajuan_id'");
$jumlahRp=number_format($jumlah,0,".",",");
$proj_id=$dbproj->getOne("select project_id from t_pengajuan_biaya where no='$pengajuan_id'");
$proj_name=$dbproj->getOne("select case when isnull(proj_name,'')='' then 'Management' else proj_name end from m_project where id='$proj_id'");
$proj_code=$dbproj->getOne("select case when isnull(proj_code,'')='' then 'Management' else proj_code end from m_project where id='$proj_id'");	
$bank=$dbproj->getOne("select (select bank from lmt_hcis.dbo.tbl_bank where bank_id=t_pengajuan_biaya.bank_id) from t_pengajuan_biaya where no='$pengajuan_id'");
$no_rek=$dbproj->getOne("select no_rek from t_pengajuan_biaya where no='$pengajuan_id'");
$nama_rek=$dbproj->getOne("select nama_rek from t_pengajuan_biaya where no='$pengajuan_id'");
$cara_bayar=$dbproj->getOne("select cara_bayar from t_pengajuan_biaya where no='$pengajuan_id'");
?>
<script type="text/javascript">
<!--
function printPartOfPage(elementId)
{
 var printContent = document.getElementById(elementId);
 var windowUrl = 'about:blank';
 var uniqueName = new Date();
 var windowName = 'Print' + uniqueName.getTime();

 var printWindow = window.open(windowUrl, windowName, 'left=50000,top=50000,width=0,height=0');

 printWindow.document.write(printContent.innerHTML);
 printWindow.document.close();
 printWindow.focus();
 printWindow.print();
 printWindow.close();
}
// -->
</script>

<div id="printDiv">
<style>
.boldtable, .boldtable TD
{
	font-family:sans-serif;
	font-size:9pt;
}
.boldtable TH{
	font-family:sans-serif;
	font-size:12pt;
	background-color:#dddddd;
}.tableTH TH{
	font-family:sans-serif;
	font-size:9pt;
	background-color:#dddddd;
}.tableTH, .tableTH TD
{
	font-family:sans-serif;
	font-size:9pt;
}
</style>
<img src="LMT.jpg" width="120" height="60">
<table width="100%" CLASS="boldtable" border=0 cellspacing=1 cellpadding=1>
<tr>
	<th align="left" colspan="6">
		DETAIL SUBMISSION COST
	</th>
</tr>

<tr>
		<td width="10%">NIP/Name</td> <td width="40%">: <?=$nip?> / <?=$nm_peg;?></td> <td>Date</td><td>: <?=date("d/m/Y");?></td>
</tr>
<tr>
		<td>Amount</td> <td>: <?=$jumlahRp;?></td> <td>Project Code</td><td>: <?=$proj_code;?></td>
</tr>
<tr>
		<td>No</td> <td>: <?=$pengajuan_id;?></td> <td>Project Name</td><td>: <?=$proj_name;?></td>
</tr>
</table>

<table width="100%" CLASS="boldtable">
<tr>
	<td colspan="6">
		<b>DETAIL INFORMATION</b>
	</td>
</tr>
</table>
<table width="100%" CLASS="tableTH" border=0 cellspacing=1 cellpadding=1>
<tr>
	<th width="5%" align="center">No</th><th width="30%">Details</th><th width="45%">Note</th><th align="right">Amount</th>
</tr>
<?php
	$sql1="select rincian_detail,catatan_detail,jumlah_detail from t_pengajuan_biaya_detail where pengajuan_id='$id'";
	$resultx=$dbproj->Execute($sql1);
	$nox=1;
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$jumlah_detailRp=number_format($jumlah_detail,0,".",",");
?>
<tr>
	<td align="center"><?=$nox?></td><td><?=$rincian_detail;?></td><td><?=$catatan_detail;?></td><td align="right"><?=$jumlah_detailRp;?></td>
</tr>
<?php
$nox++;
}

$total_paid=$dbproj->getOne("select sum(nominal) from t_pembayaran_biaya_op where pengajuan_id='$pengajuan_id'");
$total_paidRp=number_format($total_paid,0,".",",");

$total_real=$dbproj->getOne("select sum(realisasi) from t_pengajuan_biaya where [no]='$pengajuan_id'");
$total_real=number_format($total_real,0,".",",");

?>
<tr>
	<th><br></th><th></th><th></th><th></th>
</tr>
<tr>
	<td></td><td></td><td><B>Paid</B></td><td align="right"><b><?=$total_paidRp;?></b></td>
</tr>
</table>
Download Attach Fiels:<br>
<?php
	$sql2="select (select nmfile from t_pengajuan_biaya_file where id=x.file_detail) as nmfile from 
										t_pengajuan_biaya_detail x where x.pengajuan_id='$id'";
	$resultx=$dbproj->Execute($sql2);
	$files_to_zip=array();
	while($row=$resultx->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$files_to_zip[]=$nmfile;
?>
	<a href=# > <?=$pengajuan_id;?>.zip </a><br>
<?php
}

function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file,$file);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

echo json_encode($files_to_zip);

echo $result = create_zip($files_to_zip,$pengajuan_id.".zip");

if ($cara_bayar=='2'){
?>
<br>
<table CLASS="boldtable" border=0 cellspacing=1 cellpadding=1>
	<tr> <td> Transfer To</td></tr>
	<tr> <td> <?=$bank;?></td></tr>
	<tr> <td> No A/C <?=$no_rek;?></td></tr>
	<tr> <td> <b><u><?=strtoupper($nama_rek);?></u></b></td></tr>
</table>
<?}?>
</div>