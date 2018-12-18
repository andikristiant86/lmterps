<?php
ob_start();
session_start();
include("$DOCUMENT_ROOT/s/config.php");
$act=empty($act)?"Region":"$act";
if($act=='All')		$desc='All Employee';
elseif($act=='Region')	$desc='All Region';
elseif($act=='Management')	$desc='All Management';


$cek_lokasi=$db->getOne("SELECT X.lokasi_kerja FROM (
SELECT lokasi_id,lokasi_kerja FROM BACKUP_RESOURCE WHERE cmp_id='CMP-000001' GROUP BY lokasi_id,lokasi_kerja
) X WHERE X.lokasi_id='$act'");
if($cek_lokasi){
	$desc='Employee';$area="$cek_lokasi";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title id='Description'>Dashboard Cash Flow & Revenue</title>
    <meta name="description" content="This is an example of JavaScript Chart Column Series." />
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
		
			 function getExportServer() {
                return 'https://www.jqwidgets.com/export_server/export.php';
            }
		
            // prepare chart data as an array            
            var source =
            {
                datatype: "json",
                datafields: [
                    { name: 'Periode' },
                    { name: 'Salary' },
                    { name: 'Employee' },
					{ name: 'Employee_cost' }
                ],
                url: 'employee_vs_salaryGet.php?act=<?=$act;?>'
            };
            var dataAdapter = new $.jqx.dataAdapter(source, { async: false, autoBind: true, loadError: function (xhr, status, error) { alert('Error loading "' + source.url + '" : ' + error); } });
            // prepare jqxChart settings
            var settings = {
                title: "CASHFLOW & REVENUE <?=$area;?><br>",
                description: "Periode Jan 2017 s/d Jan 2018",
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
                            valueAxis:
                            {
                                visible: true,
                                unitInterval: 200,
                                title: { text: 'Employee (Amount)<br>' }
                            },
                            series: [
                                    { dataField: 'Employee', displayText: 'Employee' },
									{ dataField: 'Employee_cost', displayText: 'Employee cost ratio' }
                                ]
                        },
                        {
                            type: 'line',
							toolTipFormatSettings: { thousandsSeparator: ',' },
                            valueAxis:
                            {
                                visible: true,
                                position: 'right',
                                
                                title: { text: 'Salary (Rp)' },
                                gridLines: { visible: false },
								labels: {
									visible: true,horizontalAlignment: 'left',
									formatFunction: function (value) {
										return (value / 1000000000)+'M';
									}
								}
                            },
                            series: [
                                    { dataField: 'Salary', displayText: 'Salary' }
                                ]
                        }
                    ]
            };
            // setup the chart
            $('#chartContainer').jqxChart(settings);
			
			//$("#Country").jqxComboBox({ width: '15%', height: 25 });
			
			//$("#jqxButton").jqxLinkButton({ width: '10%', height: '30'});$("#jqxButton1").jqxLinkButton({ width: '10%', height: '30'});
			//$("#jqxButton2").jqxLinkButton({ width: '13%', height: '30'});
			
			
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
<body class='default'><!--
<a style='margin-left: 1px;' href="/chart/employee_vs_salary.php?act=All" id='jqxButton'>All Employee</a>
<a style='margin-left: 1px;' href="/chart/employee_vs_salary.php?act=Region" id='jqxButton1'>All Region</a>
<a style='margin-left: 1px;' href="/chart/employee_vs_salary.php?act=Management" id='jqxButton2'>All Management</a>

<select id="Country" onchange="hourChange(this);">
<option>PERIODE 2017</option>
<option>PERIODE 2018</option>
</select>-->

<div id='jqxMenu' style='visibility: visible;'>
        <ul>
            <li><a href="/chart/cashflow.php?act=Region">All Region</a></li>
			<?PHP
				$sql="select lokasi_id,lokasi_kerja from BACKUP_RESOURCE WHERE cmp_id='CMP-000001' GROUP BY lokasi_id,lokasi_kerja ORDER BY lokasi_id";
				$resultx=$db->Execute($sql);
				while($row=$resultx->Fetchrow()){
					foreach($row as $key=>$val){
						$key=strtolower($key);
						$$key=$val;				
					}
					
				
			?>
            <li><a href="/chart/cashflow.php?act=<?=$lokasi_id;?>"><?=ucwords(strtolower("$lokasi_kerja"));?></a></li>
            
			<?PHP
			}
			?>
        </ul>
    </div>
	
    <!--<div id="minimizeCheckbox">Minimized</div>-->

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
