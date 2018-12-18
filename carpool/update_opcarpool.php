<?php
ob_start();
session_start();
include_once($DOCUMENT_ROOT."/s/config.php");
//global $isMobileSession;
if($_COOKIE["browser_mode"]){
	header("Location:/carpool/requestBbm.php?ref_url=$ref_url");
	exit;
}

$template->basicheader(2);
//echo "date: ".$f->convert_date("02/09/2014",1,"/");
$lke_id=$db->getOne("select (select lokasi_kerja from spg_lokasi_kerja where lke_id=spg_data_current.lke_id) from spg_data_current where nip='$login_nip'");
?>
<table id="dg" style="width:100%;height:520"
            title="PAYMENT BBM/ETOLL</b>"
			data-options="
					rownumbers:true,
					singleSelect:true,
					collapsible: true,
					url:'RequestCarpoolGet.php?act=view_updateop',
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
				<th field="ocs_id" width="100">CRP_ID</th>
				<th field="date" width="100">REQ. DATE</th>
				<th field="no_polisi" width="100">NO_POL</th>
				<th field="nm_driver" width="100">DRIVER</th>
				
                
                <!--<th field="site_name" width="100">SITE NAME</th>-->
                <!--<th field="proj_name" width="170">PROJECT</th>-->
				<!--<th field="dtc_name" width="130">CREATED BY</th>-->
                <th field="dt_name" width="130">DT/MANAGEMENT</th>
				<!--<th field="rno_name" width="150">RNO NAME</th>-->
				<!--<th field="ocs_desc" width="140">DESCRIPTION</th>-->
                <!--<th field="sts_nm" width="100" align="center" formatter="Status">STATUS</th>-->
				<!--<th field="operational" width="75" align="right">OP</th>-->
				<th field="bbmRp" width="75" align="right">BBM</th>
				<th field="etollRp" width="75" align="right">ETOLL</th>
				<th field="sts_nm" width="75" align="center">STATUS</th>
				<th field="update_by" width="130" align="left">UPDATE BY</th>
            </tr>
        </thead>
    </table>
	<div id="toolbar">
		<table width="100%" cellpadding=0 cellspacing=0>
			<tr><td>
				From date <?=$f->from_date("start_date","","","",false,1);?> Up to date <?=$f->up_to_date("end_date","","","",false,1);?>
				<input class="easyui-searchbox" data-options="prompt:'Search...',menu:'#mm',searcher:doSearch" style="width:300px">
				<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-edit" plain="true" onclick="<?php $f->CheckHakAccess($fua_add,"prosess_pulang"); ?>">Update</a>
			</td>
			<td align="right">
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

    <script type="text/javascript">
	 $(function(){ $('#dg').datagrid();});
	
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
	

    </script>

<div id="dlg1" class="easyui-dialog" style="width:45%;height:275px;padding:5px 10px" closed="true" buttons="#dlg-buttons1">
	<form id="ff" method="post" enctype="multipart/form-data">	
		<div class="fitem">
		<label>Transfer/ Paid Date</label>
		<?=$f->InputDate("tgl_dibayar");?>
		</div>
		<div class="fitem">
            <label>Driver</label>
            <input id="nip_driver1" name="nip_driver1" class="easyui-combobox" style="width:50%;" required="true" data-options="
			valueField: 'nip',
			textField: 'nm_peg',
			url: '/carpool/RequestCarpoolGet.php?act=combo_driver1'">
        </div>
        <div class="fitem">
            <label>No Polisi</label>
            <input id="kode_kendaraan1" name="kode_kendaraan1" class="easyui-combobox" style="width:50%;" required="true" data-options="
			valueField: 'kode_kendaraan',
			textField: 'no_polisi',
			url: '/carpool/RequestCarpoolGet.php?act=combo_car1'">
        </div>
		<div class="fitem">
            <label>KM Start</label>
            <input name="km_start1" class="easyui-textbox" required="true">
        </div>
	<div id="hideShow">
		<div class="fitem">
            <label>BBM</label>
            <input name="bbmRp" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;" required="true">
        </div>
		<div class ="fitem">
		<label>ETOLL</label> 
			<input name="etollRp" class="easyui-numberbox" data-options="precision:0,groupSeparator:','" style="width:20%;" required="true">
		</div>	
	</div>
	</form>
</div>
<div id="dlg-buttons1">
<a href="javascript:void(0)" class="easyui-linkbutton" id="btnSave" iconCls="icon-ok" onclick="Save1()" style="width:90px">Save</a>
<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlg1').dialog('close')" style="width:90px">Cancel</a>
</div>

<script>
function prosess_jalan(){
	$("#hideShow").hide();
	var row = $('#dg').datagrid('getSelected');
	if (row){
		if(row.status==4){
			$('#ff').form('clear');
			$('#ff').form('load',{
				km_end1:'0',
				date_pulang:'1999-01-01',
				time_pulang:'00:00:00'
			});
			$('#dlg1').dialog('open').dialog('setTitle','Carpool Information');
			url = 'RequestCarpoolGet.php?act=prosess_jalan&ocs_id='+row.id;	
		}else{
			$.messager.alert('Error',"Sorry, you don't have permission!",'error');
		}
	}
	
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
function tambah_durasi(){
	var row = $('#dg').datagrid('getSelected');
	if (row){
	$.messager.prompt('Confirm','Ingin menambah durasi? <br><b>silahkan input dengan angka:</b>',function(r){
			if (r){
				$.post('RequestCarpoolGet.php?act=do_tambahdurasi&durasi='+r,{ocs_id:row.id},function(result){
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
