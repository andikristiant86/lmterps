<?php
	//require_once('oracleconnectmanager.php');
	require_once('log.php');
	$log = new Logging();
	
	include("mysql/mysql.class.php");
	$db = new MySQL();
	
	// set path and name of log file (optional)
	//$log->lfile('/tmp/mylog.txt');
	
// close log file

    $file=isset($_REQUEST['file'])?$_REQUEST['file']:null;
	$img=isset($_REQUEST['img'])?$_REQUEST['img']:null;
	$user=isset($_REQUEST['cb'])?"'".$_REQUEST['cb']."'":"null";
	$latlon=isset($_REQUEST['ll'])?"'".$_REQUEST['ll']."'":"null";
	//$log->lwrite("$file >> $img");
	$nmfile=explode(".",$file);
    $tgl=substr($nmfile[0],-15,14);
	$status=substr($nmfile[0],-1);
	$barcode=substr($nmfile[0],1,strlen($nmfile[0])-16);//create_by='021450',create_date=CURRENT_TIMESTAMP()
	
	$sql="insert into m_parkir (barcode,date,file,nmfile,status,create_by,create_date,latlon) 
		values ('$barcode',str_to_date('$tgl', '%Y%m%d%H%i%s'),'$img','$file','$status',$user,CURRENT_TIMESTAMP(),$latlon)";
	$img=substr($img,0,35);
	$sql_log="insert into m_parkir (barcode,date,file,nmfile,status,create_by,create_date,latlon) 
		values ('$barcode',str_to_date('$tgl', '%Y%m%d%H%i%s'),'$img','$file','$status',$user,CURRENT_TIMESTAMP(),$latlon)";
		
	//START HERE ******************
	if($db->Open()) {
		//$log->lwrite($file.">>".$img);
 	
		$output['res']=false;  
		$output['ket']="none";  
  
		$log->lwrite("$sql_log");	  
     	//$db->TransactionBegin();
		/*if (!$db->Query($sql)) {
    	//	$db->TransactionEnd();
    		
    //     $output['res']=false;
         $output['ket']="gagal upload...$strTbl";
         print(json_encode($output));
         $log->lwrite("gagal di ".$output[$i]);
         $log->lclose();
         die();
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