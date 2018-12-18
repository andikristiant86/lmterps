<?php
ob_start();
session_start();	
include_once($DOCUMENT_ROOT."/s/config.php");
$nip_sipeg=$_SESSION['sipeg_nip_pegawai'];	
$login_nip=(empty($nip_sipeg))? $login_nip:$nip_sipeg;
$template->basicheader(2);
$cek_admin=$dbproj->getOne("select count(*) from m_subcost_admin where admin='$login_nip' and verified_data='Y'");
?>
<table id="dg" title=":: REALISATION SUBCOST" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'realisasi_subcostGet.php?act=view',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:50,
					pageList: [10,20,30,40,50,100,200],
					showFooter:true,
					fitColumns:true
					"
>
<thead data-options="frozen:true">
            <tr>
<th data-options="field:'ck',checkbox:true"></th>

<th field="nox" width="100">REQ. ID</th>

<th field="req_name" width="150">REQ. NAME</th>
            </tr>
        </thead>
<thead>
<thead>
<tr>
<th field="note" width="200">DESCRIPTION</th>
<th field="project_name" width="150">PROJECT</th>
<th field="cara_bayar" width="100" align="center">PAYMENT TYPE</th>
<th field="amount" width="100" align="right">AMOUNT</th>
<th field="status_approval" width="130" align="right">STATUS</th>
<th field="bukti_transfer" width="70" align="center">FILE</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<?if($cek_admin>=1){?>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="real_ok()">Closed</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="reject()">Back to user</a>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="reject_all()">Reject</a>
<?}else{?>
	<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="realisation()">Realisation</a>
<?}?>
</td>
<td align="right">
	<!--<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="printx()">View</a>-->
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>

<script type="text/javascript">
var url;
function cetak(){
	window.location.assign("/finance/paid_costExport.php");
}
function doSearch(value,name){
			//var statusx = $('#status').combobox('getValue');
			$('#dg').datagrid('load',{
				value		 	: value,
				name		 	: name//,
				//status			: statusx
			});
}

function realisation(){
	document.getElementById('lbltipAddedComment').innerHTML = 'Save';
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			$('#dlg').dialog('open').dialog('setTitle',':: REALISATION SUBCOST');
			$('#fm').form('load',row);
			url = 'realisasi_subcostGet.php?act=do_realisasi&no='+row.no;
		}
}

function Save(){
	document.getElementById('lbltipAddedComment').innerHTML = 'Loading...';
	$('#btnSave').linkbutton('disable');
	$('#fm').form('submit',{
		url: url,
		onSubmit: function(){
			var isValid = $(this).form('validate');
			if (!isValid){
				document.getElementById('lbltipAddedComment').innerHTML = 'Save';
				$('#btnSave').linkbutton('enable');
			}
			return isValid;	// return false will stop the form submission
			},
			success: function(result){
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
function printx (no){
	// var row = $('#dg').datagrid('getSelected');
	// if (row){
		//if(row.status==6 || row.status==1001 || row.status==1002){
			window.open("/kepegawaian/absensi/uploads/?pengajuan_id="+no, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=50, left=100, width=900, height=525");
		//}
	//}
	
}function openfile (filex){
	// var row = $('#dg').datagrid('getSelected');
	// if (row){
		//if(row.status==6 || row.status==1001 || row.status==1002){
			window.open("/finance/uploads/"+filex, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=150, left=400, width=800, height=400");
		//}
	//}
	
}


function reject(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		$.messager.prompt('Confirm','Are you sure reject this data subcost?<br>Please input the reason!:',function(r){
			if (r){
				$.post('realisasi_subcostGet.php?act=do_reject&reason='+r,{id:ids.join(',')},function(result){
					if (result.success){
						$('#dg').datagrid('reload');
					} else {
						$.messager.show({
						title: 'error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
		});
	}
}

function reject_all(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		$.messager.prompt('Confirm','Are you sure reject this data subcost?<br>Please input the reason!:',function(r){
			if (r){
				$.post('realisasi_subcostGet.php?act=do_reject1&reason='+r,{id:ids.join(',')},function(result){
					if (result.success){
						$('#dg').datagrid('reload');
					} else {
						$.messager.show({
						title: 'error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
		});
	}
}

function real_ok(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		ids.push(rows[i].id);
	}
	if(ids.join(',')!=""){
		var row = $('#dg').datagrid('getSelected');
		if (row.status_real=='1'){
			realisation();
		}else{
		$.messager.confirm('Confirm','Are you sure approved this data subcost?',function(r){
			if (r){
					$.post('realisasi_subcostGet.php?act=do_approved',{id:ids.join(',')},function(result){
						if (result.success){
							$('#dg').datagrid('reload');
						} else {
							$.messager.show({
							title: 'error',
							msg: result.errorMsg
							});
						}
					},'json');
				}
			});
		}
	}
}


</script>
<div id="dlg" class="easyui-dialog" style="width:70%;height:300px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<div class="fitem">
<label>SUBCOST CODE</label>
	<input name="no" class="easyui-textbox" required="true" style="width:50%" readOnly>
</div>

<div class="fitem">
<label>AMOUNT PAID</label>
	<input class="easyui-numberbox"  name="amount_paid" data-options="labelPosition:'top',precision:0,groupSeparator:',',width:'25%'" required="true" style="text-align:left;">
</div>

<div class="fitem">
<label>AMOUNT REAL</label>
	<input class="easyui-numberbox"  name="amount_real" data-options="labelPosition:'top',precision:0,groupSeparator:',',width:'25%'" required="true" style="text-align:left;"> 
</div>
<!--
<div class="fitem">
<label>SURPLUS (KELEBIHAN BIAYA)</label>
	<input class="easyui-numberbox"  name="amount_back" data-options="labelPosition:'top',precision:0,groupSeparator:',',width:'25%'" required="true" style="text-align:left;"> 
</div>

<div class="fitem">
<label>DEFICIT (KEKURANGAN BIAYA)</label>
	<input class="easyui-numberbox"  name="amount_deficit" data-options="labelPosition:'top',precision:0,groupSeparator:',',width:'25%'" required="true" style="text-align:left;"> 
</div>
-->
<div class="fitem">
<label>UPLOAD NOTA</label>
	<input class="easyui-filebox" name="fileToUpload" labelPosition="top" data-options="prompt:'Choose a file...'" style="width:50%">
</div>
<div class="fitem">
<label></label>
	* Hanya diperbolehkan upload dengan format .jpg .png .pdf .xls .doc
</div>
</form>
</div>
<div id="dlg-buttons">
<font color=red>* Wajib diisi!</font>
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px"><label id="lbltipAddedComment">Send</label></a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>