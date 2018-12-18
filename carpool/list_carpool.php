<?php
	
	include("mysql/mysql.class.php");
	$db = new MySQL();
  
    $sql="select x.id,
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
  case when POSITION('_' IN p.barcode)>1 then SUBSTR(p.barcode,POSITION('_' IN SUBSTRING_INDEX(p.barcode,'_',2))+1,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))-LENGTH(SUBSTRING_INDEX(p.barcode,'_',1))-1) end as nip2,
  case when LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))<LENGTH(p.barcode) then SUBSTR(p.barcode,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))+2) end as nip3
from m_parkir p -- limit 0,5
where nmfile!='' and barcode!='null'
order by p.date desc 
) x
left join m_driver d1 on d1.barcode=x.nip1
left join m_driver d2 on d2.barcode=x.nip2
left join m_driver d3 on d3.barcode=x.nip3";/*"select x.id,
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
     ''
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
     ''
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
     ''
  end
) as barcode,
x.date,x.status,x.nmfile
from (
select p.*,
  SUBSTRING_INDEX(p.barcode,'_',1) as nip1,
	case when POSITION('_' IN p.barcode)>1 then SUBSTR(p.barcode,POSITION('_' IN SUBSTRING_INDEX(p.barcode,'_',2))+1,LENGTH(SUBSTRING_INDEX(p.barcode,'_',1))) end as nip2,
  case when LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))<LENGTH(p.barcode) then SUBSTR(p.barcode,LENGTH(SUBSTRING_INDEX(p.barcode,'_',2))+2) end as nip3
from m_parkir p -- limit 0,5
where nmfile!=''
order by p.date desc 
) x
left join m_driver d1 on d1.barcode=x.nip1
left join m_driver d2 on d2.barcode=x.nip2
left join m_driver d3 on d3.barcode=x.nip3";
	/*"select 
		concat(b.barcode,'<br>Driver: <b>',d.driver,'</b>') as barcode,
		case when b.status='K' then 'Keluar' when b.status='M' then 'Masuk' end as status,
		DATE_FORMAT(b.date,'%Y/%m/%d %H:%i:%s') as date,b.nmfile 
	from m_parkir b
	left join m_driver d on d.barcode=b.barcode
	";*/
	//die($sql);
	$styleTH="style='border:1px red dotted;padding:5px 10px 5px 10px;background-color:grey;'";
    $styleTD="style='border:1px grey dotted;padding:5px 10px 5px 10px;'";
    if ($db->Open()) {
	  if(!$db->Query($sql))
  	  	echo $db->Error();
      echo "<table>
	  <tr>
		  <th $styleTH>No</th>
		  <th $styleTH>BARCODE</th>
		  <th $styleTH>STATUS</th>
		  <th $styleTH>WAKTU</th>
		  <th $styleTH>IMAGE</th>
	  ";
	  $i=1;
	  while ($row = $db->Row()) {
          foreach ($row as $key=>$val){
				$val=empty($val)?"&nbsp;":$val;
				$$key=$val;
			}
		  $img=$nmfile=="&nbsp;"?"$nbsp;":"<a href=\"image.php?id=$nmfile&x=x&y=x\"><img src=\"image.php?id=$nmfile&x=50&y=30\"/>";
		  echo "
		  </tr>
		  <tr>
		  <td $styleTD>$i</td>
		  <td $styleTD>$barcode</td>
		  <td $styleTD>$status</td>
		  <td $styleTD>$date</td>
		  <td $styleTD>$img</td>
		  ";
		  $i++;
      }
	  echo "</tr></table>";    
	}
	else
	  echo $db->Error();

?>