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
	//$log->lwrite("$file >> test");
	$nmfile=explode(".",$file);
    $tgl=substr($nmfile[0],-15,14);
	$status=substr($nmfile[0],-1);
	$barcode=substr($nmfile[0],1,strlen($nmfile[0])-16);//create_by='021450',create_date=CURRENT_TIMESTAMP()
	$sql=array();
	$arrbarcode=explode("_",$barcode);
	for($i=0,$key="",$val="";$i<count($arrbarcode);$i++){
		$key="$key,barcode_".($i+1);
		$val="$val,'".$arrbarcode[$i]."'";
	}
		
	//$log->lwrite("hai");
	
	//START HERE ******************
	if($db->Open()) {
		//$log->lwrite($file.">>".$img);
 	
		$output['res']=false;  
		$output['ket']="none";  
		
		if(!$db->Query("select max(id) as parkir_id from m_parkir"))
			echo $db->Error();
			
			
	//$log->lwrite("hai1");
	
		  $data = array();
		//  $res=$db->Row();
		  
	//$log->lwrite("hai2:".json_encode($res);//."->".$res['parkir_id']);
		  
		  while ($res=$db->Row()) {
			foreach($res as $key1=>$val1){
				$$key1=$val1;
				//$log->lwrite("hai2:".$val);
			}
		      
		    $_id=$parkir_id+1;
			//$log->lwrite("hai2:".json_encode($res)."===".$parkir_id);
	$sql[]="insert into m_parkir (id,barcode,date,status,create_by,create_date,latlon$key) 
		values ($_id,'$barcode',str_to_date('$tgl', '%Y%m%d%H%i%s'),'$status',$user,CURRENT_TIMESTAMP(),$latlon".$val.")";
	$sql[]="insert into m_parkir_file (file,nmfile,parkir_id) 
		values ('$img','$file',$_id)";	
			  
		 }
  
		
	$img=substr($img,0,35);
	$sql_log="
		insert into m_parkir_file (file,nmfile,parkir_id) 
		values ('$img','$file',$_id);
		
		insert into m_parkir (barcode,date,file,nmfile,status,create_by,create_date,latlon) 
		values ('$barcode',str_to_date('$tgl', '%Y%m%d%H%i%s'),'$img','$file','$status',$user,CURRENT_TIMESTAMP(),$latlon)";
		
		
		$log->lwrite("$sql_log
		$sql[0]");	  
     	//$db->TransactionBegin();
			  
		
		
		if (!($db->Query($sql[0])&&$db->Query($sql[1]))) {
  	
         $output['ket']="gagal upload...";
         $log->lwrite("gagal di ".$db->Error());
         $log->lclose();
		 $output['error']=$db->Error();
		 die(json_encode($output));
       }
	 //  else
	  // {
			$log->lwrite("sukses");
			$output['res']=true;
			$output['ket']="sukses...upload";    	
	//	} 
  
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