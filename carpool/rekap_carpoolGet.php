<?php
include_once($DOCUMENT_ROOT."/s/config.php");
if($act=='combo_pm'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select nip, nm_peg from m_project_manager where nip like '%$q%' or nm_peg like '%$q%'";
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
	$sql="select * from rekap_carpool where $f_lkeid str_date between '$f_start_date' and '$f_end_date'";
	$x=$dbproj->Execute($sql);
	$items=array();
	while($row=$x->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			if(preg_match("#^(km_start|portal|km_end|parking|um|uj|three_in_one|utb|bbm|bbm_ltr|etoll|mtoll|others|uang_pulsa)#",$key)){
				$$key=number_format($val,0,".",",");
			}else{
				$$key=$val;	
			}
		}
		$totalx=str_replace(",","",$uang_pulsa)+str_replace(",","",$um)+str_replace(",","",$uj)+str_replace(",","",$parking)+str_replace(",","",$portal)+str_replace(",","",$three_in_one)+
		str_replace(",","",$utb)+str_replace(",","",$bbm)+str_replace(",","",$etoll)+str_replace(",","",$mtoll)+str_replace(",","",$others);
		$total=number_format($totalx,0,".",",");
		$date_berangkat=$f->convert_date($date_berangkat,1);
		$items[]=array("proj_code"=>"$proj_code ","proj_name"=>"$proj_name","ocs_id"=>"$ocs_id","car_number"=>"$car_number","namaSP"=>"$namasp","km_start"=>"$km_start","km_end"=>"$km_end","remaks"=>"$remaks",
		"parking"=>"$parking","portal"=>"$portal","um"=>"$um","uj"=>"$uj","three_in_one"=>"$three_in_one","utb"=>"$utb","bbm"=>"$bbm","bbm_ltr"=>"$bbm_ltr","id_etoll"=>"$id_etoll","etoll"=>"$etoll",
		"mtoll"=>"$mtoll","others"=>"$others","description"=>"$description","date_berangkat"=>"$date_berangkat","total"=>"$total","uang_pulsa"=>"$uang_pulsa",
		"pm_name"=>"$pm_name","dt_coord"=>"$dt_coord","dt_name"=>"$dt_name");
		$total_up=$total_up+str_replace(",","",$uang_pulsa);
		$total_um=$total_um+str_replace(",","",$um);
		$total_uj=$total_uj+str_replace(",","",$uj);
		$total_parking=$total_parking+str_replace(",","",$parking);
		$total_portal=$total_portal+str_replace(",","",$portal);
		$total_three_in_one=$total_three_in_one+str_replace(",","",$three_in_one);
		$total_utb=$total_utb+str_replace(",","",$utb);
		$total_bbm=$total_bbm+str_replace(",","",$bbm);
		$total_etoll=$total_etoll+str_replace(",","",$etoll);
		$total_mtoll=$total_mtoll+str_replace(",","",$mtoll);
		$total_others=$total_others+str_replace(",","",$others);
		$grand_total=$grand_total+$totalx;
	}
	$total_up=number_format($total_up,0,".",",");
	$total_um=number_format($total_um,0,".",",");
	$total_uj=number_format($total_uj,0,".",",");
	$total_parking=number_format($total_parking,0,".",",");
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
		"portal"=>"<b><span style='color:red'>$total_portal</span></b>",
		"three_in_one"=>"<b><span style='color:red'>$total_three_in_one</span></b>",
		"utb"=>"<b><span style='color:red'>$total_utb</span></b>",
		"bbm"=>"<b><span style='color:red'>$total_bbm</span></b>",
		"etoll"=>"<b><span style='color:red'>$total_etoll</span></b>",
		"mtoll"=>"<b><span style='color:red'>$total_mtoll</span></b>",
		"others"=>"<b><span style='color:red'>$total_others</span></b>",
		"total"=>"<b><span style='color:black'>$grand_total</span></b>",
		"uang_pulsa"=>"<b><span style='color:black'>$total_up</span></b>"
		)
	);
	$result["rows"] = $items;
	echo json_encode($result);
}
?>