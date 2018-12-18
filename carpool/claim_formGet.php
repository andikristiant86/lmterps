<?php
include_once($DOCUMENT_ROOT."/s/config.php");
if($act=='combo_pm'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select * from(
						select nip, nm_peg,kd_pm, sts_pensiun from spg_data_current where kd_unit_org='1030102000000000'
						union
						select nip, nm_peg,kd_pm,sts_pensiun from spg_data_current2 where kd_unit_org='1030102000000000') as x
						where nip like '%$q%' or nm_peg like '%$q%'";
	$result_user=$db->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	
	echo json_encode($result);
}elseif($act=='combo_project'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select * from m_project where pm_id='$nip' and (proj_code like '%$q%' or proj_name like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='cetak'){
$find.=(empty($pm_id))?"":"and pm_id like '%$pm_id%'";
$find.=(empty($proj_id))?"":"and proj_id like '%$proj_id%'";

	$result = array();
	$sql="select * from (
	select 
	(select proj_id from m_carpool b where b.ocs_id=a.ocs_id) as proj_id,
	(select (select c.proj_code from m_project c where c.id=b.proj_id) from m_carpool b where b.ocs_id=a.ocs_id) as proj_code,
	(select (select c.proj_name from m_project c where c.id=b.proj_id) from m_carpool b where b.ocs_id=a.ocs_id) as proj_name,
	(select (select c.pm_id from m_project c where c.id=b.proj_id) from m_carpool b where b.ocs_id=a.ocs_id) as pm_id,
	(select c.nm_peg from spg_data_current c where c.nip=a.nip_driver) as namasp,
	(select c.no_polisi from inv_data_kendaraan c where c.kode_kendaraan=a.car_number) as nopolis,
	a.* from t_carpool a
	) as x
	where 1=1 $find";
	$x=$dbproj->Execute($sql);
	$items=array();
	while($row=$x->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			if(preg_match("#^(km_start|portal|km_end|parking|um|uj|three_in_one|utb|bbm|bbm_ltr|etoll|mtoll|others)#",$key)){
				$$key=number_format($val,0,".",",");
			}else{
				$$key=$val;	
			}
		}
		$date_berangkat=$f->convert_date($date_berangkat,1);
		$items[]=array("proj_code"=>"$proj_code ","proj_name"=>"$proj_name","ocs_id"=>"$ocs_id","car_number"=>"$car_number","namaSP"=>"$namasp","km_start"=>"$km_start","km_end"=>"$km_end","remaks"=>"$remaks",
		"parking"=>"$parking","portal"=>"$portal","um"=>"$um","uj"=>"$uj","three_in_one"=>"$three_in_one","utb"=>"$utb","bbm"=>"$bbm","bbm_ltr"=>"$bbm_ltr","id_etoll"=>"$id_etoll","etoll"=>"$etoll","mtoll"=>"$mtoll","others"=>"$others","description"=>"$description","date_berangkat"=>"$date_berangkat");
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
?>