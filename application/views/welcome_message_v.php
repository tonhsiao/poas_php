<style>
    .circleContainer {
		display: flex;
		align-items: center;
		justify-content: center
      }

    .circleLight {
        border: 4px solid #fff;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23)
    }

    div.circleLight > h3 {
       font-family: Microsoft JhengHei;
       margin-bottom: 0;
       font-weight: 1000;
       color: white;
   }

    .redLight {
        background: #dc3545
    }

    .greenLight {
        background: #28a745
    }

    .yellowLight {
        background: #fd7e14
    }

    .greyLight {
        background: #6c757d
    }
</style>
<script>
	$(function(){
		let lineChartCanvas = $('#lineChart').get(0).getContext('2d')
		let areaChartOptions = {
			maintainAspectRatio: false,
			responsive: true,
			datasetFill: false,
			legend: {
				display: true,
			},
			scales: {
				xAxes: [{
					gridLines : {
						display : false,
					}
				}],
				yAxes: [{
					gridLines : {
						display : true,
					},
					ticks: {
						min: 0,
						max: 5,
						stepSize: 1
					}
				}]
			}
		}
		let lineChartOptions = $.extend(true, {}, areaChartOptions)
		let lineChartData = $.extend()
		//lineChartData = $.extend(true, {}, data.areaChartData)
		let lineChart = new Chart(lineChartCanvas, {
			showTooltips: true,
			type: 'line',
			options: lineChartOptions,
			data: lineChartData,
			tooltips: {
				enabled: true,
				mode: 'index',
				intersect: false
			}
		})


		let stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
		areaChartOptions = {
			responsive              : true,
			maintainAspectRatio     : false,
			scales: {
				xAxes: [{
					stacked: true,
				}],
				yAxes: [{
					stacked: true
				}]
			}
		}
		let stackedBarChartOptions = $.extend(true, {}, areaChartOptions)
		let stackedBarChartData = $.extend()
		//let stackedBarChartData = $.extend(true, {}, data.stackedBarChartData)
		let barChart = new Chart(stackedBarChartCanvas, {
			type: 'bar',
			data: stackedBarChartData,
			options: stackedBarChartOptions
		})


		$("select[name=select_ISP]").on('change', function(){
			let tmp_isp = $(this).find('option:selected').val();
			$.ajax({
				'url': 'Welcome/getTypeLight/' + tmp_isp,
				'type': 'post',
				'data': $("#fm").serialize(),
				'dataType': 'json',
				success: function (data) {

					// console.log('測試中: getTypeLight, 成功');
					// console.log(data);
					// console.log(tmp_isp);	

					if (data.result) {
						for (let i in data.light) {
							$(".circleLight:eq(" + i + ")").removeClass().addClass("circleLight").addClass(data.light[i])
						}
					}
				}
			})

			$.ajax({
				'url': 'Welcome/getLineChartData/' + tmp_isp,
				'type': 'post',
				'data': $("#fm").serialize(),
				'dataType': 'json',
				success: function (data) {
					
					console.log('測試中: getLineChartData, 成功');
					console.log(data);
					console.log(tmp_isp);

					lineChartData = $.extend(true, {}, data.areaChartData)
					lineChart.data = lineChartData,
					lineChart.update();

					stackedBarChartData = $.extend(true, {}, data.stackedBarChartData)
					barChart.data = stackedBarChartData
					barChart.update();
					
				},
				error:function(xhr, ajaxOptions, thrownError){
					console.log('測試中: getLineChartData, 失敗');
					console.log(xhr);
					console.log(ajaxOptions);
					console.log(thrownError);


					console.log('測試中');

            	},
			})

			$("#example1").DataTable({
				"processing": true,
				"serverSide": true,
				"ajax": {
					"url": 'Welcome/getAjaxRawdata/' + tmp_isp,
					"type": "POST",
					"data": function (data) {
						data.brd_sourceWeb = $("select[name=select_sourceWeb] option:selected").val()
						data.brd_articleType = $("select[name=select_articleType] option:selected").val()
						data.brd_pushType = $("select[name=select_brd_pushType] option:selected").val()
					}
				},
				"deferRender": true,
				"destroy": true,
				"searching": true,
				"language": {
					"searchPlaceholder": "關鍵字搜尋"
				},
				"paging": true,
				"responsive": true,
				"lengthChange": false,
				"autoWidth": false,
				"ordering": true,
				"info": true,
				"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
				"order": [[6, 'desc']],
				"columnDefs": [
					{ "targets": [0, 1, 4, 5, 6], "className": "text-center"}
				],
				"columns": [
					{ "data": "brd_sourceWeb", "title":  "來源"},
					{ "data": "brd_articleType", "title":  "分類"},
					{ "data": "brd_title", "title":  "標題"},
					{ "data": "brd_contents", "title":  "內容"},
					{ "data": "brd_pushType", "title":  "主文/推文"},
					{ "data": "brd_ispScore", "title":  "情緒分數"},
					{ "data": "brd_insertDate", "title":  "日期"}
	            ],
	            "initComplete": function(oSettings) {
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0)").removeClass("col-md-6").addClass("col-md-8");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(1)").removeClass("col-md-6").addClass("col-md-4");

	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0)").empty();
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0)").append("<div class='row'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row").append("<div class='col-md-4'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row").append("<div class='col-md-4'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row").append("<div class='col-md-4'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(0)").append("<div class='form-group'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(1)").append("<div class='form-group'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(2)").append("<div class='form-group'>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(0) > div:eq(0)").append("<select class='form-control' name='select_sourceWeb'><option value=''>--- 來源 ----</option></select>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(1) > div:eq(0)").append("<select class='form-control' name='select_articleType'><option value=''>--- 分類 ----</option></select>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(2) > div:eq(0)").append("<select class='form-control' name='select_brd_pushType'><option value=''>--- 主文/推文 ----</option></select>");

	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(0) > div:eq(0) > select").append("<option value='Mobile01'>Mobile01</option><option value='PTT'>PTT</option><option value='Dcard'>Dcard</option><option value='FB'>FB</option><option value='UDN'>UDN</option>")
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(1) > div:eq(0) > select").append("<option value='5G'>5G</option><option value='資費'>資費</option><option value='網速'>網速</option><option value='收訊'>收訊</option><option value='其他'>其他</option>");
	            	$("div#example1_wrapper").find(".row:eq(0) > div:eq(0) > div.row > div:eq(2) > div:eq(0) > select").append("<option value='主文'>主文</option><option value='回文'>回文</option>");


					$("#example1_wrapper").on('change', 'select', function(){
						$("#example1").DataTable().draw()
					})
	            }
			});
		}).trigger('change');
	})
</script>
<?php
$msg = "";
$msg .= '<form method="post" id="fm">';

$msg .= '<div class="row justify-content-center align-items-center">';
foreach (array("5 G", "資 費", "收 訊", "網 速", "其 他") as $tmp_articleType) {
	$msg .= '<div class="col-md-2">';
	$msg .= '<div class="circleContainer">';
	$msg .= '<div class="circleLight greyLight">';
	$msg .= '<h3>' . $tmp_articleType . '</h3>';
	$msg .= '</div>';
	$msg .= '</div>';
	$msg .= '</div>';
}
$msg .= '</div>';//.row
$msg .= '<hr style="border-width: 0px;margin-top: 2em;">';

$msg .= '<div class="row">';

$msg .= '<div class="col-md-6">';
$msg .= '<div class="card card-success">';
$msg .= '<div class="card-header">';
$msg .= '<h3 class="card-title">輿情平均分數</h3>';
$msg .= '<div class="card-tools">';
$msg .= '<button type="button" class="btn btn-tool" data-card-widget="collapse">';
$msg .= '<i class="fas fa-minus"></i>';
$msg .= '</button>';
$msg .= '<button type="button" class="btn btn-tool" data-card-widget="remove">';
$msg .= '<i class="fas fa-times"></i>';
$msg .= '</button>';
$msg .= '</div>';
$msg .= '</div>';// .card-header
$msg .= '<div class="card-body">';
$msg .= '<div class="chart">';
$msg .= '<canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>';
$msg .= '</div>';
$msg .= '</div>';
$msg .= '</div>';// .card
$msg .= '</div>';// .col-md-6

$msg .= '<div class="col-md-6">';
$msg .= '<div class="card card-success">';
$msg .= '<div class="card-header">';
$msg .= '<h3 class="card-title">各分類討論熱度</h3>';
$msg .= '<div class="card-tools">';
$msg .= '<button type="button" class="btn btn-tool" data-card-widget="collapse">';
$msg .= '<i class="fas fa-minus"></i>';
$msg .= '</button>';
$msg .= '<button type="button" class="btn btn-tool" data-card-widget="remove">';
$msg .= '<i class="fas fa-times"></i>';
$msg .= '</button>';
$msg .= '</div>';
$msg .= '</div>';// .card-header
$msg .= '<div class="card-body">';
$msg .= '<div class="chart">';
$msg .= '<canvas id="stackedBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>';
$msg .= '</div>';
$msg .= '</div>';
$msg .= '</div>';// .card
$msg .= '</div>';// .col-md-6

$msg .= '</div>';// .row

$msg .= '<div class="row">';
$msg .= '<div class="col-md-12">';
$msg .= '<div class="card card-success">';
$msg .= '<div class="card-header">';
$msg .= '<h3 class="card-title">輿情原始資料</h3>';
$msg .= '<div class="card-tools">';
$msg .= '<button type="button" class="btn btn-tool" data-card-widget="collapse">';
$msg .= '<i class="fas fa-minus"></i>';
$msg .= '</button>';
$msg .= '<button type="button" class="btn btn-tool" data-card-widget="remove">';
$msg .= '<i class="fas fa-times"></i>';
$msg .= '</button>';
$msg .= '</div>';
$msg .= '</div>';// .card-header
$msg .= '<div class="card-body">';
$msg .= '<table id="example1" class="table table-bordered table-striped">';
$msg .= '<thead>';
$msg .= '<tr>';
$msg .= '<td class="text-center">來源</td>';
$msg .= '<td class="text-center">分類</td>';
$msg .= '<td class="text-center">標題</td>';
$msg .= '<td class="text-center">內容</td>';
$msg .= '<td class="text-center">主文/推文</td>';
$msg .= '<td class="text-center">情緒分數</td>';
$msg .= '<td class="text-center">日期</td>';
$msg .= '</tr>';
$msg .= '</thead>';
$msg .= '<tbody>';
$msg .= '</tbody>';
$msg .= '</table>';
$msg .= '</div>';
$msg .= '</div>';// .card
$msg .= '</div>';// .col-md-12
$msg .= '</div>';// .row

$msg .= '</form>';

print $msg;