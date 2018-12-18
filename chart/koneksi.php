<?php 
include "$DOCUMENT_ROOT/classes/adodb/adodb.inc.php";
$dberps =& ADONewConnection('mssqlnative');
$dberps->PConnect("10.10.10.200","chart", "@wSx#eDc", "data_warehouse");
$dberps->SetFetchMode(ADODB_FETCH_ASSOC);
?>