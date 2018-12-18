<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=empty($nip_sipeg)?$login_nip:$nip_sipeg;
//$login_nipx=empty($nip_sipeg)?$login_nip:$nip_sipeg;
$admin_area=$dbpay->getOne("select count(*) from m_kasir where pic_id='$login_nip' and verify='Y'");
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'OCS_ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
	$offset = ($page-1)*$rows;
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$q = $_POST['value'];
	$result = array();
	
	$sub_where=($admin_area>=1)?"where x.kasir_pic='$login_nip' or x.pemohon='$login_nip'":
	"where x.pemohon='$login_nip'";
	
	if($login_nip=='021452' || $login_nip=='032015'){
		$sub_where="where x.cmp_id='CMP-000001'";
	}
	
	$sql="
	select top 1000 x.*, (select top 1 nm_jenis from t_pengajuan_biaya_jenis where id=x.jenis) as jenis_pengajuan,
	(select top 1 isnull(area,'CABANG') from t_pengajuan_biaya_jenis where id=x.jenis) as area_jenis_pengajuan,
	case when (select proj_name from m_project where id=x.project_id) is null then 'MANAGEMENT' 
		else (select proj_code+' - '+proj_name from m_project where id=x.project_id) end as project_name, 
	(select nm_peg from spg_data_current where nip=x.pemohon) as req_name,
	(select handphone_1 from spg_data_current where nip=x.pemohon) as phone_number,
	(select sum(nominal) from t_pembayaran_biaya_op where pengajuan_id=x.no) as total_dibayar,
	(select max(CONVERT(VARCHAR(24),tgl_bayar,113)) from t_pembayaran_biaya_op where pengajuan_id=x.no) as tgl_bayar, 
	case when x.status=4 or x.status=3 then 'PAYMENT' else 'PAID' end as status_name,
	CONVERT(VARCHAR(24),tanggal_mengetahui,113) as pm_app_date,
	CONVERT(VARCHAR(24),tanggal_disetujui,113) as bc_app_date, c.kasir_area as lokasi_kerja,b.lke_id,d.realisasi as amount_real,d.[back] as amount_back,d.deficit as amount_deficit,d.file_name,
	case 
							when x.status='2' then '032015' 
							when b.pm_id=x.pemohon then isnull(x.next_approval,a.nip_atasan)
							else 
								case when b.PM_ID is not null then b.PM_ID else 
									case when x.status='1' then a.nip_atasan else isnull(x.next_approval,a.nip_atasan) end
								end
							end as next_approval1,
	(select sum(p.nominal) from t_pembayaran_biaya_op p where p.pengajuan_id=x.[no])	as amount_paid, g.posisi
	from t_pengajuan_biaya x 
	left join m_atasan_langsung a on a.nip=x.pemohon
	left join m_project b on b.id=x.project_id
	left join m_project_area c on c.lke_id=x.lokasi
	left join t_pengajuan_biaya_real d on d.pengajuan_id=x.[no]
	left join lmt_hcis.dbo.spg_data_current f on f.nip=x.pemohon
	left join lmt_hcis.dbo.spg_08_jabatan_unit g on g.kd_jabatan+g.kd_unit_org=f.kd_jabatan_str+f.kd_unit_org
	$sub_where order by x.id desc
	";
	
	$where="where --status_name='$status' and  
	(pemohon like '%$q%' or req_name like '%$q%' or [no] like '%$q%' or [project_name] like '%$q%' or status_name like '%$q%')";
	
	$result["total"] = $dbproj->getOne("select count(*) from ($sql) as x $where");
		
	$sql="select * from ($sql) as x $where";
	
	$result_user=$dbproj->SelectLimit($sql,$rows,$offset);
	$items=array();
	while($row=$result_user->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		// $status=($total_dibayar < $jumlah && $total_dibayar > 0)?1001:$status;
		// $status=($total_dibayar > $jumlah)?1002:$status;
		
		// $jum_dibayar	=	$jumlah-$total_dibayar;
		// $total_dibayar	=	($realisasi<=0)?$jum_dibayar:$realisasi;
		$tgl=$f->convert_date($tgl,1);
		$amount=number_format($jumlah,0,".",",");
		$amount_paid=number_format($amount_paid,0,".",",");
		$amount_real=number_format($amount_real,0,".",",");
		$amount_back=number_format($amount_back,0,".",",");
		$amount_deficit=number_format($amount_deficit,0,".",",");
		$url_file_nota=$dbproj->getOne("select top 1 (select nmfile from t_pengajuan_biaya_file where id=x.file_detail) from 
										t_pengajuan_biaya_detail x where x.pengajuan_id='$id'");
		
		$url_file=$dbproj->getOne("select f_upload from t_bukti_transfer_upload where pengajuan_id='$no'");
		
		if(!empty($url_file)){
			$url_file=str_replace(" ","%20","$url_file");
			
			$file_transfer="<a href=/finance/uploads/$url_file> <img src=ftv2doc.gif> </a>";
			
		}
		if(!empty($url_file_nota)){
			$url_file_nota=str_replace(" ","%20","$url_file_nota");
			$file_nota="<a href=/kepegawaian/absensi/uploads/$url_file_nota> <img src=ftv2doc.gif> </a>";
		}
		
		$pm_approved=$db->getOne("select nm_peg from spg_data_current where nip='$mengetahui'");
		$budget_control_approved=$db->getOne("select nm_peg from spg_data_current where nip='$disetujui'");
		
		$detail=$dbproj->getOne("select top 1 rincian_detail from t_pengajuan_biaya_detail where pengajuan_id='$id'");
		$cara_bayar=($cara_bayar==2)?"TRANSFER":"CASH";
		$note=$dbproj->getOne("select top 1 catatan_detail from t_pengajuan_biaya_detail where pengajuan_id='$id'");
		
		$operational_real	=	$dbproj->getOne("select sum(nominal) from t_operational_real where pengajuan_id='$no'");
		$budget_carpool		=	$dbproj->getOne("select sum(nominal) from t_budget_carpool_real where pengajuan_id='$no'");
		$total_transfer		=	($budget_carpool>0)?$budget_carpool:$operational_real;
		$total_transferRp	=	number_format($total_transfer,0,".",",");
		
		$nip_atasan=$dbproj->getOne("select nip_atasan from M_ATASAN_LANGSUNG where nip='$pemohon'");
		$manager=$db->getOne("select nm_peg from spg_data_current where nip='$nip_atasan'");
		
		$verifikasi_datax=empty($verifikasi_data)?"NO":"$verifikasi_data";
		if($verifikasi_datax=='NO' and $status!='3'){
			if($status!='1')$verifikasi_data="<br><font color=red>UNVERIFIED</font>";
		}elseif($verifikasi_datax=='YES' and $status!='3'){
			if($status!='1')$verifikasi_data="<br><font color=green>VERIFIED</font>";
		}else{
			if($status!='1')$verifikasi_data="<br><font color=#663300>NOT PAID</font>";
		}
		$verifikasi_data=$verifikasi_data=='NO'?"":"$verifikasi_data";
		if(empty($next_approval1)){
			$status_approval="WAITING <br>$manager  $verifikasi_data";
		}else{
			if($status==2){
				$status_approval="WAITING <br>LUCKMAN SETIAWAN $verifikasi_data";
			}else{
				$manager=$db->getOne("select nm_peg from spg_data_current where nip='$next_approval1'");
				$status_approval="WAITING <br>$manager $verifikasi_data";
			}
		}
		$reject_by=$db->getOne("select nm_peg from spg_data_current where nip='$reject_by'");
		if($status=='3'){
			$status_approval="WAITING PAYMENT<br>BY CASHIER AREA";
		}
		elseif($status=='6'){
			$status_approval="<font color=blue>PAYMENT DONE</font>";
		}
		elseif($status=='101'){
			$status_approval="REJECT <br>BY $reject_by<br> <font color=red>$reject_reason</font>";
		}
		$lokasi_kas=($lke_id!=$lokasi)?"<b><font color=red>$lokasi_kerja</font></b>":"<b><font color=green>$lokasi_kerja</font></b>";
			$items[]=array("id"=>"$id","tgl"=>"$tgl","nox"=>"$no <br> $tgl","no"=>"$no","project_name"=>"$project_name","req_name"=>"$req_name <br> <font color=#D1D1D1>$posisi</font>",
			"phone_number"=>"$phone_number",
			"jenis"=>"$jenis_pengajuan <br> <font color=#d1d1d1>$detail</font>","amount"=>"$amount","status"=>"$status",
			"tgl_bayar"=>"$tgl_bayar","cara_bayar"=>"$cara_bayar",
			"total_dibayar"=>"$total_transferRp","jum_dibayar"=>"$jum_dibayar","realisasi"=>"$realisasi","note"=>"<b>$jenis_pengajuan</b> <br> $detail<br>
			$lokasi_kas","amount_paid"=>"$amount_paid","amount_real"=>"$amount_real","amount_back"=>"$amount_back","amount_deficit"=>"$amount_deficit",
			"bukti_transfer"=>"[<a href=# onCLick=printx('$no')>DETAIL</a>]","fileToUpload"=>"$file_name",
			"pm_approved"=>"$pm_approved <br> $pm_app_date","budget_control_approved"=>"$bc_app_date","status_approval"=>"$status_approval"
			);
		unset($file_transfer,$file_nota);
	}
	
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_realisasi'){
$status_real=$dbproj->getOne("select status_real from t_pengajuan_biaya where [no]='$no'");
if($status_real==2){
	$dbproj->Execute("update t_pengajuan_biaya set status_real='3' where [no]='$no'");
	echo json_encode(array('success'=>true));die();
}

if(empty($_FILES["fileToUpload"]["name"])){
	echo json_encode(array('success'=>true));
	die();
}

$target_dir = "$DOCUMENT_ROOT/kepegawaian/absensi/uploads/";
$file_name	= time()."_".basename($_FILES["fileToUpload"]["name"]);
$file_arr=explode(".",$file_name);
$file_arr0=base64_encode($file_arr[0]);$file_arr1=$file_arr[1];
$file_name=$file_arr0.".".$file_arr1;
$target_file = $target_dir.$file_name;

$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
// Check if image file is a actual image or fake image
// if(isset($_POST["submit"])) {
    // $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    // if($check !== false) {
		// echo json_encode(array('errorMsg'=>"File is an image - " . $check["mime"] . "."));
        // die();
    // } else {
		// echo json_encode(array('errorMsg'=>"File is not an image."));
        // die();
    // }
// }
// Check if file already exists


if (file_exists($target_file)) {
    
	echo json_encode(array('errorMsg'=>"Sorry, file already exists."));
    die();
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > (2*1048576)) {
	echo json_encode(array('errorMsg'=>"Sorry, max size upload 2Mb!"));
    die();
}
// Allow certain file formats
if($imageFileType != "zip" && $imageFileType != "rar" && $imageFileType != "ZIP" && $imageFileType != "RAR") {
	echo json_encode(array('errorMsg'=>"Sorry, only ZIP, RAR files are allowed."));
    die();
}
move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

	$cek_count=$dbproj->getOne("select count(*) from t_pengajuan_biaya_real where pengajuan_id='$no'");
	if($cek_count==0){
		$dbproj->Execute("insert into t_pengajuan_biaya_real (pengajuan_id,paid,realisasi,back,update_by,update_date,deficit,file_name) 
		values ('$no','$amount_paid','$amount_real','$amount_back','$login_nip',GETDATE(),'$amount_deficit','$file_name')");
	}else{
		$dbproj->Execute("update t_pengajuan_biaya_real set paid='$amount_paid',realisasi='$amount_real',back='$amount_back',update_by='$login_nip',update_date=GETDATE(),
		deficit='$amount_deficit',file_name='$file_name' where pengajuan_id='$no'");
	}
	$dbproj->Execute("update t_pengajuan_biaya set status_real='2' where [no]='$no'");
	echo json_encode(array('success'=>true));
}
elseif($act=='do_approved'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$pengajuan_id=$dbproj->getOne("select [no] from t_pengajuan_biaya where id='$val'");
		$dbproj->Execute("update t_pengajuan_biaya set status_real='3' where id='$val'");
		$dbproj->Execute("update t_pengajuan_biaya_real set status='YES' where pengajuan_id='$pengajuan_id'");
	}
	echo json_encode(array('success'=>true));
}
elseif($act=='do_reject'){

	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$pengajuan_id=$dbproj->getOne("select [no] from t_pengajuan_biaya where id='$val'");
		$status=$dbproj->getOne("select [status] from t_pengajuan_biaya where id='$val'");
		if($status=='6' || $status=='5' || $status=='3' || $status=='101'){
			echo json_encode(array('errorMsg'=>"No permission reject this data!"));
			die();
		}
		$dbproj->Execute("update t_pengajuan_biaya set status_real='1' where id='$val'");
		$dbproj->Execute("update t_pengajuan_biaya_real set status='NO', reject_reason='$reason' where pengajuan_id='$pengajuan_id'");
	}
	
	echo json_encode(array('success'=>true));
}
elseif($act=='do_reject1'){
	
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$pengajuan_id=$dbproj->getOne("select [no] from t_pengajuan_biaya where id='$val'");
		$status=$dbproj->getOne("select [status] from t_pengajuan_biaya where id='$val'");
		if($status=='6' || $status=='5' || $status=='3' || $status=='101'){
			echo json_encode(array('errorMsg'=>"No permission reject this data!"));
			die();
		}
		$dbproj->Execute("update t_pengajuan_biaya set status='101',reject_reason='$reason',reject_date=GETDATE(),reject_by='$login_nip' where id='$val'");
	}
	echo json_encode(array('success'=>true));
}
?>