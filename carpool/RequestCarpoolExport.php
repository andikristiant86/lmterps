<?php 
$date=date("Ymd");
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=MILESTONE_$date$proj_id.xls");
header("Pragma: no-cache");
header("Expires: 0");

include("$DOCUMENT_ROOT/s/config.php");

$sql="select * from t_milestone where proj_id='$proj_id'";
$sqlExc=$dbproj->Execute($sql);
?>
<style>
.boldtable, .boldtable TD, .boldtable TH
{
	font-family:sans-serif;
	font-size:9pt;
}
</style>
<table border="1" CLASS="boldtable">
        <thead>
			<tr bgcolor="#d1d1d1">
				<th width="100">MILESTONE ID</th>
				<th width="200">SITE NAME</th>
				<th width="100">START DATE</th>
				<th width="100">DATE#1</th>
				<th width="100">DATE#2</th>
				<th width="100">DATE#3</th>
				<th width="100">DATE#4</th>
				<th width="100">DATE#5</th>
			</tr>
        </thead>
        <tbody>
<?php 
while($row=$sqlExc->Fetchrow()){
		foreach($row as $key=>$val){
			$key=strtolower($key);
			$$key=$val;				
		}
		$start_date=$f->convert_date($start_date,1);
		$date_1=$f->convert_date($date_1,1);
		$date_2=$f->convert_date($date_2,1);
		$date_3=$f->convert_date($date_3,1);
		$date_4=$f->convert_date($date_4,1);
		$date_5=$f->convert_date($date_5,1);
?>
			<tr>
				<td align="center"><?=$id;?></td>
				<td><?=$site_name;?></td>
				<td><?=$start_date;?></td>
				<td><?=$date_1;?></td>
				<td><?=$date_2;?></td>
				<td><?=$date_3;?></td>
				<td><?=$date_4;?></td>
				<td><?=$date_5;?></td>
			</tr>
<?
}
?>
		<tbody>
</table>