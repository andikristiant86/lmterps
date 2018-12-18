<?php 
$min=strtotime("saturday");
$week=date("W", $min);
$periode=date("Y-m");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>OUTSTANDING ESAR</title>
	<meta http-equiv="refresh" content="900;URL=" />
    <meta name="description" content="Progress ESAR" />
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
    
    <script type="text/javascript">
        $(document).ready(function () {
			
			// Create jqxButton widgets.
			 $("#jqxButton").jqxLinkButton({ width: '90', height: '30'});
            //$("#jqxButton").jqxButton({ width: 90, height: 30 });
			 $("#jqxButton1").jqxLinkButton({ width: 110, height: 30 });
			  $("#jqxButton2").jqxLinkButton({ width: 110, height: 30 });
			   $("#jqxButton3").jqxLinkButton({ width: 110, height: 30 });
			    $("#jqxButton4").jqxLinkButton({ width: 120, height: 30 });
			$("#jqxButton5").jqxLinkButton({ width: 150, height: 30 });
			  // prepare chart data as an array
             var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Operator'},
                    { name: 'Progress1'},
                    { name: 'Progress2' },
                    { name: 'Progress3' }
                ],
                url: 'progress_esar_noa_w.php'
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            $.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff0000', '#ccff00', '#00ffff', '#aaaaaa'] });
          
			
            var settings = {
                title: "ESAR Outstanding NOA,NY QC Accepted, Not Found",
                description: "Week <?=$week;?> <?=$periode;?> (Operator)",
				enableAnimations: true,
                showLegend: true,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                    {
                        dataField: 'Operator',
                        showGridLines: true,
						displayText: 'Pending'
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
                                    { dataField: 'Progress1', displayText: 'Amount (IDR) Not OA',color:'#DC143C'},
                                    { dataField: 'Progress2', displayText: 'Amount (IDR) Not Yet QC Accepted',color:'#49BF3C'},
                                    { dataField: 'Progress3', displayText: 'Amount (IDR) Not Found',color:'#8B008B'}
                                ]
                        }
                    ]
            };
            
            
			
			
			  // prepare chart data as an array
             var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'PM'},
                    { name: 'Progress1'},
                    { name: 'Progress2' },
                    { name: 'Progress3' },
                    { name: 'Progress4' }
                ],
                url: 'progress_esar_noa_w_pm1.php'
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            $.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff0000', '#ccff00', '#00ffff', '#aaaaaa'] });
          
			
            var settings2 = {
                title: "ESAR Outstanding Not OA",
                description: "Week <?=$week;?> <?=$periode;?>",
				enableAnimations: true,
                showLegend: true,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                    {
                        dataField: 'PM',
                        showGridLines: true,
						displayText: 'Outstanding'
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
                                    { dataField: 'Progress1', displayText: 'TELKOMSEL',color:'#DC143C'},
                                    { dataField: 'Progress2', displayText: 'INDOSAT',color:'#DCDC2B'},
                                    { dataField: 'Progress3', displayText: 'XL',color:'#8B008B'},
									{ dataField: 'Progress4', displayText: 'H3I',color:'#6495ED'}
                                ]
                        }
                    ]
            };
            
			// setup the chart
            
            $('#jqxChart2').jqxChart(settings2);
			
        });
    </script>
</head>
<body class='default' style="background-color:powderblue;">
	<table width="100%">
		<tr> 
			
			
			<td width="100%"> 
				<div id='jqxChart2' style="width: 100%; height: 350px;">
				</div>
			</td>
		</tr>
		<tr> 
			
			<td width="100%"> 
				<div id='jqxChart4' style="width: 100%; height: 350px;">
				</div>
			</td>
		</tr>
	</table>
  <center> 
  <a href="index.html" id='jqxButton'>Home</a>
  <a href="progress_admin_w.php" id='jqxButton1'>Admin Weekly</a>
  <a href="progress_admin_m.php" id='jqxButton2'>Admin Monthly</a>
  <a href="progress_finance_w.php" id='jqxButton3'>Finance Weekly</a>
  <a href="progress_finance_m.php" id='jqxButton4'>Finance Monthly</a>
  <a href="progress_esar_NOA_w_v.php" id='jqxButton5'>ESAR Outstanding</a>
  </center>
</body>
</html>
