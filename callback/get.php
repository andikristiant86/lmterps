<?php
include_once($DOCUMENT_ROOT."/s/database.php");
include "$DOCUMENT_ROOT/classes/adodb/adodb.inc.php";

$db =& ADONewConnection('mssqlnative');
$db->PConnect($dbhostname, $dbusername, $dbpassword, $dbname[3]);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

$count=$db->getOne("select count(*) from absensi_fprint where nip='".$_POST['nip']."' and fDateTIme='".$_POST['checkinout']."'");
if($count==0){
	$db->Execute("insert into absensi_fprint(nip,fDateTIme,machineid,fInOut) values ('".$_POST['nip']."','".$_POST['checkinout']."','2','".$_POST['status']."')");
}

$status=$_POST['status']==1?"In":"Out";
$data[] = array("nip"=>$_POST['nip'],"datetime"=>$_POST['checkinout'],"status"=>$status);
echo json_encode($data);
?>