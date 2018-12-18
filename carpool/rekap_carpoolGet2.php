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
$f_lkeid=(empty($lke_id))?"":"lke_id='$lke_id' and";

	$result = array();
	$sql="select proj_id,proj_code, proj_name, sum(um) as um, sum(uj) as uj, sum(parking) as parking, sum(portal) as portal, sum(three_in_one) as three_in_one,
	sum(utb) as utb, sum(bbm) as bbm, sum(etoll) as etoll, sum(mtoll) as mtoll,sum(others) as others,sum(uang_pulsa) as uang_pulsa,
	sum(um+uj+parking+portal+three_in_one+utb+bbm+etoll+mtoll+others+uang_pulsa) as total
	from rekap_carpool where $f_lkeid str_date between '$f_start_date' and '$f_end_date' group by proj_code, proj_name,proj_id
	";
	$x=$dbproj->Execute($sql);
	$items=array();
	while($row=$x->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			if(preg_match("#^(portal|parking|um|uj|three_in_one|utb|bbm|etoll|mtoll|others|uang_pulsa|total)#",$key)){
				$$key=number_format($val,0,".",",");
			}else{
				$$key=$val;	
			}
		}
		$proj_code=(empty($proj_code))?"OTHERS":$proj_code;
		$proj_name=(empty($proj_name))?"OTHERS":$proj_name;
	
		$items[]=array("proj_code"=>"$proj_code ","proj_name"=>"$proj_name","uang_pulsa"=>"$uang_pulsa","parking"=>"$parking","portal"=>"$portal","um"=>"$um","uj"=>"$uj","three_in_one"=>"$three_in_one","utb"=>"$utb","bbm"=>"$bbm","etoll"=>"$etoll",
		"mtoll"=>"$mtoll","others"=>"$others","total"=>"$total");
		$total_um=$total_um+str_replace(",","",$um);
		$total_uj=$total_uj+str_replace(",","",$uj);
		$total_uang_pulsa=$total_uang_pulsa+str_replace(",","",$uang_pulsa);
		$total_parking=$total_parking+str_replace(",","",$parking);
		$total_portal=$total_portal+str_replace(",","",$portal);
		$total_three_in_one=$total_three_in_one+str_replace(",","",$three_in_one);
		$total_utb=$total_utb+str_replace(",","",$utb);
		$total_bbm=$total_bbm+str_replace(",","",$bbm);
		$total_etoll=$total_etoll+str_replace(",","",$etoll);
		$total_mtoll=$total_mtoll+str_replace(",","",$mtoll);
		$total_others=$total_others+str_replace(",","",$others);
		$grand_total=$grand_total+str_replace(",","",$total);
	}
	$total_um=number_format($total_um,0,".",",");
	$total_uj=number_format($total_uj,0,".",",");
	$total_parking=number_format($total_parking,0,".",",");
	$total_uang_pulsa=number_format($total_uang_pulsa,0,".",",");
	$total_portal=number_format($total_portal,0,".",",");
	$total_three_in_one=number_format($total_three_in_one,0,".",",");
	$total_utb=number_format($total_utb,0,".",",");
	$total_bbm=number_format($total_bbm,0,".",",");
	$total_etoll=number_format($total_etoll,0,".",",");
	$total_mtoll=number_format($total_mtoll,0,".",",");
	$total_others=number_format($total_others,0,".",",");
	$grand_total=number_format($grand_total,0,".",",");
	$result["footer"]=array(
		array("proj_name"=>"<b>TOTAL</b>","um"=>"<b><span style='color:red'>$total_um</span></b>","uj"=>"<b><span style='color:red'>$total_uj</span></b>",
		"parking"=>"<b><span style='color:red'>$total_parking</span></b>",
		"uang_pulsa"=>"<b><span style='color:red'>$total_uang_pulsa</span></b>",
		"portal"=>"<b><span style='color:red'>$total_portal</span></b>",
		"three_in_one"=>"<b><span style='color:red'>$total_three_in_one</span></b>",
		"utb"=>"<b><span style='color:red'>$total_utb</span></b>",
		"bbm"=>"<b><span style='color:red'>$total_bbm</span></b>",
		"etoll"=>"<b><span style='color:red'>$total_etoll</span></b>",
		"mtoll"=>"<b><span style='color:red'>$total_mtoll</span></b>",
		"others"=>"<b><span style='color:red'>$total_others</span></b>",
		"total"=>"<b><span style='color:black'>$grand_total</span></b>"
		)
	);
	$result["rows"] = $items;
	echo json_encode($result);
}
?>