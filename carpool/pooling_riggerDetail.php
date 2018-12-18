<?php
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$template->basicheader(2);
$id=$_REQUEST['v'];
$rigger=$dbproj->getOne("select (select nm_peg from spg_data_current where nip=m_pooling_rigger.nip) from m_pooling_rigger where id='$id'");
$nip=$dbproj->getOne("select nip from m_pooling_rigger where id='$id'");
$prg_id=$dbproj->getOne("select prg_id from m_pooling_rigger where id='$id'");
$proj_id=$dbproj->getOne("select proj_id from m_pooling_rigger where id='$id'");
$sow_id=$dbproj->getOne("select sow_id from m_pooling_rigger where id='$id'");
$proj_name=$dbproj->getOne("select (select proj_name from m_project where id=m_pooling_rigger.proj_id) from m_pooling_rigger where id='$id'");
?>
<table id="dg" class="easyui-datagrid" style="width:100%;height:300px"
            title="RIGGER A/N: <?=$rigger;?>, <?=$proj_name;?>"
			data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'pooling_riggerGet.php?act=view_detail&id=<?=$id?>',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:10,
					pageList: [10,20,30,40,50,100],
					showFooter:true,
					fitColumns:true
					"
			>

        <thead>
            <tr>
				<th field="site_name" width="60">SITE NAME</th>
				<th field="allowance" width="20" align="right">ALLOWANCE</th>
				<th field="status" width="20"  align="center">STATUS</th>
            </tr>
        </thead>
    </table>
	<div id="toolbar">
		<table width="100%" cellpadding=0 cellspacing=0>
			<tr><td>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="insertx()">insert</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="updatex()">update</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="deletex()">Delete</a>
			</td>
			<td align="right">
			</td>
			</tr>
		</table>
	</div>

<div id="dlg" class="easyui-dialog" style="width:70%;height:220px;padding:0px 0px"
closed="true" buttons="#dlg-buttons">

<form id="fm" method="post" enctype="multipart/form-data">

<div class="fitem">
<label>RIGGER</label>
	<input class="easyui-textbox" style="width:65%;" value="<?=$rigger;?>">
	<input type="hidden" name="nip" style="width:65%;" value="<?=$nip;?>">
	<input type="hidden" name="prg_id" style="width:65%;" value="<?=$prg_id;?>">
</div>

<div class="fitem">
<label>PROJECT</label>
	<input class="easyui-textbox" style="width:65%;" value="<?=$proj_name;?>">
</div>

<div class="fitem">
<label>SITE NAME</label>
<select class="easyui-combogrid" name="site_name" id="site_name" style="width:65%;" required="true"
					data-options="
							panelHeight:150,
							url:'pooling_riggerGet.php?act=combo_site&proj_id=<?=$proj_id?>&sow_id=<?=$sow_id?>',
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:100}
					]],
					onSelect: function (index,row){
						document.getElementById('site_id').value=row.site_id;
					}
					">
				</select>
				<input type="hidden" name="site_id" id="site_id">
</div>
<div class="fitem">
<label>STATUS</label>
<select class="easyui-combobox" name="status" id="status" style="width:65%;" data-options="panelHeight:'auto'" required="true">
                <option value="NOT DONE" selected>NotDone</option>
                <option value="DONE">Done</option>
</select>
</div>
</form>
</div>
<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>

<script type="text/javascript">
	function insertx(){
		$('#site_name').combogrid('setValue', '');$('#status').combobox('setValue', '');
		$('#fm').form('load',{
					site_id:''
				});
		$('#dlg').dialog('open').dialog('setTitle','ADD');
		url = 'pooling_riggerGet.php?act=do_addDetail';	
	}
		
		function updatex(){
			var row = $('#dg').datagrid('getSelected');
			if (row){
				$('#dlg').dialog('open').dialog('setTitle','EDIT');	
				$('#site_name').combogrid('setValue', row.site_name);
				$('#status').combobox('setValue', row.status);
				$('#fm').form('load',{
					site_id:row.site_id
				});
				url = 'pooling_riggerGet.php?act=do_update&id='+row.id;
			}			
		}
		function deletex(){
			var row = $('#dg').datagrid('getSelected');
			if (row){
				$.messager.confirm('Confirm','Are you sure you want to delete this data?',function(r){
					if (r){
						$.post('pooling_riggerGet.php?act=do_destroydetail',{id:row.id},function(result){
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
		function Save(){
			$('#fm').form('submit',{
				url: url,
				onSubmit: function(){
					return $(this).form('validate');
					},
					success: function(result){
					var result = eval('('+result+')');
					if (result.errorMsg){
						$.messager.alert('Error',result.errorMsg,'error');
					} else {
						$('#dlg').dialog('close');
						$('#dg').datagrid('reload');
					}
				}
			});
		}
</script>
