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
		$.post('makeproposalGet20160406.php',{act:'generate_kode'},function(result){
			var txt=result.kode;
			list=txt.split("|");
			$('#fm').form('load',{
				ocs_id:list[0]
			});
		},'json');
	
	url = 'makeproposalGet20160406.php?act=do_add';
}
function edit(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			if(row.status=='VERIFIED'|| row.status=='PULANG'|| row.status=='JALAN'|| (row.pm_approve=='Not Approved' && row.finance_approve=='Not Approved')){
				//$('#site_name').combogrid({url:'makeproposalGet20160406.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
				//if(row.status=='PULANG') 
				//$("#hideShow1").show();
				
				//else $("#hideShow1").hide();
				$('#dlg').dialog('open').dialog('setTitle','FORM EDIT');
				$('#fm').form('load',row);
				$('#site_id').combogrid({url:'makeproposalGet20160406.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
				$('#site_id').combogrid('setValues', row.site_id.split(','));
				$('#site_id').combogrid('disable');
				$('#nip_dt').combogrid({url:'makeproposalGet20160406.php?act=combo_dt&proj_id='+row.proj_id});
				$('#nip_dt').combogrid('setValues',row.nip_dt.split(','));
				$('#nip_dt').combogrid('disable');
				$('#proj_id').combogrid('disable');
				$('#sow').combogrid('disable');
				if(row.pm_approve=='Approved' && row.finance_approve=='Approved'){
					$('#um').combogrid('disable');$('#uj').combogrid('disable');$('#parking').combogrid('disable');$('#bbm').combogrid('disable');
				}
				//$('#site_name').combogrid('setValue', row.site_name);
				url = 'makeproposalGet20160406.php?act=do_update&ocs_id='+row.ocs_id+'&status='+row.status;
				
			}else{
				$.messager.alert('Error',"Maaf untuk merubah data setelah status JALAN/PULANG!",'error');
			}
	}
}
var divIdxs=[];
function verify(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
	//	if(row.status=='PULANG'|| row.status=='JALAN'){
			//$("#hideShow1").show();
			
			//else $("#hideShow1").hide();
			$('#dg_resource').datagrid({
				url: 'makeproposalGet20160406.php?act=view_siteverify&ocs_id='+row.ocs_id
            });
			$('#dlgVerify').dialog('open').dialog('setTitle','FORM VERIFY');
			$('#fmVerify').form('load',row);
			$('#appendsite_id').combogrid({url:'makeproposalGet20160406.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
			$('#appendnip_dt').combogrid({url:'makeproposalGet20160406.php?act=combo_dt&proj_id='+row.proj_id});
							
		//	url = 'makeproposalGet20160406.php?act=do_update&ocs_id='+row.ocs_id+'&status='+row.status;
			
	/*	}else{
			$.messager.alert('Error',"Maaf untuk verifikasi setelah status JALAN/PULANG!",'error');
		}*/
	}
}

function cancel_req(){
	$('#btnSave').linkbutton('enable');
	var row = $('#dg').datagrid('getSelected');
		if (row){
			if(row.status=='JALAN' || row.status=='PULANG' || row.status=='CLOSED'){
				$.messager.alert('Error',"Request bisa di cancel, jika status BELUM JALAN!",'error');
			}else{
				$.messager.prompt('Confirm','Are you sure <b>cancel?</b><br>Reason:',function(r){
					if (r){
						$.post('makeproposalGet20160406.php?act=do_cancel&reason='+r,{status:row.status,ocs_id:row.ocs_id},function(result){
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
			$.post('makeproposalGet20160406.php?act=do_destroy',{id:row.ocs_id},function(result){
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
var data;
</script>
<table id="dg" title="MAKE PROPOSAL" class="easyui-datagrid" style="height:450px;width:100%"
data-options="
					rownumbers:true,
					singleSelect:true,
					url:'makeproposalGet20160406.php?act=view',
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
				<th field="ocs_desc" width="200">DESCRIPTION</th>
            </tr>
        </thead>
<thead>
<thead>
<tr>

<th field="date" width="100">DATE</th>
<th field="time" width="100">TIME</th>
<th field="proj_name" width="250">PROJECT</th>
<?if(!$isAdminOrderCarpool){ ?>
<th field="sow_name" width="150">SOW</th>
<th field="site_name" width="150">SITE</th>
<th field="name_dt" width="150">DT NAME</th>
<?}else{?>
<th field="name_dt" width="150">MANAGEMENT</th>
<?}?>
<th field="km_acuan" width="150" align="right">KM ACUAN</th>
<!--<th field="uang_pulsa" width="150" align="right">PULSA</th>-->

<th field="operational" width="150" align="right">OPERATIONAL</th>

<th field="pm_approve" width="150" align="center">PM APP</th>
<th field="finance_approve" width="150" align="center">BUDGET CONTROLL APP</th>
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
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="<?php $f->CheckHakAccess($fua_edit,"verify"); ?>">Verify</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="<?php $f->CheckHakAccess($fua_delete,"destroy"); ?>">Delete</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"cancel_req"); ?>">Cancel</a>
</td>
</tr></table>
</div>
<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
</div>

<div id="dlg" class="easyui-dialog" style="width:75%;height:470px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">
<div class="fitem">
	<label>ID</label>
	<input name="ocs_id" id="ocs_id" class="easyui-textbox" style="width:100px;" readOnly required="true">
</div>
<div class="fitem">
	<label>Date/Time</label>
	<?=$f->InputDate("date");?> 
<select class="easyui-combobox" name="time" style="width:75px;" required="true"
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
        url: 'makeproposalGet20160406.php?act=combo_lke',
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
							url: 'makeproposalGet20160406.php?act=combo_dt&x=2',
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
							url: 'makeproposalGet20160406.php?act=combo_project1',
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

<select class="easyui-combogrid" name="proj_id" id="proj_id" style="width:50%;" required="true"
					data-options="
							panelWidth:400,
							url: 'makeproposalGet20160406.php?act=combo_project',
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
						$('#sow').combogrid({url:'makeproposalGet20160406.php?act=combo_sow&proj_id='+row.id});
						$('#sow').combogrid('setValue', '');
						$('#nip_dt').combogrid({url:'makeproposalGet20160406.php?act=combo_dt&proj_id='+row.id});
						$('#nip_dt').combogrid('setValue', '');
						/*
						$('select[id^=\'nip_dt\']').each(function() {
							$(this).combogrid({url:'makeproposalGet20160406.php?act=combo_dt&proj_id='+row.id});
							$(this).combogrid('setValue', '');
						});
						
						$('select[id^=\'nip_rno\']').each(function() {
							$(this).combogrid({url:'makeproposalGet20160406.php?act=combo_dt&x=1&proj_id='+row.id});
							$(this).combogrid('setValue', '');
						});*/
					}
					">
				</select>
				<input type="hidden" name="lke_id">
		
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
						
						$('select[id^=\'site_id\']').each(function() {
							$(this).combogrid({url:'makeproposalGet20160406.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
							$(this).combogrid('setValue', '');
						});
					}
					">
	</select>
	<!--
	
	var url = 'makeproposalGet20160406.php?act=combo_sow1&proj_id='+row.id;
	$('#cc2').combobox('reload', url);
	<input id="cc2" class="easyui-combobox" data-options="valueField:'sow_id',textField:'sow_name',panelHeight='auto'" style="width:50%;" required="true">-->
	<input type="hidden" name="sow_id">
</div>

<div class="fitem">
	<label>Site Visit</label>
				<select class="easyui-combogrid" name="site_id[]" id="site_id" style="width:70%;"
					data-options="
							panelWidth:400,
							idField:'site_id',
							multiple: true,
							textField:'site_name',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'ck',checkbox:true},
							{field:'site_name',title:'Site',width:'350'}
					]],
					">
				</select>			
</div>
<script type="text/javascript">/*
        				<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-edit',plain:true" onclick="setValue()">SetValue</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:'icon-view',plain:true" onclick="getValue()">GetValue</a>
function setValue(){
            $('#site_id').combogrid('setValues', 
               ['630082','630083','630084']
			);
        }
		function getValue(){
            var val = $('#site_id').combogrid('getValues');
            alert(val);
        }*/
</script> <!--
<div class="fitem">
	<label>Jumlah Site Visit</label>
	<input name="jum_site" class="easyui-numberbox" style="width:50px;"></input>&nbsp;Site
</div>	-->
<div class="fitem">
	<label>Resource</label>
	<select class="easyui-combogrid" name="nip_dt[]" id="nip_dt" style="width:70%;"
				data-options="
						panelWidth:300,
						idField:'nip',
						multiple: true,
						textField:'nm_peg',
						fitColumns:true,
						rownumbers:false,
				columns:[[
						{field:'ck',checkbox:true},
						{field:'nip',title:'NIP',width:'50'},
						{field:'nm_peg',title:'Employee Name',width:'250'},
				]],
			/*	onSelect: function (index,row){
					data=[];
						$('input[name^=\'nip_dt\']').each(function() {						
							data.push({'nip_dt':$(this).val(),'nm_dt':'','result_dt':'','remark_dt':'','status_dt':''});
						});
					}*/
				">
	</select>
</div>
<?php /*
<div class="fitem">
	<label>Site visit</label>	
	<div style="display:inline-block">
	<div id="tt" class="easyui-tabs" data-options="tools:'#tab-tools',plain:true,narrow:true," style="width:550px;height:190px">
	
    </div>

    <div id="tab-tools">
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-add'" onclick="addPanel()"></a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="plain:true,iconCls:'icon-remove'" onclick="removePanel()"></a>
    </div>
	</div>
    <script type="text/javascript">
        var index = 0;
        function addPanel(){
            index++;
            $('#tt').tabs('add',{
                title: 'Site '+index,
                content: '<div style="padding:10px 10px 3px 10px;"><label>Site Name</label>'+
				'<select class="easyui-combogrid" name="site_id['+index+']" id="site_id'+index+'" style="width:50%;"'+
				'	data-options="'+
				'			panelWidth:400,'+
				'			idField:\'site_id\','+
				'			textField:\'site_name\','+
				'			fitColumns:true,'+
				'			rownumbers:false,'+	
				'	columns:[['+
				'			{field:\'site_name\',title:\'Site\',width:\'350\'}'+
				'	]],'+
				'	">'+
				'</select></div><div style="padding:3px 10px;">'+
				'<table id="dg_resource'+index+'" class="easyui-datagrid" title="Site Resources" style="height:auto"'+
				'		data-options="'+
				'			iconCls: \'icon-edit\','+
				'			singleSelect: true,'+
				'			data:data,'+
				'			method:\'get\','+
				'			onClickRow: function(index){onClickRow(index,'+index+');},'+
				'			toolbar: \'#tb\''+
				'		">     '+
				'	<thead>'+
				'		<tr>'+
				'			<th data-options="field:\'nip_dt\',width:80">Nip</th>'+
				'			<th data-options="field:\'nm_dt\',width:100">Nama</th>'+
				'			<th data-options="field:\'result_dt\',width:80,align:\'right\','+
				'					formatter:function(value,row){'+
				'						return row.result_name || value;'+
				'					},'+
				'					editor:{'+
				'						type:\'combobox\','+
				'						options:{'+
				'							valueField:\'result_dt\','+
				'							textField:\'result_name\','+
				'							method:\'get\','+
				'							url:\'makeproposalGet20160406.php?act=list_result\','+
				'							required:true'+
				'						}'+
				'					}">Result</th>'+
				'			<th data-options="field:\'remark_dt\',width:180,align:\'right\',editor:\'text\'">Remark</th>'+
				'			<th data-options="field:\'status_dt\',width:60,align:\'center\','+
				'					editor:{'+
				'						type:\'checkbox\','+
				'						options:{'+
				'							on: \'Verified\','+
				'							off: \'\''+
				'						}'+
				'					}">Status</th>'+
				'		</tr>'+
				'	</thead>'+
				'</table>'+
				'<div id="tb" style="height:auto">'+
				'	<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:\'icon-save\',plain:true" onclick="accept()">Accept</a>'+
				'	<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:\'icon-undo\',plain:true" onclick="reject()">Reject</a>'+
				'	<a href="javascript:void(0)" class="easyui-linkbutton" data-options="iconCls:\'icon-search\',plain:true" onclick="getChanges()">GetChanges</a>'+
				'</div>'+
				'</div>',
                closable: true
            });
			var url='makeproposalGet20160406.php?act=combo_site&proj_id='+$('input[name^=\'proj_id\']').val()+'&sow_id='+$('input[name^=\'sow_id\']').val();
			$('#site_id'+index).combogrid({url:url});
			$('#site_id'+index).combogrid('setValue', '');	
			var panel = $('#dg_resource'+index).datagrid("getPanel");
			panel.find("div.datagrid-header").css("display","none");
			/*	
			url='makeproposalGet20160406.php?act=combo_dt&proj_id='+$('input[name^=\'proj_id\']').val();
			$('#nip_dt'+index).combogrid({url:url});
			$('#nip_dt'+index).combogrid('setValue', '');	
			url='makeproposalGet20160406.php?act=combo_dt&x=1&proj_id='+$('input[name^=\'proj_id\']').val();
			$('#nip_rno'+index).combogrid({url:url});
			$('#nip_rno'+index).combogrid('setValue', '');* /
        }
        function removePanel(){
            var tab = $('#tt').tabs('getSelected');
            if (tab){
                var index = $('#tt').tabs('getTabIndex', tab);
                $('#tt').tabs('close', index);
            }
        }
    </script>
</div>
*/?>
<?php
}
?>
<div class="fitem">
<label>KM Acuan</label>
	<input name="km_acuan" class="easyui-numberbox" style="width:100px;" required="true"></input>
	<!--
	PULSA: <input name="uang_pulsa" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	-->
</div>
<div class="fitem">
	<label>Uang Makan</label>
	<input name="um" class="easyui-numberbox" style="width:100px;" data-options="precision:0,groupSeparator:','" required="true"></input>
	Uang Jalan: <input name="uj" style="width:100px;" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" required="true"></input>
</div>
<div class="fitem">
<label>BBM</label>
	<input name="bbm" class="easyui-numberbox" style="width:20%;" data-options="precision:0,groupSeparator:','" style="width:20%;" required="true"></input>
	Others: <input name="parking" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" required="true" style="width:20%;"></input>
</div>
<!--
<div class="fitem">
<label>Phone Number</label>
<input class="easyui-textbox" name="phone_number" style="width:50%;">
</div>
-->
<div class="fitem">
<label>Description</label>
<input class="easyui-textbox" name="ocs_desc" data-options="multiline:true" style="width:60%;height:80px" required="true">
</div>
<?php /*
<div id="hideShow1">
<div class="fitem">
<label></label>
	<div class="easyui-tabs" style="width:100%;height:90px" border=0>
	 <div title="DT/Surveyor/rigger" style="padding:10px">
    <!-- start table -->
	<p>Evaluasi performance resource, lakukan tekan tombol accept untuk melakukan penyimpanan jika data sudah diupdate.</p>
    
    
	*/?>
	<script type="text/javascript">
        (function($){
            function getCacheContainer(t){
                var view = $(t).closest('div.datagrid-view');
                var c = view.children('div.datagrid-editor-cache');
                if (!c.length){
                    c = $('<div class="datagrid-editor-cache" style="position:absolute;display:none"></div>').appendTo(view);
                }
                return c;
            }
            function getCacheEditor(t, field){
                var c = getCacheContainer(t);
                return c.children('div.datagrid-editor-cache-' + field);
            }
            function setCacheEditor(t, field, editor){
                var c = getCacheContainer(t);
                c.children('div.datagrid-editor-cache-' + field).remove();
                var e = $('<div class="datagrid-editor-cache-' + field + '"></div>').appendTo(c);
                e.append(editor);
            }
            
            var editors = $.fn.datagrid.defaults.editors;
            for(var editor in editors){
                var opts = editors[editor];
                (function(){
                    var init = opts.init;
                    opts.init = function(container, options){
                        var field = $(container).closest('td[field]').attr('field');
                        var ed = getCacheEditor(container, field);
                        if (ed.length){
                            ed.appendTo(container);
                            return ed.find('.datagrid-editable-input');
                        } else {
                            return init(container, options);
                        }
                    }
                })();
                (function(){
                    var destroy = opts.destroy;
                    opts.destroy = function(target){
                        if ($(target).hasClass('datagrid-editable-input')){
                            var field = $(target).closest('td[field]').attr('field');
                            setCacheEditor(target, field, $(target).parent().children());
                        } else if (destroy){
                            destroy(target);
                        }
                    }
                })();
            }
        })(jQuery);
    </script>
    <script type="text/javascript">
        function endEditing(idx){
			if(idx==0){if(!endEditing(1))return false;}
            if (dataDialog[idx].editIdx == undefined||!$(dataDialog[idx].dg).data('datagrid')){
				dataDialog[idx].editIdx=undefined;
				return true
				}
            if ($(dataDialog[idx].dg).datagrid('validateRow', dataDialog[idx].editIdx)){
                var ed = $(dataDialog[idx].dg).datagrid('getEditor', {index:dataDialog[idx].editIdx,field:dataDialog[idx].result});
                var productname = $(ed.target).combobox('getText');
                $(dataDialog[idx].dg).datagrid('getRows')[dataDialog[idx].editIdx]['productname'] = productname;
                $(dataDialog[idx].dg).datagrid('endEdit', dataDialog[idx].editIdx);
                dataDialog[idx].editIdx = undefined;
				expandEnable(idx);
				enableButtonED(idx,'disable');
                return true;
            } else {
                return false;
            }
        }
		
        function onClickRow(index,idx){
			if (dataDialog[idx].editIdx != index){
                if (endEditing(idx)){
					if(idx==0&&getChanges(1)){
						alert('Silahkan simpan dahulu datanya');
						$(dataDialog[idx].dg).datagrid('selectRow', dataDialog[idx].editIdx);
						return;
					}
                    $(dataDialog[idx].dg).datagrid('selectRow', index)
                            .datagrid('beginEdit', index);
                    dataDialog[idx].editIdx = index;
					enableButtonED(idx,'enable');
                } else {
                    $(dataDialog[idx].dg).datagrid('selectRow', dataDialog[idx].editIdx);
                }
            }
        }
		
		/*function getRowParent(index){
			var rows=$(dataDialog[0].dg).datagrid('getRows');
			//var spl=dataDialog[1].dg.split('-');
			var row=rows[index];//divIdxs.indexOf(parseInt(spl[1]))];
			return row;
		}*/
		function addField(obj){
			var row,spl,rows,idx;
			for(var i=obj.length-1;i>=0;i--){
				spl=dataDialog[1].dg.split('-');
				rows=$(dataDialog[0].dg).datagrid('getRows');
				idx=divIdxs.indexOf(parseInt(spl[1]));
				row=rows[idx];
				obj[i]['ocs_id']=row.ocs_id;
				//obj[i]['lke_id']=row.lke_id;
				obj[i]['ocs_site_id']=row.id;
				obj[i]['proj_id']=row.proj_id;
				obj[i]['site_id']=row.site_id;
				obj[i]['result_site']=row.result_site;
				obj[i]['remark_site']=row.remark;
				obj[i]['sow_id']=row.sow_id;	
				obj[i]['idxsite']=idx;
			}
		}
		
		function addFieldIdx(obj,idx){
			var rowIndex;
			for(var i=obj.length-1;i>=0;i--){
				rowIndex = $(dataDialog[idx].dg).datagrid("getRowIndex", obj[i]);
				obj[i]['idx']=rowIndex;			
			}
		}
		function accept(idx){
            if (endEditing(idx)){
				var dg=$(dataDialog[idx].dg);
				var updatedRows = dg.datagrid('getChanges', 'updated');
				var insertedRows = dg.datagrid('getChanges', 'inserted').concat(updatedRows);
				var deletedRows = dg.datagrid('getChanges', 'deleted');
				addFieldIdx(insertedRows,idx);
				if(idx==1){
					addField(insertedRows);addField(deletedRows);
				}
				dg.datagrid('loading');
				$.post(
					'makeproposalGet20160406.php?act=verify&idx='+idx, 
					{
						inserts: insertedRows, 
						//updates: updatedRows, 
						deletes: deletedRows
					}, 
					function(data) {
						var msg='';
						if (data.hasOwnProperty('msg')) {
							msg=JSON.stringify(data.msg);
							$.messager.show({
								title:'Saving Response...',
								msg:msg,
								width:500,
								height:350,
								timeout:0,
								showType:'show',
								style:{
									right:'',
									top:document.body.scrollTop+document.documentElement.scrollTop,
									bottom:''
								}
							});
						}
						else {
							var i;
							//for(i=data.idsite.length-1;i>=0;i--){
							if(idx==1&&data.idsite.length>0){
								$(dataDialog[0].dg).datagrid('updateRow', {
									index: data.idsite[i].idx,
									row:{
										id:data.idsite[i].val
									}
								});
							}
							else{
								for(i=data.id.length-1;i>=0;i--){
									$(dataDialog[idx].dg).datagrid('updateRow', {
										index: data.id[i].idx,
										row:{
											id:data.id[i].val
										}
									});
								}
								dg.datagrid('acceptChanges');
								expandEnable(idx);
							}
						}
						
					}, 
					'json'
				).fail(function() {
					alert("Failed post request!");
				}).always(function() {
					dg.datagrid('loaded');
				});
			}
		}

        function reject(idx){
            if($(dataDialog[idx].dg).data('datagrid')){
				$(dataDialog[idx].dg).datagrid('rejectChanges');
				dataDialog[idx].editIdx = undefined;
				expandEnable(idx);
				enableButtonED(idx,'disable');
			}
        }
        function getChanges(idx){
			if($(dataDialog[idx].dg).data('datagrid')){
				var rows = $(dataDialog[idx].dg).datagrid('getChanges');
				return (rows.length>0);
			}else return false;
        }
		var tombol;
		var dataDialog=[
			{
				dlg:'#dlgAppendSite',fm:'#fmAppendSite',dg:'#dg_resource',
				result:'result_site',editIdx:undefined,title:'FORM ADD/EDIT SITE VISIT'},
			{
				dlg:'#dlgAppendResource',fm:'#fmAppendResource',dg:'',
				result:'result',editIdx:undefined,title:'FORM ADD/EDIT RESOURCE'}];
		
		function appendSite(idx){		    
			tombol=[{
				text:'Append',
				iconCls:'icon-add',
				handler:function(){append(idx);}
			},{
				text:'Close',
				iconCls:'icon-cancel',
				handler:function(){javascript:$(dataDialog[idx].dlg).dialog('close');}
			}];
			$(dataDialog[idx].dlg).dialog({
				title:dataDialog[idx].title,
				buttons:tombol,
				closed: false,
				cache: false,
				modal: true});
			$(dataDialog[idx].dlg).dialog('open');
			$(dataDialog[idx].fm).form('clear');
		}
		
		
		function updateSite(idx){
			var row = $(dataDialog[idx].dg).datagrid('getSelected');
			tombol=[{
				text:'Update',
				iconCls:'icon-edit',
				handler:function(){updateit(idx,row);}
			},{
				text:'Close',
				iconCls:'icon-cancel',
				handler:function(){javascript:$(dataDialog[idx].dlg).dialog('close');}
			}];
			if (row){
				$(dataDialog[idx].dlg).dialog({
				title:dataDialog[idx].title,
				buttons:tombol,
				closed: false,
				cache: false,
				modal: true});
				$(dataDialog[idx].dlg).dialog('open');
				$(dataDialog[idx].fm).form('load',row);
				if(idx==0)$('#appendsite_id').combogrid('setValue', row.site_id);
				else $('#appendnip_dt').combogrid('setValue', row.nip);
			}
		}

		function append(idx){
			if(endEditing(idx)){
					$(dataDialog[idx].dg).datagrid('appendRow',data);
					dataDialog[idx].editIdx = $(dataDialog[idx].dg).datagrid('getRows').length-1;
					$(dataDialog[idx].dg).datagrid('selectRow', dataDialog[idx].editIdx)
							.datagrid('beginEdit', dataDialog[idx].editIdx);
					enableButtonED(idx,'enable');
			}
			expandEnable(idx);
        }
		
		function updateit(idx,selectedrow){
			$(dataDialog[idx].dg).datagrid('endEdit', dataDialog[idx].editIdx);
			dataDialog[idx].editIdx = undefined;
			
			if(idx==0)
				$(dataDialog[idx].dg).datagrid('updateRow', {
				  index: $(dataDialog[idx].dg).datagrid("getRowIndex", selectedrow),
				  row:{
					site_id:data.site_id,
					site_name: data.site_name
				  }
				});
			else{
				$(dataDialog[idx].dg).datagrid('updateRow', {
				  index: $(dataDialog[idx].dg).datagrid("getRowIndex", selectedrow),
				  row:{
					nip:data.nip,
					nm_peg: data.nm_peg
				  }
				});
				expandEnable(idx,'none');
			}
			enableButtonED(idx,'disable');
        }
		
		function removeit(idx){            
			if (dataDialog[idx].editIdx == undefined){return}
            if($(dataDialog[idx].dg).datagrid('getSelected')){
				if(idx==0)divIdxs.splice(dataDialog[idx].editIdx,1);
				$(dataDialog[idx].dg).datagrid('cancelEdit', dataDialog[idx].editIdx)
						.datagrid('deleteRow', dataDialog[idx].editIdx);
				dataDialog[idx].editIdx = undefined;
				expandEnable(idx);
				enableButtonED(idx,'disable');
			}
        }
		
		function expandEnable(idx,display='inline-block'){			
			if(getChanges(idx)||display=='none'){
				display='none';enableButtonSR(idx,'enable');
			}
			else{ 
				display='inline-block';enableButtonSR(idx,'disable');
			}
			if(idx==1){
				
				var spl=dataDialog[1].dg.split('-');
				$(dataDialog[0].dg).datagrid('getExpander',divIdxs.indexOf(parseInt(spl[1]))).css('display',display);
			}
		}
		function enableButtonSR(idx,enable){
			var id1,id2;
			if(idx==1){
				var spl=dataDialog[idx].dg.split('-');
				id1='#save-'+spl[1];
				id2='#abort-'+spl[1];
			}
			else{
				id1='#vsave';
				id2='#vabort';
			}
			
			$(id1).linkbutton(enable);
			$(id2).linkbutton(enable);
		}
		
		
		function enableButtonED(idx,enable){
			var id1,id2;
			if(idx==1){
				var spl=dataDialog[idx].dg.split('-');
				id1='#edit-'+spl[1];
				id2='#del-'+spl[1];
			}
			else{
				id1='#vedit';
				id2='#vdel';
			}
			
			$(id1).linkbutton(enable);
			$(id2).linkbutton(enable);
		}
		var expandIdx;
		function onExpandVerify(idxDg,row){
			var datagrid="#dg_resource";
			if(getChanges(1)){
				alert('Simpan dahulu datanya');
				$(datagrid).datagrid('collapseRow',idxDg);
				return;
			}
			reject(1);
			var idx=divIdxs[idxDg];
			var rows=$(datagrid).datagrid('getRows');
			var rowParen=rows[idxDg];
			//var rowParen=getRowParent(idxDg);
			url='makeproposalGet20160406.php?act=view_detailverify&site='+rowParen.id+'&ocs='+rowParen.ocs_id;
			//alert(JSON.stringify(rowParen)+url);
			$('#ddv-'+idx).datagrid({
				//iconCls: 'icon-edit',
				singleSelect: true,
				url:url,
				method:'get',
				onClickRow: function(index){
						dataDialog[1].dg='#ddv-'+idx;
						onClickRow(index,1);
					},
				toolbar: [{
						text:'Add',
						iconCls:'icon-add',
						handler:function(){
							dataDialog[1].dg='#ddv-'+idx;
							appendSite(1);
						},
						id:'add-'+idx
					},{
						text:'Edit',
						iconCls:'icon-edit',
						disabled:true,
						handler:function(){
							dataDialog[1].dg='#ddv-'+idx;
							updateSite(1);
						},
						id:'edit-'+idx
					},{
						text:'Del',
						iconCls:'icon-remove',
						disabled:true,
						handler:function(){
							dataDialog[1].dg='#ddv-'+idx;
							removeit(1);
						},
						id:'del-'+idx
					},{
						text:'Save',
						iconCls:'icon-save',
						disabled:true,
						handler:function(){
							dataDialog[1].dg='#ddv-'+idx;
							accept(1);
						},
						id:'save-'+idx
					},{
						text:'Abort',
						iconCls:'icon-undo',
						disabled:true,
						handler:function(){
							dataDialog[1].dg='#ddv-'+idx;
							reject(1);
						},
						id:'abort-'+idx
					},
				],
				loadMsg:'Loading...',
				height:'auto',
			//	title:'Resource Performance',
				columns:[[
					{field:'nip',title:'NIP',width:80},
					{field:'nm_peg',title:'Nama',width:100},
					{field:'result',title:'Result',width:80,align:'right',
						formatter:function(value,row){
							return row.result_name || value;
						},
						editor:{
							type:'combobox',
							options:{
								valueField:'result_dt',
								textField:'result_name',
								method:'get',
								url:'makeproposalGet20160406.php?act=list_result',
								required:true
							}
						}
					},
					{field:'remark',title:'Remark',width:180,align:'right',editor:'text'}
				]],
				onResize:function(){
					$(datagrid).datagrid('fixDetailRowHeight',idxDg);
				},
				onLoadSuccess:function(){
					setTimeout(function(){
						$(datagrid).datagrid('fixDetailRowHeight',idxDg);
					},0);
					if(expandIdx!=idxDg)$(datagrid).datagrid('collapseRow',expandIdx);
					expandIdx=idxDg;
				}
			});
			$(datagrid).datagrid('fixDetailRowHeight',idxDg);
		}
    </script> 
	<?php /*
	<!-- end table-->
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

*/
?>
</form>

</div>
<!-- form append site -->
<div id="dlgAppendSite" class="easyui-dialog" style="width:350px;height:170px;padding:0px 0px"
closed="true" data-options="buttons:tombol">
<form id="fmAppendSite" method="post" enctype="multipart/form-data" style="padding:10px 20px;">
<div class="fitem">
	<label>Site Visit</label>
				<select class="easyui-combogrid" name="appendsite_id" id="appendsite_id" style="width:250px;"
					data-options="
							panelWidth:300,
							idField:'site_id',
							textField:'site_name',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:'350'}
					]],
					onSelect: function (index,row){
						data={'proj_id':row.proj_id,'ocs_id':$('#vocs_id').textbox('getValue'),'sow_id':row.sow_id,'site_name':row.site_name,'site_id':row.site_id};
					}
					">
				</select>			
</div>
</form>
</div>
<!-- form append site -->
<div id="dlgAppendResource" class="easyui-dialog" style="width:350px;height:170px;padding:0px 0px"
closed="true" data-options="buttons:tombol">
<form id="fmAppendResource" method="post" enctype="multipart/form-data" style="padding:10px 20px;">
<div class="fitem">
	<label>Resource</label>
	<select class="easyui-combogrid" name="appendnip_dt" id="appendnip_dt" style="width:70%;"
				data-options="
						panelWidth:300,
						idField:'nip',
						textField:'nm_peg',
						fitColumns:true,
						rownumbers:false,
				columns:[[
						{field:'nip',title:'NIP',width:'50px'},
						{field:'nm_peg',title:'Employee Name',width:'250px'},
				]],
				onSelect: function (index,row){
					data={'nip':row.nip,'nm_peg':row.nm_peg,'result':'','remark':''};
				}
				">
	</select>
</div>
</form>
</div>
<!-- form verify -->
<div id="dlgVerify" class="easyui-dialog" style="width:700px;height:470px;padding:0px 0px"
closed="true">
<form id="fmVerify" method="post" enctype="multipart/form-data" style="padding:10px 20px;">
<div class="fitem">
	<label>ID</label>
	<input name="ocs_id" id="vocs_id" class="easyui-textbox" style="width:100px;" readOnly required="true">
</div>
<div class="fitem">
	<label>Resource Performance</label><br>
	<table id="dg_resource" class="easyui-datagrid" title="Site Resources" style="height:auto"
			data-options="
				view: detailview,
				detailFormatter: function(index,row){
					var idx=divIdxs.length;
					if(index==idx){
						if(idx==0)divIdxs.push(idx);else divIdxs.push(divIdxs[idx-1]+1);
						idx=divIdxs[idx];
					}
					else
						idx=divIdxs[index];
					return '<div style=padding:2px;><table id=ddv-'+idx+'></table></div>';
				},
				onExpandRow: function(index,row){
					onExpandVerify(index,row);
				},
                iconCls: 'icon-edit',
                singleSelect: true,
                toolbar: '#tb',
                method: 'get',
                onClickRow: function(index){onClickRow(index,0);}            
			">     
		<thead>
			<tr>
				<th data-options="field:'site_name',width:150">Nama Site</th>
				<th data-options="field:'result_site',width:100,align:'right',
						formatter:function(value,row){
							return row.result_name || value;
						},
						editor:{
							type:'combobox',
							options:{
								valueField:'result_dt',
								textField:'result_name',
								method:'get',
								url:'makeproposalGet20160406.php?act=list_result',
								required:true
							}
						}">Result</th>
				<th data-options="field:'remark',width:230,align:'right',editor:'text'">Remark</th>
				
			</tr>
		</thead>
	</table>
</div>
</form>
</div>
<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>

<div id="tb" style="height:auto">
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="id:'vadd',iconCls:'icon-add',plain:true" onclick="appendSite(0)">Add</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="id:'vedit',iconCls:'icon-edit',plain:true,disabled:true" onclick="updateSite(0)">Edit</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="id:'vdel',iconCls:'icon-remove',plain:true,disabled:true" onclick="removeit(0)">Del</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="id:'vsave',iconCls:'icon-save',plain:true,disabled:true" onclick="accept(0)">Save</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" data-options="id:'vabort',iconCls:'icon-undo',plain:true,disabled:true" onclick="reject(0)">Abort</a>
    </div>