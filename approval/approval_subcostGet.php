<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;

$cmp_id=$db->getOne("select cmp_id from spg_data_current where nip='$login_nip'");
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
	
	$sub_where=($admin_area>=1)?"where x.verifikasi_data in ('NO') and x.cmp_id='CMP-000001'
						and x.kasir_pic = '$login_nip' and x.status not in (1,101,3,4,6)":
						"where x.status in (1,2,22) and 
						case 
							when x.status='2' then '032015' 
							when b.pm_id=x.pemohon then isnull(x.next_approval,a.nip_atasan)
							else 
								case when b.PM_ID is not null then b.PM_ID else 
									case when x.status='1' then a.nip_atasan else isnull(x.next_approval,a.nip_atasan) end
								end
							end='$login_nip' and x.cmp_id='CMP-000001'";
	
	$sql="
	select x.*, (select top 1 nm_jenis from t_pengajuan_biaya_jenis where id=x.jenis) as jenis_pengajuan,
	(select top 1 isnull(area,'CABANG') from t_pengajuan_biaya_jenis where id=x.jenis) as area_jenis_pengajuan,
	case when (select proj_name from m_project where id=x.project_id) is null then 'MANAGEMENT' 
		else (select proj_code+' - '+proj_name from m_project where id=x.project_id) end as project_name, 
	(select nm_peg from spg_data_current where nip=x.pemohon) as req_name,
	(select handphone_1 from spg_data_current where nip=x.pemohon) as phone_number,
	(select sum(nominal) from t_pembayaran_biaya_op where pengajuan_id=x.no) as total_dibayar,
	(select max(CONVERT(VARCHAR(24),tgl_bayar,113)) from t_pembayaran_biaya_op where pengajuan_id=x.no) as tgl_bayar, 
	case when x.status=4 or x.status=3 then 'PAYMENT' else 'PAID' end as status_name,
	CONVERT(VARCHAR(24),tanggal_mengetahui,113) as pm_app_date,
	CONVERT(VARCHAR(24),tanggal_disetujui,113) as bc_app_date, c.lokasi_kerja,b.lke_id,
	case 
							when x.status='2' then '032015' 
							when b.pm_id=x.pemohon then case when x.next_approval='' then a.nip_atasan else isnull(x.next_approval,a.nip_atasan) end
							else 
								case when b.PM_ID is not null then b.PM_ID else 
									case when x.status='1' then a.nip_atasan else isnull(x.next_approval,a.nip_atasan) end
								end
							end as next_approval1,
	case when x.verifikasi_data='NO' and x.status='2' then 1 else 2 end as urutan,g.posisi
	from t_pengajuan_biaya x 
	left join m_atasan_langsung a on a.nip=x.pemohon
	left join m_project b on b.id=x.project_id
	left join m_project_area c on c.lke_id=x.lokasi
	left join lmt_hcis.dbo.spg_data_current f on f.nip=x.pemohon
	left join lmt_hcis.dbo.spg_08_jabatan_unit g on g.kd_jabatan+g.kd_unit_org=f.kd_jabatan_str+f.kd_unit_org
	$sub_where
	";
	
	$where="where --status_name='$status' and  
	(pemohon like '%$q%' or req_name like '%$q%' or [no] like '%$q%' or [project_name] like '%$q%' or status_name like '%$q%')";
	
	$result["total"] = $dbproj->getOne("select count(*) from ($sql) as x $where");
		
	$sql="select * from ($sql) as x $where order by x.urutan asc, x.id desc";
	
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
		
		//$tgl_bayar=$f->convert_date($tgl_bayar,1);
		$jum_dibayar=number_format($jum_dibayar,0,".",",");
		$realisasi=number_format($realisasi,0,".",",");
		
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
			if($status==1){
				$status_approval="WAITING <br>$manager";
			}else{
				$status_approval="WAITING <br>$manager  $verifikasi_data";
			}
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
			$status_approval="WAITING PAYMENT<br>BY KHASIR AREA";
		}
		elseif($status=='6'){
			$status_approval="<font color=blue>PAYMENT DONE</font>";
		}
		
		elseif($status=='101'){
			$status_approval="REJECT <br>BY $reject_by<br> <font color=red>$reject_reason</font>";
		}
		$lokasi_kas=($lke_id!=$lokasi)?"<b><font color=red>$lokasi_kerja</font></b>":"<b><font color=green>$lokasi_kerja</font></b>";
			
			$note_64=base64_encode("<b>$jenis_pengajuan</b> <br> $detail");
			$items[]=array("id"=>"$id","tgl"=>"$tgl","nox"=>"$no <br> $tgl","no"=>"$no","project_name"=>"$project_name","req_name"=>"$req_name <br> <font color=#D1D1D1>$posisi</font>",
			"phone_number"=>"$phone_number",
			"jenis"=>"$jenis_pengajuan","amount"=>"$amount","status"=>"$status",
			"tgl_bayar"=>"$tgl_bayar","cara_bayar"=>"$cara_bayar",
			"total_dibayar"=>"$total_transferRp","jum_dibayar"=>"$jum_dibayar","realisasi"=>"$realisasi","note"=>"$note_64",
			"bukti_transfer"=>"[<a href=# onCLick=printx('$no')>DETAIL</a>]",
			"pm_approved"=>"$pm_approved <br> $pm_app_date","budget_control_approved"=>"$bc_app_date","status_approval"=>"$status_approval"
			);
		unset($file_transfer,$file_nota);
	}
	
	$result["rows"] = $items;
	echo json_encode($result);
}elseif($act=='do_reject'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		$dbproj->Execute("update t_pengajuan_biaya set status='101',update_by='$login_nip',tanggal_update=GETDATE(),reject_by='$login_nip',reject_date=GETDATE(),reject_reason='$reason'
		where id='$val'");
	}
	echo json_encode(array('success'=>true));
}
elseif($act=='do_approved'){
	$rows=explode(",",$id);
	foreach ($rows as $key=>$val){
		// $pemohon=$dbproj->getOne("select pemohon from t_pengajuan_biaya where id='$val'");
		// if($pemohon==$login_nip){
			// if($login_nip!='021452' || $login_nip!='032015'){
				// echo json_encode(array('errorMsg'=>"Silahkan hubungi atasan langsung untuk persetujuan!!!"));
				// die();
			// }
		// }
		$status=$dbproj->getOne("select case when mengetahui is null then 1 else status end from t_pengajuan_biaya where id='$val'");
		if($admin_area>=1){
			$dbproj->Execute("update t_pengajuan_biaya set verifikasi_data='YES' where id='$val'");
		}
		if($status=='1'){
			if($status=='1' and $login_nip=='032015'){
				$dbproj->Execute("update t_pengajuan_biaya set status='3',disetujui='$login_nip',tanggal_disetujui=GETDATE(), update_by='$login_nip',
				tanggal_update=GETDATE() where id='$val'");
			}else{
				$dbproj->Execute("update t_pengajuan_biaya set status='2',mengetahui='$login_nip',tanggal_mengetahui=GETDATE(), next_approval='032015', 
				update_by='$login_nip',tanggal_update=GETDATE(),verifikasi_data='NO' where id='$val'");
			}
		}
		elseif($status=='2' and $login_nip=='032015'){
			$dbproj->Execute("update t_pengajuan_biaya set status='3',disetujui='$login_nip',tanggal_disetujui=GETDATE(), update_by='$login_nip',
			tanggal_update=GETDATE() where id='$val'");
		}
	}
	echo json_encode(array('success'=>true));
}

?>