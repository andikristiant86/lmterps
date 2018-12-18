<?php
$act=empty($act)?"All":"$act";
if($act=='All')		$desc='All Employee';
elseif($act=='Region')	$desc='All Region';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>JavaScript Chart Column Series Example</title>
    <meta name="description" content="This is an example of JavaScript Chart Column Series." />
    <link rel="stylesheet" href="./jqwidgets/styles/jqx.base.css" type="text/css" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1 minimum-scale=1" />	
    <script type="text/javascript" src="./scripts/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxdraw.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxchart.core.js"></script>
    <script type="text/javascript" src="./scripts/demos.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxdata.js"></script>
	<script type="text/javascript" src="./jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxlistbox.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxcombobox.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // prepare chart data as an array            
            var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Periode' },
                    { name: 'PO' },
                    { name: 'Esar' },
                    { name: 'Costproject' }
                ],
                url: 'esarGet.php'
            };
            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            // prepare jqxChart settings
            var settings = {
                title: "PROGRESS ESAR<br>",
                description: "Periode Jan s/d Des 2017",
                showLegend: true,
                enableAnimations: true,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 90, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                    {
                        dataField: 'Periode',
                        gridLines: { visible: true },
                        valuesOnTicks: false
                    },
                colorScheme: 'scheme01',
                columnSeriesOverlap: false,
                seriesGroups:
                    [
                        {
                            type: 'column',
							toolTipFormatSettings: { thousandsSeparator: ',' },
                            valueAxis:
                            {
                                visible: true,
                                //unitInterval: 200,
                                title: { text: 'Cost per Month (Rp)<br>' },
								formatFunction: function (value) {
										return (value / 1000000000)+'M';
									}
                            },
                            series: [
                                    { dataField: 'PO', displayText: 'Amount (Rp) PO' },
									{ dataField: 'Esar', displayText: 'Amount (Rp) Esar' },
									{ dataField: 'Costproject', displayText: 'Amount (Rp) Cost Project' }
                                ]
                        }
                    ]
            };
            // setup the chart
            $('#chartContainer').jqxChart(settings);
			
			//$("#Country").jqxComboBox({ width: '15%', height: 25 });
			
			//$("#jqxButton").jqxLinkButton({ width: '10%', height: '30'});$("#jqxButton1").jqxLinkButton({ width: '10%', height: '30'});
        });
    </script>
</head>
<body class='default'><!--
<a style='margin-left: 1px;' href="/chart/employee_vs_salary.php?act=All" id='jqxButton'>All Employee</a>
<a style='margin-left: 1px;' href="/chart/employee_vs_salary.php?act=Region" id='jqxButton1'>All Region</a>
<!--
<select id="Country" onchange="hourChange(this);">
<option>PERIODE 2017</option>
<option>PERIODE 2018</option>
</select>-->
	<div id='chartContainer' style="width:100%; height:450px;">
</body>
</html>
