<?php
   //Your code to call database for image id (from _GET['id']), for instance:
   $image_id = $_GET['id'];
   $image_x = empty($_GET['x'])?50:$_GET['x'];
   $image_y = empty($_GET['y'])?30:$_GET['y'];
   
   include("mysql/mysql.class.php");
	$db = new MySQL();
  
    $sql="select file from m_parkir where nmfile='$image_id'";
    
	if ($db->Open()) {
		if(!$db->Query($sql))
			echo $db->Error();
    
		while ($row = $db->Row()) {
			foreach ($row as $key=>$val){
				$$key=$val;
			}
	
			header("content-type: image/jpeg");
			echo resize(base64_decode($file),$image_x,$image_y);
		}
   }
   
   function resize($blob_binary, $desired_width, $desired_height) { // simple function for resizing images to specified dimensions from the request variable in the url
    if($desired_width=='x'&&$desired_height=='x'){
	   $new = imagecreatefromstring($blob_binary);
	}
	else {
	$im = imagecreatefromstring($blob_binary);
    $new = imagecreatetruecolor($desired_width, $desired_height) or exit("bad url");
    $x = imagesx($im);
    $y = imagesy($im);
    imagecopyresampled($new, $im, 0, 0, 0, 0, $desired_width, $desired_height, $x, $y) or exit("bad url");
    imagedestroy($im);
    }
	$new=imagerotate($new,-90,0);
    imagejpeg($new, null, 85) or exit("bad url");
	return $new;
	}
?>