<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>DASHBOARD CARPOOL</title>
    <meta name="description" content="reosurce" />
    <link rel="stylesheet" href="./jqwidgets/styles/jqx.base.css" type="text/css" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1 minimum-scale=1" />	
    <script type="text/javascript" src="./scripts/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="./scripts/demos.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxcore.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxscrollbar.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxcheckbox.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxmenu.js"></script>
	<script type="text/javascript" src="./jqwidgets/jqxchart.core.js"></script>
	<script type="text/javascript" src="./jqwidgets/jqxbuttons.js"></script>
    <script type="text/javascript" src="./jqwidgets/jqxlistbox.js"></script>
	 <script type="text/javascript" src="./jqwidgets/jqxdraw.js"></script>
	 <script type="text/javascript" src="./jqwidgets/jqxdata.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
	
			  // prepare chart data as an array
             var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Periode'},
                    { name: 'Driver' },
                    { name: 'Car' },
					{ name: 'Cost' }
                ],
                url: 'carpoolGet.php?act=<?=$act;?>&lke_id=<?=$lke_id;?>'
            };

            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            $.jqx._jqxChart.prototype.colorSchemes.push({ name: 'myScheme', colors: ['#ff0000', '#ccff00', '#00ffff', '#aaaaaa'] });
          
			
            var settings4 = {
                title: "CAR & DRIVER <?=$act;?>",
                description: "Periode 2017/2018",
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
                                description: 'Car & Driver',
                                axisSize: 'auto',
                                tickMarksColor: '#888888'
                            },
                            series: [
                                    
                                    { dataField: 'Driver', displayText: 'Amount DRIVER',color:'#49BF3C'},
                                    { dataField: 'Car', displayText: 'Amount CAR',color:'#8B008B'}
                                ]
                        },
                        {
                            type: 'line',
							toolTipFormatSettings: { thousandsSeparator: ',' },
                            valueAxis:
                            {
                                visible: true,
                                position: 'right',
                                
                                title: { text: 'Amount (Rp)' },
                                gridLines: { visible: false },
								labels: {
									visible: true,horizontalAlignment: 'left',
									formatFunction: function (value) {
										return (value / 1000000)+'K';
									}
								}
                            },
                            series: [
                                    { dataField: 'Cost', displayText: 'Amount Cost Carpool' }
                                ]
                        }
                    ]
            };
            
			// setup the chart
            $('#chartContainer').jqxChart(settings4);
            /*
			$("#jqxButton").jqxLinkButton({ width: '10%', height: '30'});
			$("#jqxButton1").jqxLinkButton({ width: '10%', height: '30'});
			$("#jqxButton2").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton3").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton4").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton5").jqxLinkButton({ width: '7%', height: '30'});
			$("#jqxButton6").jqxLinkButton({ width: '5%', height: '30'});
			$("#jqxButton7").jqxLinkButton({ width: '10%', height: '30'});
			$("#jqxButton8").jqxLinkButton({ width: '10%', height: '30'});*/
			
			// create jqxMenu
            $("#jqxMenu").jqxMenu({ height: '32px', autoSizeMainItems: true});
            $("#jqxMenu").jqxMenu('minimize');
            
            $("#jqxMenu").css('visibility', 'visible');
			
			$("#print").click(function () {
                var content = $('#chartContainer')[0].outerHTML;
                var newWindow = window.open('', '', 'width=800, height=500'),
                document = newWindow.document.open(),
                pageContent =
                    '<!DOCTYPE html>' +
                    '<html>' +
                    '<head>' +
                    '<meta charset="utf-8" />' +
                    '<title>Employee vs Salary</title>' +
                    '</head>' +
                    '<body>' + content + '</body></html>';
                try
                {
                    document.write(pageContent);
                    document.close();
                    newWindow.print();
                    newWindow.close();
                }
                catch (error) {
                }
            });
            $("#print").jqxButton({ template: "primary" });
			
			
			
			$("#jpegButton").jqxButton({ template: "primary" });
            $("#pngButton").jqxButton({ template: "primary" });
            //$("#pdfButton").jqxButton({ template: "primary" });
            $("#jpegButton").click(function () {
                // call the export server to create a JPEG image
                $('#chartContainer').jqxChart('saveAsJPEG', 'employee_vs_salary.jpeg', getExportServer());
            });
            $("#pngButton").click(function () {
                // call the export server to create a PNG image
                $('#chartContainer').jqxChart('saveAsPNG', 'employee_vs_salary.png', getExportServer());
            });
           // $("#pdfButton").click(function () {
                // call the export server to create a PNG image
                //$('#chartContainer').jqxChart('saveAsPDF', 'employee_vs_salary.pdf', getExportServer());
           // });
			
			
        });
    </script>
</head>

<body class='default' style="background-color:powderblue;">

<div id='jqxMenu' style='visibility: visible;'>
        <ul>
            <li><a href="/chart/carpool.php?act=All">All Location</a></li>
			<?PHP
				$sql="select b.lke_id,a.lokasi_kerja from [dbo].[m_carpool] b
						left join m_project_area a on a.lke_id=b.lke_id
						where a.lokasi_kerja is not null
						group by b.lke_id,a.lokasi_kerja";
				$resultx=$dbproj->Execute($sql);
				while($row=$resultx->Fetchrow()){
					foreach($row as $key=>$val){
						$key=strtolower($key);
						$$key=$val;				
					}
					
				
			?>
            <li><a href="/chart/carpool.php?act=<?=$lokasi_kerja;?>&lke_id=<?=$lke_id;?>"><?=ucwords(strtolower("$lokasi_kerja"));?></a></li>
			<?PHP
			}
			?>
            
        </ul>
    </div>
<!--
<a style='margin-left: 1px;' href="/chart/resource.php?act=All" id='jqxButton'>All Location</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=JABODETABEK" id='jqxButton1'>Jabodetabek</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=WEST JAVA" id='jqxButton2'>WJ</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=EAST JAVA" id='jqxButton3'>EJ</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=CENTRAL JAVA" id='jqxButton4'>CJ</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=BALOM" id='jqxButton5'>BALOM</a>
<a style='margin-left: 1px;' href="/chart/resource.php?act=NORTH CENTRAL SUMATERA" id='jqxButton6'>NCS</a>	
<a style='margin-left: 1px;' href="/chart/resource.php?act=HQ OFFICE" id='jqxButton7'>HQ Office</a>
<!--<a style='margin-left: 1px;' href="/chart/resource.php?act=FILE" id='jqxButton8'>Unknow</a>-->
					<div id='host' style="margin: 0 auto; width: 100%;">
		<div id='chartContainer' style="width: 100%; height: 450px;">
		</div>
		<div style='margin-top: 10px;'>
			<input style='float: left;' id="print" type="button" value="Print" />
			<input style='float: left; margin-left: 5px;' id="jpegButton" type="button" value="Save As JPEG" />
            <input style='float: left; margin-left: 5px;' id="pngButton" type="button" value="Save As PNG" /><!--
            <input style='float: left; margin-left: 5px;' id="pdfButton" type="button" value="Save As PDF" />-->
		</div>
	</div>
			
</body>
</html>
