<?php
session_start();
if(!empty($act) and $_SESSION["link_name"]=="subcost"){
	include $_SERVER['DOCUMENT_ROOT']."/kepegawaian/absensi/pengajuan_biaya1.php";
}
?>