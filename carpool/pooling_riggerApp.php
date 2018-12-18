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
					singleSelect:false,
					collapsible: true,
					url:'pooling_riggerGetNew.php?act=view_app',
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
				<th data-options="field:'ck',checkbox:true"></th>
				<th field="prg_id" width="10">PRG_ID</th>
				<th field="req_date" width="10">DATE</th>
				<th field="name" width="20">NAME</th>
				<th field="proj_name" width="20">PROJECT</th>
				<th field="area_site" width="10">AREA</th>
				<th field="transport" width="15">TRANSPORT</th>
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
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="approvalx()">Approved</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="unapproved()">Reject</a>
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
       		
function detailpool(v){
		window.open("pooling_riggerD1.php?v="+v, "_blank", "top=100,left=300,width=800,height=350");
	}	

function approvalx(){
	var prg = [];
	var rows = $('#dg').datagrid('getSelections');
	for(var i=0; i<rows.length; i++){
			prg.push(rows[i].id);
	}
	if(prg.join(',')==""){
		$.messager.alert('Error','Please checklist this data approval!','error');
	}else{
		$.messager.confirm('Confirm','are you sure, you want to approve this data?',function(r){
			if (r){
				$.post('pooling_riggerGet.php?act=do_approvedx',{prg_id:prg.join(',')},function(result){
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
   
function approved(){
			var row = $('#dg').datagrid('getSelected');
			if (row){
				$.messager.confirm('Confirm','are you sure, you want to approve this data?',function(r){
					if (r){
						$.post('pooling_riggerGet.php?act=do_approved',{prg_id:row.id},function(result){
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
function unapproved(){
		var row = $('#dg').datagrid('getSelected');
		if (row){
            $.messager.prompt('REJECT', 'Are you sure you want to cancel this data? <br><b>Please, enter a reason !!!', function(r){
                if (r){
                    $.post('/pooling/payment_riggerGet.php?act=do_reject',{reason:r,prg_id:row.id},function(result){
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
$(function(){ $('#dg').datagrid();});
</script>
