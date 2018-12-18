<?php
include_once($DOCUMENT_ROOT."/s/database.php");
include "$DOCUMENT_ROOT/classes/adodb/adodb.inc.php";

$db =& ADONewConnection('mssqlnative');
$db->PConnect($dbhostname, $dbusername, $dbpassword, $dbname[3]);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

$cek_nip=$db->getOne('select count(*) from lmt_hcis.dbo.spg_data_current where kd_jabatan_str+kd_unit_org=8011040102010000000 and isnull(sts_pensiun,0)=0 and nip='.$_POST['userid'].'');

if(empty($_POST['userid'])) {echo json_encode(array('error'=>'Gagal, userid wajib diisi!'));}

elseif($cek_nip==0) {echo json_encode(array('error'=>'Gagal, userid tidak terdaftar!'));}

else {
		$db->Execute("insert into lmt_timesheet.dbo.absensi_fprint values ('$_POST[userid]',GETDATE(),'100',NULL,'$_POST[fInOut]')");
		echo json_encode(array('success'=>'Absensi berhasil disimpan...'));
}
?>