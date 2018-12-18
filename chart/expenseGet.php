<?php
include_once($DOCUMENT_ROOT."/s/database.php");
include "$DOCUMENT_ROOT/classes/adodb/adodb.inc.php";

$db =& ADONewConnection('mssqlnative');
$db->PConnect($dbhostname, $dbusername, $dbpassword, $dbname[0]);
$db->SetFetchMode(ADODB_FETCH_ASSOC);

$dbproj =& ADONewConnection('mssqlnative');
$dbproj->PConnect($dbhostname, $dbusername, $dbpassword, $dbname[1]);
$dbproj->SetFetchMode(ADODB_FETCH_ASSOC);

	$arr=array();
	
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_OTHERS] WHERE [PROJECT_ID] = '267'";
	$amount_subcost_213=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_OTHERS] WHERE [PROJECT_ID] = '268'";
	$amount_subcost_214=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_OTHERS] WHERE [PROJECT_ID] = '266'";
	$amount_subcost_215=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_OTHERS] WHERE [PROJECT_ID] = '297'";
	$amount_subcost_238=$dbproj->getOne($sql_1);
	
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_CARPOOL] WHERE [PROJECT_ID] = '267'";
	$amount_carpool_213=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_CARPOOL] WHERE [PROJECT_ID] = '268'";
	$amount_carpool_214=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_CARPOOL] WHERE [PROJECT_ID] = '266'";
	$amount_carpool_215=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_CARPOOL] WHERE [PROJECT_ID] = '297'";
	$amount_carpool_238=$dbproj->getOne($sql_1);
	
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_PULSA] WHERE [PROJECT_ID] = '267'";
	$amount_pulsa_213=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_PULSA] WHERE [PROJECT_ID] = '268'";
	$amount_pulsa_214=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_PULSA] WHERE [PROJECT_ID] = '266'";
	$amount_pulsa_215=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_PULSA] WHERE [PROJECT_ID] = '297'";
	$amount_pulsa_238=$dbproj->getOne($sql_1);
	
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_SALARY] WHERE [PROJECT_ID] = '267'";
	$amount_salary_213=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_SALARY] WHERE [PROJECT_ID] = '268'";
	$amount_salary_214=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_SALARY] WHERE [PROJECT_ID] = '266'";
	$amount_salary_215=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(AMOUNT) FROM data_warehouse.[dbo].[EXPENSE_SALARY] WHERE [PROJECT_ID] = '297'";
	$amount_salary_238=$dbproj->getOne($sql_1);
	
	$total_213=$amount_subcost_213+$amount_carpool_213+$amount_pulsa_213+$amount_salary_213;
	$total_214=$amount_subcost_214+$amount_carpool_214+$amount_pulsa_214+$amount_salary_214;
	$total_215=$amount_subcost_215+$amount_carpool_215+$amount_pulsa_215+$amount_salary_215;
	$total_238=$amount_subcost_238+$amount_carpool_238+$amount_pulsa_238+$amount_salary_238;
	
	$sql_1="SELECT SUM(budget_plan) FROM [dbo].[M_PROJECT] WHERE [ID] = '267'";
	$BO_213=$dbproj->getOne($sql_1);
	$sql_1="SELECT SUM(budget_plan) FROM [dbo].[M_PROJECT] WHERE [ID] = '268'";
	$BO_214=$dbproj->getOne($sql_1);
	
	$sql_1="SELECT SUM(budget_plan) FROM [dbo].[M_PROJECT] WHERE [ID] = '266'";
	$BO_215=$dbproj->getOne($sql_1);
	
	$sql_1="SELECT SUM(budget_plan) FROM [dbo].[M_PROJECT] WHERE [ID] = '297'";
	$BO_238=$dbproj->getOne($sql_1);
	
	
	$arr[]=array("Typex"=>"Subcost/Others","Progress1"=>"$amount_subcost_213","Progress2"=>"$amount_subcost_214",
	"Progress3"=>"$amount_subcost_215","Progress4"=>"$amount_subcost_238");
	$arr[]=array("Typex"=>"Carpool","Progress1"=>"$amount_carpool_213","Progress2"=>"$amount_carpool_214",
	"Progress3"=>"$amount_carpool_215","Progress4"=>"$amount_carpool_238");
	$arr[]=array("Typex"=>"Pulsa","Progress1"=>"$amount_pulsa_213","Progress2"=>"$amount_pulsa_214",
	"Progress3"=>"$amount_pulsa_215","Progress4"=>"$amount_pulsa_238");
	$arr[]=array("Typex"=>"Salary","Progress1"=>"$amount_salary_213","Progress2"=>"$amount_salary_214",
	"Progress3"=>"$amount_salary_215","Progress4"=>"$amount_salary_238");
	$arr[]=array("Typex"=>"Total Expense","Progress1"=>"$total_213","Progress2"=>"$total_214","Progress3"=>"$total_215",
		"Progress4"=>"$total_238");
	$arr[]=array("Typex"=>"Budget Plan","Progress1"=>"$BO_213","Progress2"=>"$BO_214","Progress3"=>"$BO_215",
		"Progress4"=>"$BO_238");
		
	echo json_encode($arr);
?>