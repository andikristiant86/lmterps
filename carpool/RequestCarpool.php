<?php
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
$template->basicheader(2);
include_once($DOCUMENT_ROOT."/project/report/check_saldo_carpool.php");
?>
<table id="dg" style="width:100%;height:520"
            title=":: PAYMENT CARPOOL</font></b>"
			data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'RequestCarpoolGetnew.php?act=view_payment',
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

        <thead>
            <tr>
				<th field="ocs_id" width="100">ID</th>
				<!--<th field="no_polisi" width="90">No. POL</th>-->
				<th field="date" width="100">REQ. DATE</th>
                
                <!--<th field="site_name" width="100">SITE NAME</th>-->
                <th field="proj_name" width="170">PROJECT</th>
				<th field="dtc_name" width="120">CREATED BY</th>
                <th field="dt_name" width="130">DT/MANAGEMENT</th>
				<!--<th field="rno_name" width="150">RNO NAME</th>-->
				<th field="ocs_desc" width="140">DESCRIPTION</th>
                <th field="sts_nm" width="100" align="center" formatter="Status">STATUS</th>
				<th field="operational" width="75" align="right">OP</th>
            </tr>
        </thead>
    </table>
	<div id="toolbar">
		<table width="100%" cellpadding=0 cellspacing=0>
			<tr><td>
				From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
				<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
			</td>
			<td align="right">
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-ok" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"prosess_paid"); ?>">Paid</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" plain="true" onclick="<?php $f->CheckHakAccess($fua_edit,"prosess_reject"); ?>">Reject</a>
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-print" plain="true" onclick="print()">Print</a>
			</td>
			</tr>
		</table>
	</div>
	
	<div id="mm">
		<div data-options="name:'all',iconCls:'icon-ok'">All Category</div>
		<div data-options="name:'ocs_id'">OCS ID</div>
		<div data-options="name:'time'">Time</div>
		<div data-options="name:'dtc_name'">DT Coordinator</div>
		<div data-options="name:'dt_name'">DT Name</div>
		<div data-options="name:'rno_name'">RNO Name</div>
		<div data-options="name:'proj_name'">Project Name</div>
		<div data-options="name:'ots_description'">Job Description</div>
		<div data-options="name:'sts_nm'">Status</div>
	</div>
<div id="dlg" class="easyui-dialog" style="width:95%;height:450px;padding:0px 0px" closed="true" buttons="#dlg-buttons">
	<form id="fm" method="post" enctype="multipart/form-data">	
<div id="hideShow1">
	<div class ="fitem">
		<label>OCS ID</label>
		<input name="x1" class="easyui-textbox" style="width:75%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>OPEN DATE</label>
		<input name="x2" class="easyui-textbox" style="width:75%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>TIME</label>
		<input name="x3" class="easyui-textbox" style="width:75%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>SITE NAME</label>
		<input name="x4" class="easyui-textbox" style="width:50%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>PROJECT NAME</label>
		<input name="x5" class="easyui-textbox" style="width:50%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>SOW NAME</label>
		<input name="x10" class="easyui-textbox" style="width:50%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>DT COORDINATOR</label>
		<input name="x6" class="easyui-textbox" style="width:50%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>DT NAME</label>
		<input name="x8" class="easyui-textbox" style="width:50%;" readOnly></input>
	</div>
	<div class ="fitem">
		<label>TOTAL</label>
		<input name="x7" class="easyui-textbox" style="width:50%;" readOnly></input>
	</div>
	
	<div class ="fitem">
		<label>STATUS</label>
		<input name="x9" class="easyui-textbox" style="width:30%;" readOnly></input>
	</div>
	
	<div class ="fitem">
		<label>DESCRIPTION</label>
		<input name="x11" class="easyui-textbox" style="width:30%;" readOnly></input>
	</div>
	
	<div class ="fitem">
	<label>DATE OF RETURN</label>
		<?=$f->inputDate("date_pulang","","","",false);?> Time: <input class="easyui-timespinner" name="time_pulang" data-options="min:'06:00',max:'23:59'" style="width:80px;"></input>
	</div>	
</div>
<div id="hideShow2">

	<div class ="fitem">
	<label>DATE OUT</label>
		<?=$f->inputDate("date_pergi");?> Time: <input class="easyui-timespinner" name="time_pergi" data-options="min:'06:00',max:'23:59'" style="width:80px;" required="true"></input>
	</div>

	<div class="fitem">
	<label>DRIVER</label>
	<select class="easyui-combogrid" name="nm_driver" id="nm_driver" style="width:50%;" required="true"
	data-options="
								panelWidth:400,
								url: 'RequestCarpoolGetnew.php?act=combo_driver',
								idField:'nip',
								textField:'nm_peg',
								mode:'remote',
								fitColumns:true,
								rownumbers:false,
						columns:[[
								{field:'nip',title:'NIP',width:'100'},
								{field:'nm_peg',title:'Driver Name',width:'299'}
						]],
						onSelect: function (index,row){
							$('#fm').form('load',{nip_driver:row.nip});
						}
						">
					
					</select>
	<input type="hidden" name="nip_driver">
	</div>

	<div class="fitem">
	<label>CAR NUMBER</label>
	<select class="easyui-combogrid" name="car_name" id="car_name" required="true" style="width:50%;"
	data-options="
								panelWidth:400,
								url: 'RequestCarpoolGetnew.php?act=combo_car123',
								idField:'kode_kendaraan',
								textField:'no_polisi',
								mode:'remote',
								fitColumns:true,
								rownumbers:false,
						columns:[[
								{field:'kode_kendaraan',title:'ID Car',width:'100'},
								{field:'no_polisi',title:'Police Numbers',width:'299'}
						]],
						onSelect: function (index,row){
							$('#fm').form('load',{car_number:row.kode_kendaraan});
						}
						">
					</select>
		<input type="hidden" name="car_number">			
	</div>

	<div class="fitem">
	<label>DESCRIPTION</label>
	<input name="description" id="description" data-options="multiline:true" class="easyui-textbox" style="width:75%;height:35px" required="true">
	</div>

	<div class="fitem">
	<label>REMAKS</label>
	<input name="remaks" class="easyui-textbox" style="width:75%;"></input>
	</div>

	<div class="fitem">
	<label>KM START</label>
	<input name="km_start" id="km_start" class="easyui-numberbox" required="true" style="width:20%" data-options="precision:0,groupSeparator:','"></input>

	<label>KM END</label>
	<input name="km_end" class="easyui-numberbox"  data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	</div>


	<div class="fitem">
	<label>UANG MAKAN</label>

	<input name="um" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	<label>UANG JALAN</label>
	<input name="uj" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	</div>
	
	<div class="fitem">
	<label>BBM RP</label>
	<input name="bbm" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>


	<label>BBM LTR</label>
	<input name="bbm_ltr" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	</div>
	<div class="fitem">
	<label>PARKING</label>
	<input name="parking" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	<label>PORTAL</label>
	<input name="portal" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	</div>
	<div class="fitem">
	<label>MANUAL TOLL</label>
	<input name="mtoll" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	<label>E-TOLL</label>

	<input name="etoll" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	</div>

	<div class="fitem">
	<label>THREE IN ONE</label>
	<input name="three_in_one" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	<label>OTHERS</label>
	<input name="others" id="others" class="easyui-textbox" style="width:20%;">
	</div>

	<div class="fitem">
	<label>TAMBAL BAN</label>
	<input name="utb" id='utb' class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;"></input>
	</div>
</div>
</form>
</div>
<div id="dlg-buttons">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg').dialog('close')" style="width:90px">Cancel</a>
</div>

    <script type="text/javascript">
	function doSearch(value,name){
			var start_date 	= $('#str_date').val();
			var end_date 	= $('#upto_date').val();
			$('#dg').datagrid('load',{
				value		 	: value,
				name		 	: name,
				f_start_date 	: start_date,
				f_end_date 		: end_date
			});
		}
        $(function(){
            $('#dg').datagrid({
                view: detailview,
				detailFormatter:function(index,row){
					return '<div style="padding:2px"><table id="ddv-'+index+'"></table></div>';
				},
				onExpandRow: function(index,row){
					var ddv = $(this).datagrid('getRowDetail',index).find('table.ddv');
					
						$('#ddv-'+index).datagrid({
                        url:'RequestCarpoolGetnew.php?act=detailview&ocs_id='+row.ocs_id,
						//MilestoneGet.php?act=detailview&itemid='+row.proj_id+'&template='+row.template+'&sow_id='+row.sow_id,
                        fitColumns:false,
                        singleSelect:true,
                        rownumbers:true,
                        loadMsg:'Loading...',
                        height:'auto',
						pagination:true,
						nowrap: true,
						title:'Request Carpool',
                        frozenColumns:[[
							
							{field:'driver_name',title:'Driver',width:150},
							{field:'no_police',title:'Car Number',width:150},
							]],
						columns:[[
						{field:'date_berangkat',title:'Date of departure',width:120},
                            {field:'km_start',title:'KM Start',width:100},
							{field:'km_end',title:'KM End',width:100},
                            {field:'um',title:'UM',width:120,align:'right'},
						{field:'remaks',title:'REMAKS',width:120,align:'right'},
						{field:'three_in_one',title:'Three In One',width:120,align:'right'},
						{field:'bbm',title:'BBM',width:120,align:'right'},
						{field:'bbm_ltr',title:'BBM_LTR',width:120,align:'right'},
                            {field:'uj',title:'UJ',width:120,align:'right'},
							{field:'parking',title:'Parking',width:120,align:'right'},
							{field:'portal',title:'Portal',width:120,align:'right'},
							{field:'mtoll',title:'MToll',width:120,align:'right'},
							{field:'etoll',title:'EToll',width:120,align:'right'},
							{field:'utb',title:'UTB',width:120,align:'right'},
							{field:'others',title:'Others',width:120,align:'right'},
							{field:'description',title:'Description',width:250},
							{field:'total',title:'Total',width:150,align:'right'}
                        ]],
                        onResize:function(){
                            $('#dg').datagrid('fixDetailRowHeight',index);
                        },
                        onLoadSuccess:function(){
                            setTimeout(function(){
                                $('#dg').datagrid('fixDetailRowHeight',index);
                            },0);
                        },
						toolbar: [{
									text:'Add',
									iconCls:'icon-add',
									handler: function(){
										<?php
											$f->CheckHakAccess($fua_add,"addDetail");
										?>
										}
								},'-',{
									text:'Edit',
									iconCls:'icon-edit',
									handler: function(){
										<?php
											$f->CheckHakAccess($fua_edit,"editDetail");
										?>}
								}/*,'-',{
									text:'Delete',
									iconCls:'icon-remove',
									handler: function(){
										<?php
											$f->CheckHakAccess($fua_delete,"removeDetail");
										?>
									}
								}*/]
                    });
                    $('#dg').datagrid('fixDetailRowHeight',index);
					function addDetail(){
						$('#nm_driver').combogrid('grid').datagrid('reload');
						$('#car_name').combogrid('grid').datagrid('reload');
						// if(row.status==2 || row.status==3){
							// $.messager.alert('Error',"Sorry, you don't have permission to insert this data!",'error');
						// }else{
							$('#btnSave').linkbutton('enable');
							$("#hideShow1").hide();
							$("#hideShow2").show();
							$('#dlg').dialog('open').dialog('setTitle',':: Payment CARPOOL');
							$('#fm').form('clear');
							$('#fm').form('load',{
										description:row.ocs_desc
									});
							url = 'RequestCarpoolGetnew.php?act=do_add&ocs_id='+row.ocs_id;
							v_index=index;
							xload=1;
						//}
					}
					function editDetail(){
						$('#nm_driver').combogrid('grid').datagrid('reload');
						$('#car_name').combogrid('grid').datagrid('reload');
						var rowx = $('#ddv-'+index).datagrid('getSelected');
						if (rowx){
							if(row.sts_nm=='BELUM JALAN'){
								$.messager.alert('Error',"Maaf, status mobil belum JALAN!",'error');
							}
							// else if(row.sts_nm=='JALAN'){
								// $.messager.alert('Error',"Maaf, data tidak bisa diubah! <br><b>set in terlebih dahulu</b>",'error');
							// }
							else{
								$('#btnSave').linkbutton('enable');
								$("#hideShow1").hide();
								$("#hideShow2").show();
								$('#dlg').dialog('open').dialog('setTitle',':: Payment Carpool');
								$('#fm').form('load',rowx);
								url = 'RequestCarpoolGetnew.php?act=do_update&id='+rowx.id;
									$('#fm').form('load',{
										nm_driver:rowx.driver_name,
										car_name:rowx.no_police
									});
								
								v_index=index;
								xload=1;
							}	
						}
					}
					
					$('#btnSave').linkbutton('enable');
					
					function removeDetail(){
						var rowx = $('#ddv-'+index).datagrid('getSelected');
						if (rowx){
							if(row.sts_nm=='JALAN' || row.sts_nm=='PULANG' || row.sts_nm=='CLOSED'){
								$.messager.alert('Error',"Sorry, you don't have permission to delete this data!",'error');
							}else{
								$.messager.confirm('Confirm','Are you sure you want to delete?',function(r){
									if (r){
										$.post('RequestCarpoolGetnew.php?act=removeDetail',{id:rowx.id},function(result){
											if (result.success){
												$('#ddv-'+index).datagrid('reload');	// reload the user data
											} else {
												$.messager.show({	// show error message
													title: 'Error',
													msg: result.msg
												});
											}
										},'json');
									}
								});
							}
						}
					}
                }
            });
        });
		
	function Save(){
			$('#fm').form('submit',{
				url: url,
				onSubmit: function(){
					return $(this).form('validate');
				},
				success: function(result){
					var result = eval('('+result+')');
					if (result.success){
						$('#dlg').dialog('close');
						/*if(xload==1){
							$('#ddv-'+v_index).datagrid('reload');
						}else if(xload==2){*/
							$('#dg').datagrid('reload');
						//}
					}else{
						$.messager.alert('Error',result.errorMsg,'error');
					}
				}
			});
	}
		
	function Status(val,row){
	
		if (val == 'PAYMENT'){
			return '<span style="color:red;">'+val+'</span>';
		}else{
			return '<span style="color:green;">'+val+'</span>';
		}
		
	}
/*	
function prosess_jalan1(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
		if(row.status==1){
			$.messager.alert('Error',"Sorry, you don't have permission!",'error');
		}else if(row.status==2){
			$.messager.alert('Error',"Sorry, you don't have permission!",'error');
		}else{
			$.messager.confirm('Confirm','Prosess set out?',function(r){
			if (r){
				$.post('RequestCarpoolGetnew.php?act=prosess_jalan',{ocs_id:row.ocs_id},function(result){
					if (result.success){
						$('#dg').datagrid('reload'); // reload the user data
					} else {
						$.messager.show({ // show error message
						title: 'Error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
			});
		}
	}
}*/
function prosess_reject(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
			if(row.sts_nm=='PAID'){
				$.messager.alert('Error','Permintaan sudah dibayarkan, tidak boleh di reject!','error');
			}else{
			$.messager.confirm('Confirm','Process reject?',function(r){
			if (r){
				$.post('RequestCarpoolGetnew.php?act=prosess_reject',{ocs_id:row.ocs_id},function(result){
					if (result.success){
						$('#dg').datagrid('reload'); // reload the user data
					} else {
						$.messager.show({ // show error message
						title: 'Error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
			});
		}
	}
}
function prosess_paid(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
			$.messager.confirm('Confirm',"Tanggal dibayar <b><?=date('d/m/Y');?></b>?",function(r){
			if (r){
				$.post('RequestCarpoolGetnew.php?act=prosess_paid',{ocs_id:row.ocs_id},function(result){
					if (result.success){
						$('#dg').datagrid('reload'); // reload the user data
					} else {
						$.messager.show({ // show error message
						title: 'Error',
						msg: result.errorMsg
						});
					}
				},'json');
			}
			});
		
	}
}
/*
function prosess_pulang(){
	$("#hideShow1").show();
	$("#hideShow2").hide();
	var row = $('#dg').datagrid('getSelected');
	if (row){
		if(row.status=='1'){
			$('#dlg').dialog('open').dialog('setTitle','PROSESS SET IN');
			$('#fm').form('load',{
				x1:row.ocs_id,
				x2:row.date,
				x3:row.time,
				x4:row.site_name,
				x5:row.proj_name,
				x6:row.dtc_name,
				x7:row.total,
				x8:row.dt_name,
				x9:'JALAN',
				x10:row.sow_name,
				x11:row.ocs_desc,
				date_pergi:'1999-01-01',
				time_pergi:'00:00',
				nm_driver:'X', 
				car_name:'X', 
				description:'X', 
				km_start:'0'
			});
			url = 'RequestCarpoolGetnew.php?act=prosess_pulang';
			xload=2;
			
		}else{
			$.messager.alert('Error',"Sorry, you don't have permission!",'error');
		}
	}
	
}
*/

function print (){
	var row = $('#dg').datagrid('getSelected');
	if (row){
		if(row.sts_nm=='PAID'){
			window.open("/carpool/claim_formcar.php?ocs_id="+row.ocs_id, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=150, left=400, width=800, height=500");
		}else{
			$.messager.alert('Error',"Ubah status paid sebelum mencetak!",'error');
		}
	}
	
}
    </script>
