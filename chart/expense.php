
<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>PROGRESS ESAR</title>
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
    <script type="text/javascript" src="./sampledata/generatedata.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
			
			  // prepare chart data as an array
             var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Typex'},
                    { name: 'Progress1'},
                    { name: 'Progress2' },
                    { name: 'Progress3' },
                    { name: 'Progress4' }
                ],
                url: 'expenseGet.php'
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            $.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff0000', '#ccff00', '#00ffff', '#aaaaaa'] });
          
			
            var settings3 = {
                title: "Expense Project",
                description: "Confidential",
				enableAnimations: true,
                showLegend: true,
                padding: { left: 5, top: 5, right: 5, bottom: 5 },
                titlePadding: { left: 0, top: 0, right: 0, bottom: 10 },
                source: dataAdapter,
                xAxis:
                    {
                        dataField: 'Typex',
                        showGridLines: true,
						displayText: 'Expense'
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
									{ dataField: 'Progress1', displayText: 'Project 213',color:'#DCDC2B'},
                                    { dataField: 'Progress2', displayText: 'Project 214',color:'#49BF3C'},
                                    { dataField: 'Progress3', displayText: 'Project 215',color:'#8B008B'},
                                    { dataField: 'Progress4', displayText: 'Project 238',color:'#6495ED'}
                                ]
                        }
                    ]
            };
            
            
			// setup the chart
            $('#jqxChart').jqxChart(settings3);
            
			
        });
    </script>
</head>

					<div id='jqxChart' style="width: 100%; height: 200px;">
					</div>
			