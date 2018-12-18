<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<body onLoad="$('#lke_id').combobox('setValue','');$('#hidex').hide();">
<div class="easyui-panel" title="Rekap Operational Cost" style="width:100%;height:250px;padding:10px;">
<div class="easyui-layout" data-options="fit:true">
<div data-options="region:'center'" style="padding:10px">
<div id="hidex">
<div class="fitem">
<label>PM</label>
<select class="easyui-combogrid" name="pm_id" id="pm_id" style="width:50%;"
	data-options="
								panelWidth:525,
								url: 'rekap_carpoolGet.php?act=combo_pm',
								idField:'nip',
								textField:'nm_peg',
								mode:'remote',
								fitColumns:true,
								rownumbers:false,
						columns:[[
								{field:'nip',title:'NIP',width:'200'},
								{field:'nm_peg',title:'PM Name',width:'325'}
						]]
						">
					
					</select>

</div>

<div class="fitem">
<label>Project Name</label>
<select class="easyui-combogrid" name="proj_code" id="proj_code" style="width:50%;"
	data-options="
								panelWidth:525,
								url: 'rekap_carpoolGet.php?act=combo_project',
								idField:'ID',
								textField:'PROJ_NAME',
								mode:'remote',
								fitColumns:true,
								rownumbers:false,
						columns:[[
								{field:'PROJ_CODE',title:'Project Code',width:'30%'},
								{field:'PROJ_NAME',title:'Project Name',width:'69%'}
						]]
						">
					
					</select>
</div>
</div>

<div class="fitem">
<label>From date</label> <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
</div>

<div class="fitem">
<label>Type Rekap</label> <select class="easyui-combobox" name="format" style="width:20%;" id="format" data-options="panelHeight:'auto'">
	<option value="1">Detail Carpool Real</option>
	<option value="2">Detail Rental Real</option>
	<option value="3">Detail Operational Real</option>
	<option value="4">Detail Pulsa Real</option>
	<option value="6">Detail Operational Rigger Real</option>
	<option value="5">Detail Budget Plan</option>
	
	<!--<option value="10">Detail Operational RealNEW</option>-->
</select>
</div>

<div class="fitem">
<label>Output</label> <select class="easyui-combobox" name="output" style="width:100px;" id="output" data-options="panelHeight:'auto'">
<!--<option value="html">HTML</option>-->
<option value="excel">Excel</option>
</select>

</div>

<div class="fitem">
<label>Location</label>
	<input id="lke_id" name="lke_id" class="easyui-combobox" style="width:20%;" data-options="
        valueField: 'lke_id',
        textField: 'lokasi_kerja',
		panelHeight:'auto',
        url: '/inventaris/data_kendaraanGet.php?act=combo_lokasiReport'">
</div>

<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-search'" style="width:80px" onclick="submit()">SUBMIT</a>
</div>
</div>
</div>
<script>
function submit(){
	var pm_id=$('#pm_id').combogrid('getValue');
	var proj_code=$('#proj_code').combogrid('getValue');
	var output=$('#output').combobox('getValue');
	var format=$('#format').combobox('getValue');
	var lke_id=$('#lke_id').combobox('getValue');
	var start_date 	= $('#str_date').val();
	var end_date 	= $('#upto_date').val();
	if(output=='html'){
		if(format=='1'){
			window.location.assign("rekap_carpoolHTML.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}else{
			window.location.assign("rekap_carpoolHTML2.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}
	}else if(output=='excel'){
		if(format=='1'){
			window.location.assign("rekap_carpoolExcelNew.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}
		else if(format=='2'){
			window.location.assign("rekap_rentalExcel.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}else if(format=='3'){
			window.location.assign("rekap_operationalExcelNew.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}/*else if(format=='10'){
			window.location.assign("rekap_operationalExcelNew.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}*/else if(format=='4'){
			window.location.assign("rekap_pulsaExcel.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}else if(format=='5'){
			window.location.assign("rekap_operationalplanExcel.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}else if(format=='6'){
			window.location.assign("rekap_operationalriggerExcel.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}else{
			window.location.assign("rekap_carpoolExcel2.php?pm_id="+pm_id+"&proj_code="+proj_code+"&start_date="+start_date+"&end_date="+end_date+"&lke_id="+lke_id);
		}
	}
}
</script>