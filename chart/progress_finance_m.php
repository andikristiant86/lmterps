<?php 
include "koneksi.php";
	$minx=strtotime(date("m"));
	$min=strtotime("5 month", $minx);
	$max=strtotime("3 month", $min);
	$arr=array();
	$columns2.="<th align='left'>Progress</th>";
	$i=1;
	$y=15;
	while ($min < $max) {
  $month=date("m", $min);
  $monthx=date("M", $min);
		$year=date("Y", $min)+48;
  
  $sql_1="select top 1 amount_idr_1 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_1=$dberps->getOne($sql_1);
  
  $sql_2="select top 1 amount_idr_2 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_2=$dberps->getOne($sql_2);
  
  $sql_3="select top 1 amount_idr_3 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_3=$dberps->getOne($sql_3);
  
  $sql_4="select top 1 amount_idr_4 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_4=$dberps->getOne($sql_4);
  
  $sql_5="select top 1 amount_idr_5 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_5=$dberps->getOne($sql_5);
  
  $sql_6="select top 1 amount_idr_6 from lmt_project.dbo.dhs_progress_esar where type='FINANCE_MONTHLY' and isnull(uncheck,'N') in ('N') and 
			periode='M-$month'";
  $amount_idr_6=$dberps->getOne($sql_6);
  
		$amount_idr_1=number_format("$amount_idr_1",0,",",".");
		$amount_idr_2=number_format("$amount_idr_2",0,",",".");
		$amount_idr_3=number_format("$amount_idr_3",0,",",".");
		$amount_idr_4=number_format("$amount_idr_4",0,",",".");
		$amount_idr_5=number_format("$amount_idr_5",0,",",".");
		$amount_idr_6=number_format("$amount_idr_6",0,",",".");
		$columns.="{ text: '$monthx $year', dataField: 'Amount_idr_$i',width: '15%', align: 'right', cellsAlign: 'right'},";
		$columns2.="<th align='left'>Amount_idr_$i</th>";
		$progress_admin1.="<td>IDR $amount_idr_1</td>";
		$progress_admin2.="<td>IDR $amount_idr_2</td>";
		$progress_admin3.="<td>IDR $amount_idr_3</td>";
		$progress_admin4.="<td>IDR $amount_idr_4</td>";
		$progress_admin5.="<td>IDR $amount_idr_5</td>";
		$progress_admin6.="<td>IDR $amount_idr_6</td>";
		$i++;
		$y_t=$y_t+$y;
		$min = strtotime("+1 month", $min);
	}
	$p_c=100-$y_t;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>PROGRESS ESAR</title>
    <meta name="description" content="Progress ESAR" />
	<meta http-equiv="refresh" content="600;URL=" />
    <link rel="stylesheet" href="./jqwidgets/styles/jqx.base.css" type="text/css" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1 minimum-scale=1" />	
    <script type="text/javascript" src="./scripts/jquery-1.12.4.min.js"></script>
     <script type="text/javascript" src="./jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxdata.js"></script> 
    <script type="text/javascript" src="./jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxmenu.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxdatatable.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxdraw.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxchart.core.js"></script>
   
    <script type="text/javascript" src="./jqwidgets/jqxgrid.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxgrid.selection.js"></script> 
    <script type="text/javascript" src="./jqwidgets/jqxgrid.columnsresize.js"></script> 
    <script type="text/javascript" src="./scripts/demos.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxcheckbox.js"></script>
    <script type="text/javascript" src="./sampledata/generatedata.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
			
			$("#table").jqxDataTable(
            {
                altRows: true,
                sortable: true,
                editable: true,
                selectionMode: 'singleRow',
				width: '100%',
                
                columns: [
					{ text: 'Progress', dataField: 'Progress', width: '<?=$p_c;?>%', align: 'left', cellsAlign: 'left'},
                  <?=$columns;?>
                ]
            });
			
			// Create jqxButton widgets.
			 $("#jqxButton").jqxLinkButton({ width: '90', height: '30'});
            //$("#jqxButton").jqxButton({ width: 90, height: 30 });
			 $("#jqxButton1").jqxLinkButton({ width: 110, height: 30 });
			  $("#jqxButton2").jqxLinkButton({ width: 110, height: 30 });
			   $("#jqxButton3").jqxLinkButton({ width: 110, height: 30 });
			    $("#jqxButton4").jqxLinkButton({ width: 120, height: 30 });
			
			  // prepare chart data as an array
             var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Month'},
                    { name: 'Progress1'},
                    { name: 'Progress2' },
                    { name: 'Progress3' },
                    { name: 'Progress4' },
                    { name: 'Progress5' },
                    { name: 'Progress6' }
                ],
                url: 'progres_esar_finance_m.php'
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            $.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff0000', '#ccff00', '#00ffff', '#aaaaaa'] });
          
			
            var settings4 = {
                title: "ESAR Progress Finance LMT Monthly",
                description: "Confidential",
				enableAnimations: true,
                showLegend: true,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                    {
                        dataField: 'Month',
                        showGridLines: true,
						displayText: 'Progress'
                    },
                colorScheme: 'scheme01',
                seriesGroups:
                    [
                        {
                            type: 'column',
                            //columnsGapPercent: 10,
                            //seriesGapPercent: 0,
							toolTipFormatSettings: { thousandsSeparator: ',' },
                            valueAxis:
                            {
                                //unitInterval: 10,
                                //minValue: 0,
                               // maxValue: 100,
                                displayValueAxis: true,
                                description: 'IDR',
                                axisSize: 'auto',
                                tickMarksColor: '#888888',
								labels: {
									visible: true,
									formatFunction: function (value) {
										return (value / 1000000000)+'M';
									}
								}
                            },
                            series: [
                                    { dataField: 'Progress1', displayText: 'Amount (IDR) handover from admin LMT to finance',color:'#DCDC2B'},
                                    { dataField: 'Progress2', displayText: 'Amount (IDR) in process SCS',color:'#49BF3C'},
                                    { dataField: 'Progress3', displayText: 'Amount (IDR) waiting in process PPS',color:'#8B008B'},
                                    { dataField: 'Progress4', displayText: 'Amount (IDR) invoice sent to HW in this month',color:'#6495ED'},
									{ dataField: 'Progress5', displayText: 'Forecast day amount (IDR) value PO (Excl. Tax)',color:'#DC143C'},
									{ dataField: 'Progress6', displayText: 'Forecast day after payment HW on LMT bankaccount (Incl. Tax)',color:'#423F3F'}
                                ]
                        }
                    ]
            };
            
			// setup the chart
            $('#jqxChart').jqxChart(settings4);
            
			
        });
    </script>
</head>
<body class='default' style="background-color:powderblue;">
	<table width="100%">
		<tr> 
			
			<td width="100%"> 
					<div id='jqxChart' style="width: 100%; height: 400px;">
					</div>
			</td>
			
		</tr>
		<tr> 
			<td width="100%" valign="top"> 
					<table id="table" border="1" width="100%">
        <thead>
            <tr>
                <?=$columns2;?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Amount (IDR) handover from admin LMT to finance</td>
				<?=$progress_admin1;?>
				
				
            </tr>
			<tr>
                <td>Amount (IDR) in process SCS</td>
				<?=$progress_admin2;?>
				
            </tr>
            <tr>
                <td>Amount (IDR) waiting in process PPS</td>
				<?=$progress_admin3;?>
				
            </tr>
			<tr>
                <td>Amount (IDR) invoice sent to HW in This Week</td>
				<?=$progress_admin4;?>
				
            </tr>
			<tr>
                <td>Forecast day amount (IDR) value PO (Excl. Tax)</td>
				<?=$progress_admin5;?>
				
            </tr>
			<tr>
                <td>Forecast day after payment HW on LMT bankaccount (Incl. Tax)</td>
				<?=$progress_admin6;?>
				
            </tr>
        </tbody>
		
    </table>
					
			</td>
			
		</tr>
	</table>
  <center> 
  <a href="index.html" id='jqxButton'>Home</a>
  <a href="progress_admin_w.php" id='jqxButton1'>Admin Weekly</a>
  <a href="progress_admin_m.php" id='jqxButton2'>Admin Monthly</a> 
  <a href="progress_finance_w.php" id='jqxButton3'>Finance Weekly</a>
  <a href="progress_finance_m.php" id='jqxButton4'>Finance Monthly</a>
  </center>
</body>
</html>
