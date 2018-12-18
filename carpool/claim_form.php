<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
?>
<body>
<div class="easyui-panel" title="Claim Form" style="width:100%;height:230px;padding:10px;">
<div class="easyui-layout" data-options="fit:true">
<div data-options="region:'center'" style="padding:10px">
<div class="fitem">
<label>Driver</label>
<select class="easyui-combogrid" name="pm_id" id="pm_id" value="All" style="width:50%;"
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
						]],
						onSelect: function (index,row){
							$('#proj_code').combogrid({
								url: 'rekap_carpoolGet.php?act=combo_project&nip='+row.nip,
							});
						}
						">
					
					</select>

</div>
<div class="fitem">
<label>Date</label> <?=$f->inputDate("start_date","","","",false,1);?>
</div>

<div class="fitem">
<label>Status</label> <select class="easyui-combobox" name="jenis_claim" style="width:100px;" id="output" data-options="panelHeight:'auto'">
<option value="OUT">OUT</option>
<option value="IN">IN</option>
</select>
</div>

<div class="fitem">
<label>Output</label> <select class="easyui-combobox" name="output" style="width:100px;" id="output" data-options="panelHeight:'auto'">
<option value="html">HTML</option>
<option value="excel">Excel</option>
</select>

</div>

<a href="#" class="easyui-linkbutton" data-options="iconCls:'icon-search'" style="width:80px" onclick="submit()">SUBMIT</a>
</div>
</div>
</div>
<script>
function submit(){
	var output=$('#output').combobox('getValue');
	if(output=='html'){
		window.location.assign("calim_formHTML.php");
	}else if(output=='excel'){
		window.location.assign("calim_formWord.php");
	}
}
</script>