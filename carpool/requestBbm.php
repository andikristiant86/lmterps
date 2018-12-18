<?php 
	session_start();
	include_once($DOCUMENT_ROOT."/s/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>ERP SYSTEM - LMT Mobile</title>
    <link rel="stylesheet" type="text/css" href="/jqueryui/themes/bootstrap/easyui.css">
    <link rel="stylesheet" type="text/css" href="/jqueryui/themes/mobile.css">
    <link rel="stylesheet" type="text/css" href="/jqueryui/themes/color.css">
    <link rel="stylesheet" type="text/css" href="/jqueryui/themes/icon.css">
    <script type="text/javascript" src="/jqueryui/jquery.min.js"></script>
    <script type="text/javascript" src="/jqueryui/jquery.easyui.min.js"></script>
    <script type="text/javascript" src="/jqueryui/jquery.easyui.mobile.js"></script>
</head>
<body style="font-size:15px;">
<style scoped>
        label{
            display: block;
            margin: 15px 0 5px 0;
			font-size:17px;
        }
		.panel-body {
			font-size:16px;
		}
		.1-btn-text {
			font-size:16px;
		}
		.textbox .textbox-text{
			font-size:16px;
		}
    </style>
<div id="ptable" class="easyui-navpanel" style="background-color:rgba(220, 204, 100, 0.28);position:relative;padding:20px;">
	<header style="height:50px;">
        <div class="m-toolbar">
            <div class="m-right">
                <a href="javascript:void(0)" style="width:120px;" id="vfilter" class="easyui-linkbutton c7" data-options="size:'large',iconCls:'icon-edit',plain:true" onclick="<?php  $f->CheckHakAccess($fua_edit,'$.mobile.go(\'#pfilter\')'); ?>">filter</a>
                <a href="javascript:void(0)" class="easyui-menubutton" data-options="size:'large',iconCls:'icon-more',menu:'#mm',menuAlign:'right',hasDownArrow:false"></a>
            </div>
        </div>
    </header>
	<form id="ff" style="padding:10px;">
            <div>
                <label>CRP ID</label>
                <input  name="ocs_id" id="ocs_id" type="hidden">
				<input  name="id" id="id" type="hidden">
				<div style="background-color:rgba(220, 204, 87, 0.38);margin:2px;font-size:18px;color:red;" id="crp_id"></div>
            </div>
            <div>
                <label>Nopol</label>
				<div style="background-color:rgba(220, 204, 87, 0.38);margin:2px;font-size:18px;color:red;" id="no_polisi"></div>
            </div>
            <div>
                <label>KM</label>
                <input class="easyui-numberbox" required="true" id="km_start" name="km_start" prompt="KM Awal" style="width:100%;height:50px;padding:12px;">
            </div>
            <div>
                <label>BBM</label>
                <input class="easyui-numberbox" required="true" id="bbm" name="bbm" prompt="BBM" style="width:100%;height:50px;padding:12px;">
            </div>
            <div>
                <label>E-Toll</label>
                <input class="easyui-numberbox" required="true" id="etoll" name="etoll" style="width:100%;height:50px;padding:12px;" data-options="prompt:'E-Toll'">
            </div>
    </form>
	
	<div style="padding:10px;">
        <div class="m-toolbar">
            <div class="m-right">
                <a href="javascript:void(0)" style="width:120px;" id="vsave" class="easyui-linkbutton c4" data-options="size:'large',iconCls:'icon-save',plain:false,disabled:true" onclick="<?php  $f->CheckHakAccess($fua_edit,'accept'); ?>">Simpan</a>
                <a href="javascript:void(0)" style="width:120px;" id="vabort" class="easyui-linkbutton c3" data-options="size:'large',iconCls:'icon-undo',plain:false,disabled:true" onclick="reject()">Batal</a>
            </div>
        </div>
    </div>
</div>
<div id="pfilter" class="easyui-navpanel" style="background-color:rgba(220, 204, 100, 0.28);position:relative;padding:20px;">
    <header style="height:40px;">
            <div class="m-toolbar">
                <div class="m-title">Filter</div>
				<div class="m-right">
					<a href="javascript:void(0)" class="easyui-menubutton" data-options="size:'large',iconCls:'icon-more',menu:'#mm',menuAlign:'right',hasDownArrow:false"></a>
				</div>
            </div>
    </header>
	<ul class="m-list">
		<!--<li><span>date [<font color=red>dd/mm/yyyy</font>]</span><br>
		<input id="start_date" class="easyui-datebox"> </li>
		<li><span>up to [<font color=red>dd/mm/yyyy</font>]</span><br><input id="end_date" class="easyui-datebox">
		</li>
		<li><span>Filter by</span><br>
            <select class="easyui-combobox" id="filter" style="width:100%">
				<option value="all">All</option> 
                <option value="ocs_id">OCS ID</option>
                <option value="dt_name">DT Name</option>
                <option value="nm_driver">Driver</option>
                <option value="no_polisi">Nopol</option>
			</select>
		</li>-->
		<li><label>OCS Filter</label><input id="tb" class="easyui-textbox"  prompt="Search..." style="width:100%;height:50px;padding:12px;size:15px;"></li>
		
	</ul>
	<div style="padding:10px;">
				<div class="m-toolbar">
					<div class="m-right">
						<a href="javascript:void(0)" style="width:120px;" class="easyui-linkbutton c3" data-options="size:'large',iconCls:'icon-cancel',plain:false" onclick="$.mobile.go('#ptable')">Batal</a>
						<a href="javascript:void(0)" style="width:120px;" class="easyui-linkbutton c4" data-options="size:'large',iconCls:'icon-ok',plain:false" onclick="doSearch()">Tampil</a>
					</div>
				</div>
		</div>
</div>
<div id="mm" class="easyui-menu" style="width:150px;">
        <div data-options="iconCls:'icon-lock'" onclick="window.location.href ='/?act=logout'">Logout</div>
    </div>
    <script type="text/javascript">       
        $(function(){
			$.mobile.go('#pfilter');   
			//$.messager.progress();	// display the progress bar
			$('#ff').form('submit', {
				url:'/carpool/requestBbmGet.php?act=update1',
				onBeforeLoad:function(){$.messager.progress(); return true;},
				onSubmit: function(){
					var isValid = $(this).form('validate');
					if (!isValid){
						$.messager.progress('close');	// hide progress bar while the form is invalid
					}
					return isValid;	// return false will stop the form submission
				},
				success: function(){
					$.messager.progress('close');	// hide progress bar while submit successfully
					//enableButtonSR(false);
				},
				onLoadSuccess:function(data){
					var data = JSON.stringify(data);  // change the JSON string to javascript object
					data=JSON.parse(data);
					$('#crp_id').html(data.crp_id);
					$('#no_polisi').html(data.no_polisi);
					$.messager.progress('close');
					enableButtonSR(false);
				},
				onLoadError:$.messager.progress('close')
			});
			
			$('#km_start').textbox({onChange:function(){
					enableButtonSR(true);
				}
			});
			$('#bbm').textbox({onChange:function(){
					enableButtonSR(true);
				}
			});
			$('#etoll').textbox({onChange:function(){
					enableButtonSR(true);
				}
			});
        });
		
		function accept(){
			//$('#ff').submit();
			if($('#ff').form('enableValidation').form('validate')){
				$.messager.confirm('Confirm','Ingin menyimpan data yang sudah diedit? <br><b>pastikan data yang diinput tdk salah</b>',function(r){
				if (r){
					//if (endEditing()){
						$.messager.progress();
						$.post(
							'/carpool/requestBbmGet.php?act=update1', 
							{
								updates: {
									ocs_id:$('#ocs_id').val(),
									id:$('#id').val(),
									km_start:$('#km_start').val(),
									bbm:$('#bbm').val(),
									etoll:$('#etoll').val()
								}
							}, 
							function(data) {
								var msg='';
								if (data.hasOwnProperty('msg')) {
									msg=JSON.stringify(data.msg);
									$.messager.alert({
										title:'Saving Response...',
										msg:msg,
										//width:500,
										//height:350,
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
									enableButtonSR(false);
								}
								$.messager.progress('close');
								
							}, 
							'json'
						).fail(function() {
							alert("Failed post request!");
						}).always(function() {
							reject();
						});
					//}
					}
				}
			)};
		}
		
		function reject(){
			$('#ff').form('load',url);
		}
		
		function enableButtonSR(active){
			var disable;
			if(active){
				disable='disable';enable='enable';
			}
			else{
				disable='enable';enable='disable';
			}
			$('#vfilter').linkbutton(disable);	
			$('#vsave').linkbutton(enable);
			$('#vabort').linkbutton(enable);
			//alert(enable);
		}
		var url;
        function doSearch(){			
			var val=$('#tb').textbox('getValue');
			url='/carpool/requestBbmGet.php?act=view1&value='+val+'&name=ocs_id';
			$('#ff').form('clear');$('#crp_id').html('');$('#no_polisi').html('');
			$('#ff').form('load',url);		
			$.mobile.go('#ptable');
		}		
		
    </script>
</body>
</html>