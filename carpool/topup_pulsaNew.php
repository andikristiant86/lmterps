<?php
	include_once($DOCUMENT_ROOT."/s/config.php");
	$template->basicheader(2);
	$lke_id=$db->getOne("select lke_id from spg_data_current where nip='$login_nip'");
	// if($lke_id=='LKE-000004'){
		// echo "<font color=red>Maaf sedang ada perbaikan system, silahkan ajukan di menu lama : submission pulsa</font>";
		// die();
	// }
?>
<script type="text/javascript">
var url;
function doSearch(value,name){
			$('#dg').datagrid('load',{
				value: value,
				name: name
			});
		}
function reject(){
	var ids = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
		//if(rows[i].sts_app_pm=='OPEN'){
			ids.push(rows[i].id);
		//}
	}
	if(ids.join(',')==""){
		$.messager.alert('Error','Maaf, silahkan pilih data sebelum proses reject!','error');
	}else{
		$.messager.prompt('Confirm','Are you sure reject this data pulsa?<b>Please input reason!</b>',function(r){
			if (r){
				$.post('monitoring_topup_appGet.php?act=do_notapproval&alasan='+r,{id:ids.join(',')},function(result){
					if (result.success){
						$('#dg').datagrid('reload');
					} else {
						$.messager.alert('Error',result.errorMsg,'error');
					}
				},'json');
			}
		});
	}

}

function tambah(){
	$('#transfer_type').combobox('setValue','');
	$("#hideShow1").hide();
	$('#btnSave').linkbutton('enable');
	$('#dlg').dialog('open').dialog('setTitle',':: E-PULSA');
	url = 'ppob_pulsaGet.php?act=do_add';
}
function edit(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			if(row.status=='CLOSED'){
				$.messager.alert('Error',"Sorry, you don't have permission to update this data!",'error');
			}else{

				if(row.status=='PULANG') $("#hideShow1").show();
				
				else $("#hideShow1").hide();
				$('#dlg').dialog('open').dialog('setTitle','FORM EDIT');
				$('#fm').form('load',row);
				$('#site_name').combogrid('setValue', row.site_name);
				url = 'ppob_pulsaGet.php?act=do_update&ocs_id='+row.ocs_id+'&status='+row.status;
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
			$('#btnSave').linkbutton('disable');
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
	if(row.status_topup=='RECEIVED'){
		$.messager.alert('Error',"Sorry, you don't have permission to delete this data!",'error');
	}else{
		$.messager.confirm('Confirm','You want to delete this data?',function(r){
		if (r){
			$.post('ppob_pulsaGet.php?act=do_destroy',{id:row.id},function(result){
				if (result.success){
					$('#dg').datagrid('reload'); // reload the user data
				} else {
					$.messager.alert('Error',result.errorMsg,'error');
				}
			},'json');
		}
		});
	}
}
}

function status(val,row){
	if (val == 'BELUM JALAN'){
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
				window.location.assign("/carpool/topup_pulsa.php?ocs_id="+row.ocs_id);
			}
	}
}
</script>

<table id="dg" title=":: E-PULSA" class="easyui-datagrid" style="height:475px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'ppob_pulsaGet.php?act=view',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:20,
					pageList: [10,20,30,40,50],
					showFooter:true,
					fitColumns:true
					"
>
<thead frozen="true">
        <tr>
            <th field="ocs_id" width="100">REQ ID</th>
			<th field="req_name" width="175">TOPUP TO</th>
        </tr>
    </thead>
<thead>
<tr>
	
	<th field="area" width="120">PAYMENT AREA</th>
	<th field="phone_number" width="150">PHONE NUMBER</th>
	<th field="voucher" width="250">VOUCHER</th>
	<th field="proj_name" width="250">PROJECT</th>
	
	<th field="status" width="150" align="center">STATUS</th>
</tr>
</thead>
</table>

<div id="toolbar">
<table width="100%" cellpadding=0 cellspacing=0><tr><td>
<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="tambah()">Add Pulsa</a>
<!--<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="tambah()">Update</a>-->
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="reject()">Reject</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>

<div id="dlg" class="easyui-dialog" style="width:70%;height:435px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<input type="hidden" name="ocs_id" value="<?=$ocs_id;?>"> 
<input type="hidden" name="proj_id" value="<?=$proj_id;?>"> 
<div class="fitem">
	<label>Date</label>
	<?=$f->InputDate("request_date");?> 
	
</div>

<div class="fitem">
<label>Name</label>
<select class="easyui-combogrid" name="request_name" id="request_name" style="width:50%;" required="true"
					data-options="
							panelWidth:300,
							url: 'ppob_pulsaGet.php?act=combo_employee',
							idField:'nip',
							textField:'nm_peg',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'nip',title:'NIP',width:'100'},
							{field:'nm_peg',title:'Name',width:'200'}
					]],
					onSelect: function (index,row){
						$('#fm').form('load',{req_nip:row.nip});
						operator=$('#ppob_operator').combobox('getValue');
						var url_phone1 = '/carpool/ppob_pulsaGet.php?act=combo_phone&nip='+row.nip+'&operator='+operator;
						$('#phone_number').combobox('reload', url_phone1);
						$('#phone_number').combobox('setValue','');
					}
					">
				</select>
<input name="req_nip" id="req_nip" type="hidden">
</div>
<div class="fitem">
<label>Project</label>
<select class="easyui-combogrid" name="proj_id" style="width:50%;" required="true"
					data-options="
							panelWidth:400,
							url: 'ppob_pulsaGet.php?act=combo_project',
							idField:'id',
							textField:'proj_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'proj_code',title:'Project Code',width:'30%'},
							{field:'proj_name',title:'Project Name',width:'60%'}
					]]
					">
</select>
</div>
<div class="fitem">
<label>Product</label>
	<input id="ppob_product" name="ppob_product" class="easyui-combobox" style="width:40%;" required="true" data-options="
        valueField: 'id',
        textField: 'product',panelHeight:'auto',
        url: '/ppob/h2h_product.php',
		onSelect: function(rec){
            var url= '/ppob/h2h_operator.php?operator='+rec.id;
            $('#ppob_operator').combobox('reload', url);
			$('#ppob_operator').combobox('setValue','');
			$('#ppob_voucher').combobox('setValue','');
        }"
		
	>
</div>
<div class="fitem">
<label>Operator</label>
	<input id="ppob_operator" name="ppob_operator" class="easyui-combobox" style="width:40%;" required="true" data-options="
        valueField: 'opr',
        textField: 'opr',
        onSelect: function(rec){
            var url = '/ppob/h2h_voucher.php?operator='+rec.opr;
            $('#ppob_voucher').combobox('reload', url);
			$('#ppob_voucher').combobox('setValue','');
			
			nip = document.getElementById('req_nip').value;
			var url_phone = '/carpool/ppob_pulsaGet.php?act=combo_phone&nip='+nip+'&operator='+rec.opr;
			$('#phone_number').combobox('reload', url_phone);
			$('#phone_number').combobox('setValue','');
			
        }"
	>
</div>
<div class="fitem">
<label>Voucher</label>
	<input id="ppob_voucher" name="ppob_voucher" class="easyui-combobox" style="width:50%;" required="true" data-options="
        valueField: 'product',
        textField: 'product_name',
		onSelect: function(rec){
			document.getElementById('status').value = rec.status;
			document.getElementById('harga').value = rec.harga;
			document.getElementById('ket').value = rec.product_name;
        }">
	
	<input type="hidden" id="status" name="status">
	<input type="hidden" id="harga" name="harga">
	<input type="hidden" id="ket" name="ket">
</div>
<div class="fitem">
<label>Transfer Type</label>
	<select class="easyui-combobox" name="transfer_type" id="transfer_type" required="true" labelPosition="top" required="true" style="width:20%;">
                <option value="DATA">PULSA DATA</option>
				<option value="VOICE">PULSA VOICE</option>
				<option value="LISTRIK">PULSA LISTRIK</option>
	</select>
	<input type="hidden" name="loop_topup" value="1">
</div>
<div class="fitem">
<label>For Purpose</label>
	<select class="easyui-combobox" name="untuk_keperluan" id="untuk_keperluan" labelPosition="top" required="true" style="width:30%;">
                <option value="DT">DT</option>
				<option value="RF">RF</option>
				<option value="TL">TL</option>
                <option value="RIGGER">RIGGER</option>
				<option value="INSTALLATION">INSTALLATION</option>
				<option value="COORDINATION">COORDINATION</option>
                
	</select>
</div>
<div class="fitem">
<label>MSISDN</label>
	<input id="phone_number" name="phone_number" class="easyui-combobox" style="width:50%;" required="true" data-options="
        valueField: 'phone_number',
		panelHeight:'200px',
        textField: 'phone_numberx'">
</div>
<div class="fitem">
<label>Description</label>
<input class="easyui-textbox" name="description" data-options="multiline:true" style="width:50%;height:35px" required="true">
</div>
<div class="fitem">
<label>Payment Area</label>
	<input id="lke_id" name="lke_id" class="easyui-combobox" style="width:30%;" required="true" data-options="
        valueField: 'lke_id',
        textField: 'lokasi_kerja',
		panelHeight:'auto',
        url: 'request_pulsaGet.php?act=combo_lokasi'">
</div>
</form>
</div>

<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>
