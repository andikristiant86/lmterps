<?php
	include "koneksi.php";
	$sql="select * from lmt_project.dbo.dashboard_notice_admin";
	$result=$dberps->Execute($sql);
	$arr=array();
	
	while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$arr_peg=explode(" ",$nm_peg);
		$nm_peg=empty($arr_peg[1])?$arr_peg[0]:$arr_peg[1];
		$arr[]=array("PIC"=>"$nm_peg","notice1"=>"$noticetoprint","notice2"=>"$sendesartoregional","notice3"=>"$esarongoingtoregion","notice4"=>"$esarongoingtohq","notice5"=>"$submitesartoar");
	}
	
echo json_encode($arr);

?>