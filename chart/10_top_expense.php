<?php
include "koneksi.php";

	$sql="select top 10 * from tes_aja
	order by amount_total desc";
	$result=$dberps->Execute($sql);
	$arr=array();
	
	while($row=$result->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$proj_code=$dberps->getOne("select a.proj_code from m_project a where a.id='$id'");
		$arr[]=array("Day"=>"$proj_code","Keith"=>"$amount_car","Erica"=>"$amount_salary","George"=>"$amount_pulsa","Others"=>"$amount_others");	
}
echo json_encode($arr);
?>