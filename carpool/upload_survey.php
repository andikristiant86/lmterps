<?php
	$db_host    = "10.10.10.202:3307"; // server name
	$db_user    = "dms";          // user name
	$db_pass    = "dms";          // password
	$db_dbname  = "dotproject";          // database name
	//die('test');	
	require_once('log.php');
	$log = new Logging();
	//$log->lwrite("===================== start survey =====".json_encode($_REQUEST));
	include("mysql/mysql.class.php");
	$db = new MySQL(true,$db_dbname, $db_host,$db_user, $db_pass);
	//$log->lwrite("===1====");
	
    $foto_file=isset($_REQUEST['foto_file'])?$_REQUEST['foto_file']:null;
	$berkas1_file=isset($_REQUEST['berkas1_file'])?$_REQUEST['berkas1_file']:null;
	//$berkas2_file=isset($_REQUEST['berkas2_file'])?$_REQUEST['berkas2_file']:null;
	$foto=isset($_REQUEST['foto'])?$_REQUEST['foto']:null;
	$berkas1=isset($_REQUEST['berkas1'])?$_REQUEST['berkas1']:null;
	//$berkas2=isset($_REQUEST['berkas2'])?$_REQUEST['berkas2']:null;
	$no_surat=isset($_REQUEST['no_surat'])?$_REQUEST['no_surat']:null;
	$latlon=isset($_REQUEST['latlon'])?$_REQUEST['latlon']:null;
	$tgl_terima=isset($_REQUEST['tgl_terima'])?$_REQUEST['tgl_terima']:null;
	$update_by=isset($_REQUEST['update_by'])?$_REQUEST['update_by']:null;
	$upload_by=isset($_REQUEST['upload_by'])?$_REQUEST['upload_by']:null;
	$log->lwrite("===2====");
	//$log->lwrite("$file >> $img");
	$latlon=explode("/",$latlon);
    //$tgl=substr($nmfile[0],-15,14);
	//$status=substr($nmfile[0],-1);
	//$barcode=substr($nmfile[0],1,strlen($nmfile[0])-16);//create_by='021450',create_date=CURRENT_TIMESTAMP()
	//$log->lwrite("===3====");
	/*berkas2,,berkas2_file'$berkas2_file',,berkas2
	'$berkas2_file',,'$berkas2',berkas2_file,'$_berkas2'*/
	$sql="insert into tbl_survey (no_surat,lat,lon,foto_file,
	berkas1_file,foto,berkas1,tgl_terima,update_by,upload_by) 
		values ('$no_surat',".$latlon[0].",".$latlon[1].",'$foto_file','$berkas1_file',
		'$foto','$berkas1',str_to_date('$tgl_terima', '%Y%m%d%H%i%s'),
		'$update_by','$upload_by')";
	$_foto=substr($foto,0,35);
	$_berkas1=substr($berkas1,0,35);
	//$_berkas2=substr($berkas2,0,35);
	$sql_log="insert into tbl_survey (no_surat,lat,lon,foto_file,
	berkas1_file,foto,berkas1,tgl_terima,update_by,upload_by) 
		values ('$no_surat',".$latlon[0].",".$latlon[1].",'$foto_file','$berkas1_file',
		'$_foto','$_berkas1',str_to_date('$tgl_terima', '%Y%m%d%H%i%s'),
		'$update_by','$upload_by')";
		
	//START HERE ******************
	if($db->Open()) {
		//$log->lwrite($file.">>".$img);
 	
		$output['res']=false;  
		$output['ket']="none";  
  
		$log->lwrite("$sql_log");	  
     	//$db->TransactionBegin();
		if (!$db->Query($sql)) {
    	//	$db->TransactionEnd();
    		
    //     $output['res']=false;
	
         $output['ket']="gagal upload...";
       //  print(json_encode($output));
         $log->lwrite("gagal di ".$db->Error());
         $log->lclose();
		 $output['error']=$db->Error();
		 die(json_encode($output));
       }
	 /*  else{
			$file = fopen("survey/".$foto_file, 'wb');
			fwrite($file, base64_decode($foto));
			fclose($file);
			$file = fopen("survey/".$berkas1_file, 'wb');
			fwrite($file, base64_decode($berkas1));
			fclose($file);
			$file = fopen("survey/".$berkas2_file, 'wb');
			fwrite($file, base64_decode($berkas2));
			fclose($file);
	   }*/
    //}
    $output['res']=true;
	$output['ket']="sukses...upload";    	
//  } 
  
	print(json_encode($output));
	$log->lclose();	
  } else {  	
  	//$output['error']=$e->getMessage().$db->Error();
  	$output['ket']="error db connection server";
  	$output['error']=$db->Error();
  	print(json_encode($output));
  	$log->lclose();
  	$db->Kill();
  	
  }
  
?>