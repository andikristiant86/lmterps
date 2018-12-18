<?php
	include "$DOCUMENT_ROOT/s/template.php";
	$template=new template;
	$template->basicheader(2);
?>


<script type="text/javascript">
var namaBulanI = new Array( "Januari","Februari","Maret","April","Mei","Juni",
"Juli","Agustus","September","Oktober","November","Desember" ); 
var namaHariI = new Array( "Kamis","Jumat","Sabtu","Minggu","Senin","Selasa","Rabu" ); 

function showDate() {
now = new Date();

iTanggalM = now.getDate(); 
iBulanM = now.getMonth(); 
iTahunM = now.getYear(); 
if(iTahunM<1900) { iTahunM += 1900; } // Y2K 

iJam=now.getHours(); 
iMenit=now.getMinutes(); 
iDetik=now.getSeconds();

hr = Date.UTC(iTahunM,iBulanM,iTanggalM,0,0,0)/1000/60/60/24; 

sDate = namaHariI[hr%7]+", "+iTanggalM+" "+namaBulanI[iBulanM]+" "+iTahunM+"<br>"; 
sDate += (iJam<10?"0"+iJam:iJam)+":"+ 
(iMenit<10?"0"+iMenit:iMenit)+":"+ 
(iDetik<10?"0"+iDetik:iDetik);

if(document.all) 
{ document.all.clock.innerHTML=sDate; } 
else if(document.getElementById) 
{ document.getElementById( "clock" ).innerHTML=sDate; } 
else { document.write(sDate); } 
}


function showIt() { 
showDate();
if(document.all||document.getElementById) 
{ setInterval("showDate()",1000); } 
} 
</script>

<div class="easyui-panel" title="ABSENT ONLINE" style="width:100%; padding:10px;">
Absensi manual ini diperuntukan bagi karyawan yang tidak bisa menggunakan FINGERPRINT atau sedang bepergian keluar kota. <br><br>

CheckIn: absen ketika masuk kerja, CheckOut: absen ketika pulang kerja.
<!--<BR><BR>
<font color=red>MULAI TANGGAL 23-05-2016, ALL KARYAWAN LMT AREA JAKARTA N WEST JAVA YANG BERKANTOR DI LMT WAJIB ABSENT DENGAN FINGERPRINT. SECARA OTOMATIS ABSENT MANUAL AKAN
DITUTUP PERTANGGAL 23-05-2016. JIKA SEWAKTU BUTUH ABSENSI MANUAL SILAHKAN DI AJUKAN DI UNLOCK SYSTEM.</font>-->
<br><br>
<span id="clock"><script>showIt();</script></span><br><br>
<input id="userid" class="easyui-textbox" data-options="prompt:'Enter a Userid...'" style="width:15%;">
<a href="#" class="easyui-linkbutton c6" data-options="iconCls:'icon-ok',toggle:true" onClick="absent(1)">CheckIn</a>
<a href="#" class="easyui-linkbutton c6" data-options="iconCls:'icon-ok',toggle:true" onClick="absent(2)">CheckOut</a>

<p id="loading"></p>
<p id="alert"></p>
</div>

<script type="text/javascript">
document.getElementById("demo").innerHTML = Date();
function absent(val){
	var val;
	var userid = document.getElementById("userid").value;
	document.getElementById("alert").innerHTML = "";
	document.getElementById("loading").innerHTML = "";
									document.getElementById("loading").innerHTML = "Loading...";
									$.post('absensierpsGet.php',{fInOut:val,userid:userid},function(result){
										if (result.success){
											document.getElementById("alert").innerHTML = '<font color=green><b>'+result.success+'</b></font>';
											document.getElementById("loading").innerHTML = "";
										} else {
											document.getElementById("alert").innerHTML = '<font color=red><b>'+result.error+'</b></font>';
											document.getElementById("loading").innerHTML = "";
										}
									},'json');
}
</script>






