<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;

$dbmy		= ADONewConnection('mysql');
$dbmy->PConnect("10.10.10.202:3307", "barcode", "Djisamsoe5","barcode");
$dbmy->SetFetchMode(ADODB_FETCH_ASSOC); 


	
if($act=='view'){
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
	$rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
	$sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'ID';
	$order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
	if($sort=='ID'){
		$sort='ID';
	}elseif($sort=='DATE'){
		$sort='DATE';
	}
	
	$offset = ($page-1)*$rows;
	
	$f_start_date	=	$_REQUEST['f_start_date'];
	$f_start_date	= (empty($f_start_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_start_date,1,'/'));
	$f_end_date		=	$_REQUEST['f_end_date'];
	$f_end_date	= (empty($f_end_date))?date("Y-m-d"):str_replace('/','-',$f->convert_date($f_end_date,1,'/'));
	
	$q = $_POST['value'];
	$fname=isset($_POST['name']) ? strval($_POST['name']) : 'barcode';
	$find = ($fname=="all")?"and (barcode  like '%$q%' OR status like '%$q%'
	)":"and $fname like '%$q%'";
	
	$sqlttl="SELECT count(*) FROM (
	select x.id,
concat(
  CASE 
  when d1.barcode is not null then 
     case 
     when d1.sts=0 then concat('R: ',d1.barcode,'(',d1.driver,')<br>')
     when d1.sts=1 then concat('D: ',d1.barcode,'(',d1.driver,')<br>')
     when d1.sts=2 then concat('M: ',d1.barcode,'(',d1.driver,')<br>') 
     else concat(d1.barcode,'(',d1.driver,') ')
     end
  ELSE
     case when x.nip1 is not null then concat('X:',x.nip1,'<br>')
     else '' end
  end,
  CASE 
  when d2.barcode is not null then 
     case 
     when d2.sts=0 then concat('R: ',d2.barcode,'(',d2.driver,')<br>')
     when d2.sts=1 then concat('D: ',d2.barcode,'(',d2.driver,')<br>')
     when d2.sts=2 then concat('M: ',d2.barcode,'(',d2.driver,')<br>') 
     else concat(d2.barcode,'(',d2.driver,') ')
     end
  ELSE
     case when x.nip2 is not null then concat('X:',x.nip2,'<br>')
     else '' end
  end,
  CASE 
  when d3.barcode is not null then 
     case 
     when d3.sts=0 then concat('R: ',d3.barcode,'(',d3.driver,')<br>')
     when d3.sts=1 then concat('D: ',d3.barcode,'(',d3.driver,')<br>')
     when d3.sts=2 then concat('M: ',d3.barcode,'(',d3.driver,')<br>') 
     else concat(d3.barcode,'(',d3.driver,') ')
     end
  ELSE
     case when x.nip3 is not null then concat('X:',x.nip3,'<br>')
     else '' end
  end
) as barcode,
x.date,case when x.status='M' then 'Masuk' when x.status='K' then 'Keluar' end status ,x.nmfile
from (
select p.id,p.barcode,p.date,p.status,f.nmfile,
  barcode_1 as nip1,barcode_2 as nip2,barcode_3 as nip3
from m_parkir p 
left join m_parkir_file f on f.parkir_id=p.id
where nmfile!='' and barcode!='null'
order by p.date desc 
) x
left join m_driver d1 on d1.barcode=x.nip1
left join m_driver d2 on d2.barcode=x.nip2
left join m_driver d3 on d3.barcode=x.nip3) AS Y 
where date>=STR_TO_DATE('$f_start_date','%Y-%m-%d') and date < DATE_ADD(STR_TO_DATE('$f_end_date','%Y-%m-%d'),INTERVAL 1 DAY) $find";

//echo nl2br($sqlttl); 
/*
SUBSTRING_INDEX(p.barcode,'_',1) as nip1,
  case 
  when POSITION('_' IN p.barcode)>1 then 
	SUBSTR(p.barcode,POSITION('_' IN SUBSTRING_INDEX(p.barcode,'_',2))+1,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))-LENGTH(SUBSTRING_INDEX(p.barcode,'_',1))-1) 
  end as nip2,
  case 
  when LENGTH(SUBSTRING_INDEX(p.barcode,'_',2)) < LENGTH(p.barcode) then 
	SUBSTR(p.barcode,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))+2) 
  end as nip3
*/

	$result = array();
	$result["total"]= $dbmy->getOne($sqlttl);
	//echo $result["total"];	
	$sql="SELECT Y.* FROM (
	select x.id,
concat(
  CASE 
  when d1.barcode is not null then 
     case 
     when d1.sts=0 then concat('R: ',d1.barcode,'(',d1.driver,')<br>')
     when d1.sts=1 then concat('D: ',d1.barcode,'(',d1.driver,')<br>')
     when d1.sts=2 then concat('M: ',d1.barcode,'(',d1.driver,')<br>') 
     else concat(d1.barcode,'(',d1.driver,') ')
     end
  ELSE
     case when x.nip1 is not null then concat('X:',x.nip1,'<br>')
     else '' end
  end,
  CASE 
  when d2.barcode is not null then 
     case 
     when d2.sts=0 then concat('R: ',d2.barcode,'(',d2.driver,')<br>')
     when d2.sts=1 then concat('D: ',d2.barcode,'(',d2.driver,')<br>')
     when d2.sts=2 then concat('M: ',d2.barcode,'(',d2.driver,')<br>') 
     else concat(d2.barcode,'(',d2.driver,') ')
     end
  ELSE
     case when x.nip2 is not null then concat('X:',x.nip2,'<br>')
     else '' end
  end,
  CASE 
  when d3.barcode is not null then 
     case 
     when d3.sts=0 then concat('R: ',d3.barcode,'(',d3.driver,')<br>')
     when d3.sts=1 then concat('D: ',d3.barcode,'(',d3.driver,')<br>')
     when d3.sts=2 then concat('M: ',d3.barcode,'(',d3.driver,')<br>') 
     else concat(d3.barcode,'(',d3.driver,') ')
     end
  ELSE
     case when x.nip3 is not null then concat('X:',x.nip3,'<br>')
     else '' end
  end
) as barcode,
x.date,case when x.status='M' then 'Masuk' when x.status='K' then 'Keluar' end status ,x.nmfile
from (
select p.id,p.barcode,p.date,p.status,p.nmfile,
  SUBSTRING_INDEX(p.barcode,'_',1) as nip1,
  case 
  when POSITION('_' IN p.barcode)>1 then 
	SUBSTR(p.barcode,POSITION('_' IN SUBSTRING_INDEX(p.barcode,'_',2))+1,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))-LENGTH(SUBSTRING_INDEX(p.barcode,'_',1))-1) 
  end as nip2,
  case 
  when LENGTH(SUBSTRING_INDEX(p.barcode,'_',2)) < LENGTH(p.barcode) then 
	SUBSTR(p.barcode,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))+2) 
  end as nip3
from m_parkir p 
where nmfile!='' and barcode!='null'
order by p.date desc 
) x
left join m_driver d1 on d1.barcode=x.nip1
left join m_driver d2 on d2.barcode=x.nip2
left join m_driver d3 on d3.barcode=x.nip3) AS Y 
where date>=STR_TO_DATE('$f_start_date','%Y-%m-%d') and date < DATE_ADD(STR_TO_DATE('$f_end_date','%Y-%m-%d'),INTERVAL 1 DAY) $find
	ORDER BY DATE DESC
	";
	//echo $sql;
	$result_user=$dbmy->SelectLimit($sql,$rows,$offset);
	//die(json_encode($result_user));
	$items=array();
	while($row=$result_user->Fetchrow()){
	    //die(json_encode($row));
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$date=$f->convert_date(substr($date,0,10),1)." ".substr($date,11,8);
		$id=(empty($id))?"OTHERS":$id;
		$barcode=(empty($barcode))?"OTHERS":$barcode;
		//$file=(empty($file))?"OTHERS":$file;
		$status=(empty($status))?"OTHERS":$status;
		$nmfile=(empty($nmfile))?"OTHERS":$nmfile;
		$img=$nmfile=="&nbsp;"?"$nbsp;":"<a href=\"image.php?id=$nmfile&x=500&y=300\" target=\"_blank\"><img src=\"/i/icons/photo_link.png\"/></a>";
		  
		//$coord_name=$db->getOne("select nm_peg from spg_data_current where nip='$create_by'");
		$items[]=array("id"=>"$id","barcode"=>"$barcode","date"=>"$date","status"=>"$status","nmfile"=>"$nmfile","file"=>"$img"
			);
		
	}
	//$total_topup=number_format($dbproj->getOne("select sum(amount) from m_request_pulsa where req_date between '$f_start_date' and '$f_end_date' and status_topup='RECEIVED'"),0,'.',',');
	/*$result["footer"]=array(
							array("req_date"=>"<b>Total Transfer</b>","amount"=>"<b><span style='color:black'>$total_topup</span></b>")
							);*/
	$result["rows"] = $items;
	echo json_encode($result);
}
?>