<?php
/*****
 * Example of OracleConnectManager class.
 *
 *
 * 
 * Written by Mohammad Ashrafuddin Ferdousi.
 * email: it.codeperl@gmail.com 
 * Licensed under GNU, GPL. 
 *
 *
 */
 //including the oracle_connect_manager.php to available the OracleConnectManager Class.
  //require_once('oracleconnectmanager.php');
  

	require_once('log.php');
	$log = new Logging();
	
	if(isset($_REQUEST['filter'])){
		$filter=split('_',$_REQUEST['filter']);
		$fil='b.barcode';
		$i=0;
	}
    else if(isset($_REQUEST['nip'])){
		$filter=array($_REQUEST['nip']);
		$fil='login';
		$i=2;
	}
	else 
		$filter=null;
    //$table=array('ref_propinsi','ref_dati2','ref_kecamatan','ref_kelurahan');
    $sqlWhere='';
   // $sqlSelect='*';
    $nfil=count($filter);
  // echo "$sqlWhere>>hohoho>>$nfil<br>";
     for($j=0;$j<$nfil;$j++)
    {
    	if($j>0){
    		$sqlWhere.=' or ';
    		//$sqlSelect.=',';
    	}
    	else {
    		$sqlWhere.=' ';
    	//	$sqlSelect='';//$fil[$i];
    	}
    	$sqlWhere.=$fil."='".$filter[$j]."'";
    //	$sqlSelect.=','.$fil[$i+1];    	
    }/**/
 /* */
   $idx=array(
  	array(	//sub_pajak
  		"ID"=>1, //primary key don't change/remove
	  		"DRIVER"=>2, 
	),array(),
	array( //user
		"USER"=>1,
	)/**/
   );
 
 $sqlWhere=empty($sqlWhere)?'':"where $sqlWhere";
 $sql=array(
"select b.barcode as id,b.driver from m_driver b $sqlWhere",""
 ,//"select concat(nip,':',PASSWORD,':',ID) as user from tbl_user b $sqlWhere"
 "select login+':'+PASSWORD+':'+cast(ID as VARCHAR) as 'user' from tbl_user b $sqlWhere"
 );
  $output['res']=false;  
  $output['ket']="none";  
  //echo $sql[$i];
  if($i==0){
	include("mysql/mysql.class.php");
	$db = new MySQL();
	if ($db->Open()) {
		//for($i=0;$i<count($sql);$i++){
		  if(!$db->Query($sql[$i]))
			echo $db->Error();
		  //oci_set_prefetch($stid, 60);
		 // oci_execute($stid);
		  //$data =null;//
		  $data = array();
		  if(isset($_GET['test']))
			 echo "<br>+++++# tabel ke $i #++++<br>";
		  //echo json_encode($data)."<br>==>";
		 // $db->MoveFirst();
		  while ($row = $db->Row()) {
			 // $row = $db->Row();
			// echo json_encode($row);
			  $out=array();
			  foreach ($row as $key=>$val){
					//$str="31-Dec-2007";
					//$$key=
				/*	if(strtolower($key)=="foto"){
							$filename="images/".$val;
							$log->lwrite($filename);
							$file = fopen($filename, 'r');
							$binary=fread($file, filesize($filename));
							fclose($file);
							$val=base64_encode($binary);
					//		$log->lwrite($val);	      				
					}*/
						
					$xdum=$idx[$i][strtoupper($key)];
					//echo "<br>==>$xdum <br>";
					/*if($xdum==23||$xdum==25||$xdum==27){ //jika tipe tanggal
						$basedate = strtotime($val);
						//$date1 = strtotime("-3 months", $basedate);
						$val=date("Y-m-d H:i:s", $basedate); 
					} */
					$out[$xdum]=trim($val);
					//if (strtoupper($key=='KD_POS_WP'))
					//	echo json_encode($row)."<br>".$key."==>";
			  
				}
				if(isset($_GET['test']))
					echo json_encode($row)."<br><hr>";
			  //ECHO "2";
			  $data[] = $out;
			 // echo "test";
		  }
		  $output[$i+1]=$data;
		 // echo json_encode($data)."<br><br>";
	   // }
		//oci_free_statement($stid);
		//oci_close($conn[0]);
		$output['res']=count($data)>0?true:false; 
		$output['ket']="Sukses...koneksi";
	  } 
	  else 
	  {
		$output['ket']="error db connection server";
		//if ($conn[1]!="")
		$output['error']=$db->Error();
		$db->Kill();  
	  }
  }
  else if($i==2){
	$dbhostname		= "(local)";
	$dbusername		= "sa_erp";
	$dbpassword		= "Djisamsoe5";
	$dbname			= "lmt_hcis";
	
	include "$DOCUMENT_ROOT/classes/adodb/adodb.inc.php";
	
	$db =& ADONewConnection('mssqlnative');
	$db->PConnect($dbhostname, $dbusername, $dbpassword, $dbname);
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	
	$user=$db->getOne($sql[$i]);
	$data = array();
    if(!empty($user)){
		if(isset($_GET['test']))
		   echo "<br>+++++# tabel ke $i #++++<br>";
		$out=array();
		$xdum=$idx[$i][strtoupper("user")];
		$out[$xdum]=trim($user);//.":Update aplikasi Anda dgn download HCISdroid di HCIS Anda";
		$data[] = $out;		
		$output['ket']="Sukses...koneksi";
	}
	$output[$i+1]=$data;
	$output['res']=count($data)>0?true:false; 
  }  
  
 // if(!isset($_GET['test']))
  	echo json_encode($output);
//$log->lwrite(json_encode($output));
  


?>