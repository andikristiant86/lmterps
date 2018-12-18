<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];
	$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
	$template->basicheader(2);
	$lke_id=$db->getOne("select (select lokasi_kerja from spg_lokasi_kerja where lke_id=spg_data_current.lke_id) from spg_data_current where nip='$login_nip'");
	$isAdminOrderCarpool=$db->getOne("select top 1 wia_inquiryaccess  from wf_inquiry_access where wia_accessname='$wia_accessname' and wia_inquiryname='AdminOrderCarpool'")=='Ya'?true:false;

?>
<!--Info penting, warna hijau atau merah sebelah kiri menandakan site sudah dikerjakan berapa kali. <font color=red>mohon pastikan site diisi dengan benar.</font>-->
<script type="text/javascript">
var url;
function doSearch(value,name){
			var start_date 	= $('#str_date').val();
			var end_date 	= $('#upto_date').val();
			$('#dg').datagrid('load',{
				value: value,
				name: name,
				f_start_date 	: start_date,
				f_end_date 		: end_date
			});
		}
function tambah(){
	$("#hideShow1").hide();
	$('#btnSave').linkbutton('enable');
	$('#dlg').dialog('open').dialog('setTitle','FORM ADD');
		$('#fm').form('clear');
		$.post('makeproposalGetnew.php',{act:'generate_kode'},function(result){
			var txt=result.kode;
			list=txt.split("|");
			$('#fm').form('load',{
				ocs_id:list[0]
			});
		},'json');
	
	url = 'makeproposalGetnew.php?act=do_add';
}
function edit(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			if(row.status=='VERIFIED'|| row.status=='PULANG'|| row.status=='JALAN'|| (row.pm_approve=='Not Approved' && row.finance_approve=='Not Approved')){
				//$('#site_name').combogrid({url:'makeproposalGetnew.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
				//if(row.status=='PULANG') 
				$("#hideShow1").show();
				
				//else $("#hideShow1").hide();
				$('#dlg').dialog('open').dialog('setTitle','FORM EDIT');
				$('#fm').form('load',row);
				//$('#site_name').combogrid('setValue', row.site_name);
				url = 'makeproposalGetnew.php?act=do_update&ocs_id='+row.ocs_id+'&status='+row.status;
				
			}else{
				$.messager.alert('Error',"Maaf untuk merubah data setelah status JALAN/PULANG!",'error');
			}
	}
}function cancel_req(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			if(row.status=='JALAN' || row.status=='PULANG' || row.status=='CLOSED'){
				$.messager.alert('Error',"Request bisa di cancel, jika status BELUM JALAN!",'error');
			}else{
				$.messager.prompt('Confirm','Are you sure <b>cancel?</b><br>Reason:',function(r){
					if (r){
						$.post('makeproposalGetnew.php?act=do_cancel&reason='+r,{status:row.status,ocs_id:row.ocs_id},function(result){
							if (result.success){
								$('#dg').datagrid('reload');
							} else {
								$.messager.alert('Error',"Request bisa di cancel, jika status BELUM JALAN!",'error');
							}
						},'json');
					}
				});
			}
	}
}
function Save(){
	$('#fm').form('submit',{
		url: url,
		onSubmit: function(){
			return $(this).form('validate');
			},
			success: function(result){
			//$('#btnSave').linkbutton('disable');
			var result = eval('('+result+')');
			if (result.errorMsg){
			
			$.messager.alert('Error',result.errorMsg,'error');
			} else {
			$('#dlg').dialog('close'); // close the dialog
			$('#dg').datagrid('reload'); // reload the user data
			}
		}
	});
}
function destroy(){
var row = $('#dg').datagrid('getSelected');
if (row){
	// if(row.pm_approve.indexOf('Approved') > -1||row.finance_approve.indexOf('Approved') > -1){
		// $.messager.alert('Error',"Sorry, you don't have permission to delete this data!",'error');
	// }else{
		$.messager.confirm('Confirm','You want to delete this data?',function(r){
		if (r){
			$.post('makeproposalGetnew.php?act=do_destroy',{id:row.ocs_id},function(result){
				if (result.success){
					$('#dg').datagrid('reload'); // reload the user data
				} else {
					$.messager.alert('Error',result.errorMsg,'error');
				}
			},'json');
		}
		});
	//}
}
}

function status(val,row){
	if (val == 'BELUM JALAN' || val=='CANCEL' || val=='REJECT'){
				return '<span style="color:red;">'+val+'</span>';
	}else if(val=='PULANG'){
				return '<span style="color:blue;">'+val+'</span>';
	}else if(val=='CLOSED'){
				return '<span style="color:black;">'+val+'</span>';
	}else{
				return '<span style="color:green;">'+val+'</span>';
	}
}
function topup_pulsa(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
			if(row.status=='CLOSED'){
				$.messager.alert('Error',"Sorry, you don't have permission to update this data!",'error');
			}else{
				window.open("/carpool/topup_pulsa.php?ocs_id="+row.ocs_id+"&proj_id="+row.proj_id, "_blank", 
				"toolbar=yes, scrollbars=yes, resizable=yes, top=150, left=200, width=1000, height=500");
			}
	}
}

function cellStyler(value,row,index){
            if (value<=2){
                return 'background-color:green;color:white;';
            }
			else if(value>=3){
				 return 'background-color:red;color:white;';
			}
        }

</script>
<table id="dg" title="POOLING DT" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'makeproposalGetnew.php?act=view',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:20,
					pageList: [10,20,30,40,50],
					showFooter:true,
					fitColumns:false
					"
>
<thead data-options="frozen:true">
            <tr>
			<th data-options="field:'ck',checkbox:true"></th>
				 <th field="jum_site" width="20" align="center" data-options="styler:cellStyler"></th>
                <th field="ocs_id" width="100">ID</th>
				<th field="proj_name" width="250">PROJECT</th>
				<th field="ocs_desc" width="200">DESCRIPTION</th>
            </tr>
        </thead>
<thead>
<thead>
<tr>

<th field="date" width="100">DATE</th>
<th field="time" width="100">TIME</th>
<?if(!$isAdminOrderCarpool){ ?>
<th field="sow_name" width="150">SOW</th>
<th field="site_name" width="150">SITE</th>
<th field="name_dt" width="150">ENGINER</th>
<?}else{?>
<th field="name_dt" width="150">MANAGEMENT</th>
<?}?>
<th field="km_acuan" width="150" align="right">KM ACUAN</th>
<!--<th field="uang_pulsa" width="150" align="right">PULSA</th>-->

<th field="operational" width="150" align="right">OPERATIONAL</th>

<th field="pm_approve" width="150" align="center">TL APPROVAL</th>
<th field="finance_approve" width="150" align="center">BC APPROVAL</th>
<th field="status" width="100" formatter="status" align="center">STATUS</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:250px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"tambah"); ?>">Insert</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="<?php $f->CheckHakAccess($fua_edit,"edit"); ?>">Update</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="<?php $f->CheckHakAccess($fua_delete,"destroy"); ?>">Delete</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"cancel_req"); ?>">Cancel</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>

<div id="dlg" class="easyui-dialog" style="width:75%;height:450px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<div class="fitem">
	<label>ID</label>
	<input name="ocs_id" id="ocs_id" class="easyui-textbox" style="width:29%;" readOnly required="true">
</div>
<div class="fitem">
	<label>Date/Time</label>
	<?=$f->InputDate("date");?> 
<select class="easyui-combobox" name="time" style="width:20%;" required="true"
data-options="panelHeight:'auto'">
	<option value="Pagi">Pagi</option>
	<option value="Malam">Malam</option>
</select>
</div>
<!--
<div class="fitem">
<label>Area</label>
<input id="lke_id" name="lke_id" class="easyui-combobox" style="width:50%;" required="true" data-options="
        valueField: 'lke_id',
        textField: 'lokasi_kerja',
        url: 'makeproposalGetnew.php?act=combo_lke',
		panelHeight:'auto'">
</div>
-->
<?php

if($isAdminOrderCarpool){ 
?>
<div class="fitem">
<label>Employee</label>
<select class="easyui-combogrid" required="true" name="name_dt" id="name_dt" style="width:50%;"
					data-options="
							panelWidth:400,
							url: 'makeproposalGetnew.php?act=combo_dt&x=2',
							idField:'nip',
							textField:'nm_peg',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'nip',title:'NIP',width:'100'},
							{field:'nm_peg',title:'Name',width:'300'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{nip_dt:row.nip});
					}
					">
				</select>
<input name="nip_dt" type="hidden">
</div>

<div class="fitem">
<label>Order Area</label>

<select class="easyui-combogrid" name="proj_id" style="width:50%;" required="true"
					data-options="
							panelWidth:400,
							url: 'makeproposalGetnew.php?act=combo_project1',
							idField:'id',
							textField:'proj_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'proj_code',title:'Code',width:'150'},
							{field:'proj_name',title:'Name',width:'250'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{lke_id:row.lke_id});
					}
					">
				</select>
				<input type="hidden" name="lke_id">

</div>

<?php
}
else {
?>
<div class="fitem">
<label>Project Name</label>

<select class="easyui-combogrid" name="proj_id" style="width:50%;" required="true"
					data-options="
							panelWidth:400,
							url: 'makeproposalGetnew.php?act=combo_project',
							idField:'id',
							textField:'proj_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'proj_code',title:'Project Code',width:'150'},
							{field:'proj_name',title:'Project Name',width:'250'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{lke_id:row.lke_id});
						$('#sow').combogrid({url:'makeproposalGetnew.php?act=combo_sow&proj_id='+row.id});
						$('#sow').combogrid('setValue', '');
					}
					">
				</select>
				<input type="hidden" name="lke_id">

</div>

<div title="RNO/PLO" style="padding:10px">
		<label>DT Type</label>
			<input id="dt_type" name="dt_type" class="easyui-combobox" style="width:20%;" required="true" data-options="
							valueField: 'dt_type',
							textField: 'dt_type',
							panelHeight:'auto',
							url:'activity_dtGet.php?act=combo_dttype'
							">
		</div>

<div class="fitem">
<label>SOW Name</label>
	<select class="easyui-combogrid" name="sow_name" id="sow" style="width:50%;" required="true"
					data-options="
							panelWidth:400,
							idField:'sow_name',
							textField:'sow_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'sow_name',title:'SOW Name',width:'400'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{sow_id:row.sow_id});
						$('#site_name').combogrid({url:'makeproposalGetnew.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
						$('#site_name').combogrid('setValue', '');
					}
					">
	</select>
	<!--
	
	var url = 'makeproposalGetnew.php?act=combo_sow1&proj_id='+row.id;
	$('#cc2').combobox('reload', url);
	<input id="cc2" class="easyui-combobox" data-options="valueField:'sow_id',textField:'sow_name',panelHeight='auto'" style="width:50%;" required="true">-->
	<input type="hidden" name="sow_id">
</div>
<div class="fitem">
<!--<label>Jumlah Site</label>
	<input name="jum_site" class="easyui-numberbox" style="width:20%;" required="true"></input>
</div>-->

<div class="fitem">
<label>Site</label>
	<select class="easyui-combogrid" name="site_name" id="site_name" style="width:50%;"
					data-options="
							panelWidth:400,
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:'400'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{site_id:row.id});
						
					}
					">
				</select>
</div>
<input type="hidden" name="site_id">
<div class="fitem">
<label>Enginer</label>
<select class="easyui-combogrid" name="name_dt" id="name_dt" style="width:50%;"
					data-options="
							panelWidth:400,
							url: 'makeproposalGetnew.php?act=combo_dt',
							idField:'nip',
							textField:'nm_peg',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'nip',title:'NIP',width:'100'},
							{field:'nm_peg',title:'Employee Name',width:'300'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{nip_dt:row.nip});
					},required:true
					">
				</select>
<input name="nip_dt" type="hidden">
</div>

<div class="fitem">
<label>PLO/RNO</label>
<select class="easyui-combogrid" name="name_rno" style="width:50%;"
					data-options="
							panelWidth:400,
							url: 'makeproposalGetnew.php?act=combo_dt&x=1',
							idField:'nip',
							textField:'nm_peg',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'nip',title:'NIP',width:'100'},
							{field:'nm_peg',title:'Employee Name',width:'300'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{nip_rno:row.nip});
					}
					">
				</select>
<input name="nip_rno" type="hidden">
</div>
<?php
}
?>
<div class="fitem">
<label>KM Acuan</label>
	<input name="km_acuan" class="easyui-numberbox" style="width:20%;" required="true"></input>
	<!--
	PULSA: <input name="uang_pulsa" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	-->
</div>
<div class="fitem">
<label>Uang Makan</label>
	<input name="um" class="easyui-numberbox" style="width:20%;" data-options="precision:0,groupSeparator:','" style="width:20%;" required="true"></input>
	Uang Jalan <input name="uj" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" required="true" style="width:20%;"></input>
</div>
<div class="fitem">
<label>BBM</label>
	<input name="bbm" class="easyui-numberbox" style="width:20%;" data-options="precision:0,groupSeparator:','" style="width:20%;" required="true"></input>
	Others <input name="parking" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" required="true" style="width:20%;"></input>
</div>
<!--
<div class="fitem">
<label>Phone Number</label>
<input class="easyui-textbox" name="phone_number" style="width:50%;">
</div>
-->
<div class="fitem">
<label>Description</label>
<input class="easyui-textbox" name="ocs_desc" data-options="multiline:true" style="width:50%;height:35px" required="true">
</div>
<!--
<div class="fitem">
	<label>Shift Of Work</label>
		<select id="shift" class="easyui-combobox" name="shift" style="width:20%;" data-options="panelHeight:'auto'" required="true">
			<option value="E_L1_PAGI">SHIFT_PAGI</option>
			<option value="E_L1_SIANG">SHIFT_SIANG</option>
			<option value="E_L1_MALAM">SHIFT_MALAM</option>
		</select>
	</div>
-->

<div id="hideShow1">
<div class="fitem">
<label></label>
	 <div class="easyui-tabs" style="width:100%;height:90px" border=0>
		<div title="DT" style="padding:10px" border=0>
		Result: 
			<select class="easyui-combobox" name="result_dt" style="width:200px;" data-options="panelHeight:'auto'">
				<option value="" selected>--Selected--</option>
				<option value="CANCLE">CANCLE</option>
				<option value="DONE">DONE</option>
				<option value="FAILED">FAILED</option>
				<option value="NOT DONE">NOT DONE</option>
			</select>
		</div>
		<div title="RNO/PLO" style="padding:10px">
		Result: 
			<select class="easyui-combobox" name="result_rno" style="width:200px;" data-options="panelHeight:'auto'">
				<option value="" selected>--Selected--</option>
				<option value="CANCLE">CANCLE</option>
				<option value="DONE">DONE</option>
				<option value="FAILED">FAILED</option>
				<option value="NOT DONE">NOT DONE</option>
			</select>
		</div>
		<div title="Remark" style="padding:10px">
		remark: 
			<input class="easyui-textbox" name="remark" data-options="multiline:true" style="width:85%;height:35px">
		</div>
	</div>
</div>
</div>

</form>
</div>

<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>
<!--
<div>
<b>Catatan:
</b>
<ul>
<li>Request dilock jika request sebelumnya masih <font color=red>BELUM JALAN</font>, belum <font color=red>CLOSED</font> (utk yang jalan/pulang), atau belum <font color=red>CANCEL</font>(yang status <font color=red>BELUM JALAN</font> dibatalkan)</li>
<li>status <font color=red>CLOSED</font> jika mobil yang jalan sudah update kepulangannya (status <font color=red>PULANG</font>) ke admin carpool dan terverifikasi (status <font color=red>VERIFIED</font>) oleh dt koordinator</li>
</ul>
</div>-->