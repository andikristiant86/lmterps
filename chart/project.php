<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>DASHBOARD RESOURCE</title>
    <meta name="description" content="reosurce" />
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
	
			  // prepare chart data as an array
             var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Periode'},
                    { name: 'Jabodetabek'},
					{ name: 'East_java'},
					{ name: 'West_java'},
					{ name: 'Central_java'},
					{ name: 'Balom'},
					{ name: 'NCS'}
                ],
                url: 'projectGet.php?act=<?=$act;?>'
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            $.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff0000', '#ccff00', '#00ffff', '#aaaaaa'] });
          
			
            var settings4 = {
                title: "PROJECT LMT Monthly",
                description: "Periode Januari 2017 s/d Januari 2018",
				enableAnimations: true,
                showLegend: true,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                    {
                        dataField: 'Periode',
                        showGridLines: true,
						displayText: 'Periode'
                    },
                colorScheme: 'scheme01',
                seriesGroups:
                    [
                        {
                            type: 'column',
                            //columnsGapPercent: 10,
                            //seriesGapPercent: 0,
							//toolTipFormatSettings: { thousandsSeparator: ',' },
                            valueAxis:
                            {
                                //unitInterval: 10,
                                //minValue: 0,
                               // maxValue: 100,
                                displayValueAxis: true,
                                description: 'Amount Project',
                                axisSize: 'auto',
                                tickMarksColor: '#888888'
                            },
                            series: [
                                    { dataField: 'Jabodetabek', displayText: 'Amount (Project) Area Jabodetabek',color:'#DCDC2B'},
                                    { dataField: 'East_java', displayText: 'Amount (Project) East Java',color:'#49BF3C'},
                                    { dataField: 'West_java', displayText: 'Amount (Project) West Java',color:'#8B008B'},
                                    { dataField: 'Central_java', displayText: 'Amount (Project) Central Java',color:'#6495ED'},
									{ dataField: 'Balom', displayText: 'Amount (Project) Balom',color:'#DC143C'},
									{ dataField: 'NCS', displayText: 'Amount (Project) Nort Central Sumatra',color:'#423F3F'}
                                ]
                        }
                    ]
            };
            
			// setup the chart
            $('#jqxChart').jqxChart(settings4);
            
			$("#jqxButton").jqxLinkButton({ width: '10%', height: '30'});
			$("#jqxButton1").jqxLinkButton({ width: '10%', height: '30'});
			$("#jqxButton2").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton3").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton4").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton5").jqxLinkButton({ width: '7%', height: '30'});
			$("#jqxButton6").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton7").jqxLinkButton({ width: '10%', height: '30'});
			$("#jqxButton8").jqxLinkButton({ width: '10%', height: '30'});
        });
    </script>
</head>

<body class='default' style="background-color:powderblue;"><!--
<a style='margin-left: 1px;' href="/chart/resource.php?act=All" id='jqxButton'>All Location</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=JABODETABEK" id='jqxButton1'>Jabodetabek</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=WEST JAVA" id='jqxButton2'>WJ</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=EAST JAVA" id='jqxButton3'>EJ</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=CENTRAL JAVA" id='jqxButton4'>CJ</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=BALOM" id='jqxButton5'>BALOM</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=NORTH CENTRAL SUMATERA" id='jqxButton6'>NCS</a>	
<a style='margin-left: 1px;' href="/chart/resource.php?act=HQ OFFICE" id='jqxButton7'>HQ Office</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=FILE" id='jqxButton8'>Unknow</a>-->
					<div id='jqxChart' style="width: 100%; height: 450px;">
					</div>
			
</body>
</html>
