<?php
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$template->basicheader(2);
?>
<table id="dg" style="width:100%;height:475"
            title="POOLING RIGGER</b>"
			data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'pooling_riggerGet.php?act=view',
					method:'post',
					pagination:true,
					toolbar:'#toolbar',
					striped: true,
					nowrap: false,
					pageSize:100,
					pageList: [10,20,30,40,50,100],
					showFooter:true,
					fitColumns:true
					"
			>

        <thead>
            <tr>
				<th field="prg_id" width="10">PRG_ID</th>
				<th field="req_date" width="15">DATE</th>
				<th field="name" width="25">NAME</th>
				<th field="proj_name" width="25">PROJECT</th>
				<th field="sewa_motor" width="15" align="right">SEWA MOTOR</th>
				<th field="next_approval" width="15" align="center">STATUS</th>
				<th field="detail" width="10" align="center">OPTION</th>
            </tr>
        </thead>
    </table>
	<div id="toolbar">
		<table width="100%" cellpadding=0 cellspacing=0>
			<tr><td>
				<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" 
						onclick="<?php $f->CheckHakAccess($fua_add,"insert"); ?>">Insert</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-remove" plain="true" 
						onclick="<?php $f->CheckHakAccess($fua_delete,"cancelx"); ?>">Cancel</a>
			</td>
			<td align="right">
			</td>
			</tr>
		</table>
	</div>
	
	<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
	</div>

    <script type="text/javascript">
	function doSearch(value,name){
			$('#dg').datagrid('load',{
				value		 	: value,
				name		 	: name
			});
		}
       		
	function Status(val,row){
	
		if (val == 'BELUM JALAN'){
			return '<span style="color:red;">'+val+'</span>';
		}else if (val == 'JALAN'){
			return '<span style="color:green;">'+val+'</span>';
		}else if (val == 'PULANG'){
			return '<span style="color:blue;">'+val+'</span>';
		}else {
			return '<span style="color:black;">'+val+'</span>';
		}
		
	}
	function detailpool(v){
		window.open("pooling_riggerDetail.php?v="+v, "_blank", "top=100,left=300,width=800,height=350");
	}
	$(function(){ $('#dg').datagrid();});
    </script>

<div id="dlg1" class="easyui-dialog" style="width:65%;height:360px;padding:10px 10px" closed="true" buttons="#dlg-buttons1">
	<form id="ff" method="post" enctype="multipart/form-data">	
		<div class="fitem">
            <label>PLAN DATE</label>
			<?=$f->InputDate("req_date",0,0,0,true,1);?>
		</div>
		<div class="fitem">
            <label>RIGGER</label>
				<select class="easyui-combogrid" name="nip" id="nip" style="width:60%" required="true" data-options="
                    panelWidth: 500,
                    idField: 'nip',rownumbers:true,
                    textField: 'nm_peg',
                    url: 'pooling_riggerGet.php?act=combo_rigger',
                    mode:'remote',
					fitColumns:true,
                    columns: [[
                        {field:'nip',title:'NIP',width:20},
                        {field:'nm_peg',title:'NM PEG',width:80}
                    ]],
					onSelect: function (index,row){
						$('#ff').form('load',{sewa_motor:row.sewa_motor,sewa_motor1:row.sewa_motor});
					}
                ">
				</select>
				<input type='hidden' name='sewa_motor1' id='sewa_motor1' >
        </div>
        <div class="fitem">
            <label>PROJECT</label>
				<select class="easyui-combogrid" name="project" id="project" style="width:60%" required="true" data-options="
                    panelWidth: 600,
                    idField: 'text',rownumbers:true,
                    textField: 'text',
                    url: 'pooling_riggerGet.php?act=combo_project',
                    mode:'remote',
					nowrap: false,
					fitColumns:true,
                    columns: [[
                        {field:'proj_code',title:'PROJ_CODE',width:30},
                        {field:'proj_name',title:'PROJ_NAME',width:40},
						{field:'sow_name',title:'SOW',width:40}
                    ]],
					onSelect: function (index,row){
						$('#ff').form('load',{proj_id:row.proj_id,sow_id:row.sow_id});
						$('#site_name1').combogrid({url:'pooling_riggerGet.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
						$('#site_name2').combogrid({url:'pooling_riggerGet.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
						$('#site_name3').combogrid({url:'pooling_riggerGet.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
						$('#site_name4').combogrid({url:'pooling_riggerGet.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
						$('#site_name5').combogrid({url:'pooling_riggerGet.php?act=combo_site&proj_id='+row.proj_id+'&sow_id='+row.sow_id});
					}
                ">
				</select>
				<input type="hidden" name="proj_id" id="proj_id"><input type="hidden" name="sow_id" id="sow_id">
        </div>
		<div class="fitem">
            <label>SEWA MOTOR</label>
            <input name="sewa_motor" id="sewa_motor" class="easyui-numberbox" 
			data-options="precision:0,groupSeparator:','" style="width:30%;text-align:right;" required="true" readOnly>
			<input type="checkbox" id="myCheck" onclick="myFunction()" /> With DT
			<input type="checkbox" id="myCheck1" onclick="myFunction1()" /> Dedicate Car
			<script> 
				function myFunction() {
					var x = document.getElementById("myCheck").checked;
					var y = document.getElementById("sewa_motor1").value;
					if(x==true){
							$('#ff').form('load',{sewa_motor:0});
						}else{
							$('#ff').form('load',{sewa_motor:y});
						}
				}function myFunction1() {
					var x = document.getElementById("myCheck1").checked;
					var y = document.getElementById("sewa_motor1").value;
					if(x==true){
							$('#ff').form('load',{sewa_motor:0});
						}else{
							$('#ff').form('load',{sewa_motor:y});
						}
				}
			</script>
        </div>
		<div class="fitem">
		<label>SITE 1</label>
		<select class="easyui-combogrid" name="site_name[]" id="site_name1" style="width:60%;" required="true"
					data-options="
							
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:100}
					]],
					onSelect: function (index,row){
						document.getElementById('site_1').value=row.site_id;
					}
					">
				</select>
				<input type="hidden" name="site[]" id="site_1">
		</div>
		<div class="fitem" id="hd2">
		<label>SITE 2</label>
		<select class="easyui-combogrid" name="site_name[]" id="site_name2" style="width:60%;" 
					data-options="
							
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:100}
					]],
					onSelect: function (index,row){
						document.getElementById('site_2').value=row.site_id;
					}
					">
				</select>
				<input type="hidden" name="site[]" id="site_2">
		</div>
		<div class="fitem" id="hd3">
		<label>SITE 3</label>
		<select class="easyui-combogrid" name="site_name[]" id="site_name3" style="width:60%;" 
					data-options="
							
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:100}
					]],
					onSelect: function (index,row){
						document.getElementById('site_3').value=row.site_id;
					}
					">
				</select>
				<input type="hidden" name="site[]" id="site_3">
		</div>
		<div class="fitem" id="hd4">
		<label>SITE 4</label>
		<select class="easyui-combogrid" name="site_name[]" id="site_name4" style="width:60%;" 
					data-options="
							
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:100}
					]],
					onSelect: function (index,row){
						document.getElementById('site_4').value=row.site_id;
					}
					">
				</select>
				<input type="hidden" name="site[]" id="site_4">
		</div>
		<div class="fitem" id="hd5">
		<label>SITE 5</label>
		<select class="easyui-combogrid" name="site_name[]" id="site_name5" style="width:60%;" 
					data-options="
							
							idField:'site_name',
							textField:'site_name',
							mode:'remote',
							fitColumns:true,
							rownumbers:false,
					columns:[[
							{field:'site_name',title:'Site',width:100}
					]],
					onSelect: function (index,row){
						document.getElementById('site_5').value=row.site_id;
					}
					">
				</select>
				<input type="hidden" name="site[]" id="site_5">
		</div>
				
	</form>
</div>
<div id="dlg-buttons1">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save1()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')" style="width:90px">Cancel</a>
</div>

<script>
function insert(){
	//$("#hd2").hide();$("#hd3").hide();$("#hd4").hide();$("#hd5").hide();
	$('#ff').form('clear');
	$('#dlg1').dialog('open').dialog('setTitle','ADD');
	url = 'pooling_riggerGet.php?act=do_add';	
}function prosess_pulang(){
	$("#hideShow").show();
	var row = $('#dg').datagrid('getSelected');
	if (row){
		
			$('#ff').form('clear');
			$('#ff').form('load',{
			nip_driver1:row.nip_driver,
			kode_kendaraan1:row.kode_kendaraan1,
			km_start1:row.km_start,
			bbmRp:row.bbmRp,
			etollRp:row.etollRp
			});
			$('#dlg1').dialog('open').dialog('setTitle','Payment BBM/ETOLL');
			url = 'RequestCarpoolGet.php?act=prosess_opupdate&ocs_id='+row.id;	
	}
	
}
function deletex(){
			var row = $('#dg').datagrid('getSelected');
			if (row){
				$.messager.confirm('Confirm','Are you sure you want to delete this data?',function(r){
					if (r){
						$.post('pooling_riggerGet.php?act=do_destroy',{prg_id:row.id},function(result){
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
function cancelx(){
			var row = $('#dg').datagrid('getSelected');
			if (row){
				$.messager.confirm('Confirm','Are you sure you want to cancel this data?',function(r){
					if (r){
						$.post('pooling_riggerGet.php?act=do_cancel',{prg_id:row.id},function(result){
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
function Save1(){
	$('#ff').form('submit',{
		url: url,
		onSubmit: function(){
			return $(this).form('validate');
			},
			success: function(result){
			var result = eval('('+result+')');
			if (result.errorMsg){
				$.messager.alert('Error',result.errorMsg,'error');
			} else {
				$('#dlg1').dialog('close'); // close the dialog
				$('#dg').datagrid('reload'); // reload the user data
			}
		}
	});
}

</script>
