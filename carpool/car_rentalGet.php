<?
include_once($DOCUMENT_ROOT."/s/config.php");
$CreateBy="$login_nip";
$CreateDate=date("Y/m/d");
$lke_id=$db->getOne("select lke_id from spg_data_current where nip='$login_nip'");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'ClientID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
		
	$offset = ($page-1)*$rows;
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'ClientID';
	$find = ($fname=="all")?"and (y.proj_name like '%$q%' or y.proj_code like '%$q%' or x.periode like '%$q%' or z.nm_peg like '%$q%')":"and $fname like '%$q%'";
	
	$result = array();
	$result["total"] = $dbproj->getOne("select count(*) from rental_car x
	left join m_project y on y.id=x.proj_id
	left join spg_data_current z on z.nip=y.pm_id
	where x.lke_id='$lke_id' $find");
	$sql="select x.*,y.proj_name,y.proj_code,z.nm_peg from rental_car x
	left join m_project y on y.id=x.proj_id
	left join spg_data_current z on z.nip=y.pm_id
	where x.lke_id='$lke_id' $find order by x.periode desc";
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$total_costRp=number_format($total_cost,0,".",",");
		$items[]=array("id"=>"$id","periode"=>"$periode","proj_name"=>"$proj_name","proj_code"=>"$proj_code","pm"=>"$nm_peg","total_car"=>"$total_car",
		"total_cost"=>"$total_costRp","proj_id"=>"$proj_id","status"=>"$status"
		);
	}
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='generate_kode'){
	$clientid=$f->generate_nomorkolom("lmt_project.dbo.M_Client","ClientID","CLN");
	$user_input=$db->getOne("select nm_peg from spg_pegawai where nip='$login_nip'");
	$tanggal_input=date("d/m/Y");
	echo json_encode(array('kode'=>"$clientid|$user_input|$tanggal_input"));
}
elseif($act=='do_add'){
	foreach($HTTP_POST_VARS as $key=>$val){
		if(preg_match("#^(tgl|tanggal|tmt|input_date)#",$key)){
			$date=date("Y/m/d");
			$columns .="$key,";
			$values .= "'$date',";
		}elseif(preg_match("#^(user_input)#",$key)){
			$columns .="$key,";
			$values .="'$login_nip',";
		}else{
			$columns .="$key,";
			$values .="'$val',";
		}
	}
	$columns = preg_replace("/,$/","",$columns);
	$values	 = preg_replace("/,$/","",$values);
	$result=$dbproj->Execute("insert into rental_car ($columns,lke_id,create_by,create_date) values ($values,'$lke_id','$login_nip',GETDATE())");
		
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error: insert into m_client ($columns) values ($values)"));
		}
}
elseif($act=='do_update'){
	foreach($HTTP_POST_VARS as $key=>$val){
		if(!preg_match("/^(clientid)$/i",$key)){
			if(preg_match("#^(tgl|tanggal|tmt|input_date)#",$key)){
				$date=date("Y/m/d");
				$list .="$key='$date',";
			}elseif(preg_match("#^(user_input)#",$key)){
				$list .="$key='$login_nip',";
			}else{
				$list .="$key='$val',";
			}
		}
	}
	$columns = preg_replace("/,$/","",$columns);
	$values	 = preg_replace("/,$/","",$values);
	$list	 = preg_replace("/,$/","",$list);
	$result=$dbproj->Execute("update rental_car set $list where id='$id'");
		
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"error"));
		}
}
elseif($act=='do_destroy'){
	$result=$dbproj->Execute("delete rental_car where id='$id'");
		if ($result){
			echo json_encode(array('success'=>true));
		} else {
			echo json_encode(array('errorMsg'=>"Error"));
		}
}elseif($act=='combo_project'){
	$q = isset($_POST['q']) ? strval($_POST['q']) : '';
	$sql="select x.id, x.proj_code, x.proj_name from m_project x 
	where isnull(status,'')='' and (x.proj_code like '%$q%' or x.proj_name like '%$q%')";
	$result_user=$dbproj->Execute($sql);
	$items=array();
	while($row=$result_user->Fetchrow()){
		$items[]=$row;
	}
	$result["rows"] = $items;
	echo json_encode($result);
}
?>