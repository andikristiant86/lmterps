<?php
include "koneksi.php";
	$sql="select * from dashboard_notice_finance";
	$result=$dberps->Execute($sql);
	$arr=array();
	
	while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$arr[]=array("pic"=>"$nm_peg","notice1"=>"$esaracceptancebyar","notice2"=>"$submitesartoscs","notice3"=>"$waitingvsapproval","notice4"=>"$submitinvoice");
	}
	
echo json_encode($arr);

?>