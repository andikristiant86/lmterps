<?php
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
if($act=='combo_dttype'){
	$items[]=array("dt_type"=>"Site");
	$items[]=array("dt_type"=>"Cluster");
	$items[]=array("dt_type"=>"Cluster and Site");
	$result["rows"] = $items;
	echo json_encode($result);
}
?>