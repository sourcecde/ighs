<?php
if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}
session_start();
if(!isset($_SESSION['user'])){
	$loginUrl="./login.php";
	header('Location: '.$loginUrl);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lakshya</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes"> 
	<link href="./css/pages/plans.css" rel="stylesheet" type="text/css">
	<?php include('./partials/cssInclude.php')?>
</head>
<body>
<input type='hidden' id='personId' value='<?=$_SESSION['user']['gibbonPersonID']?>'>
<?php include('./partials/navbar.php') ?>
	<div class="main">
		<div class="main-inner">
			<div class="container">
				<div class="row" style="margin-top:20px;">	      	
					<div class="span3">
					    <?php include('./partials/profile.php')?>
					</div> <!-- /span6 -->
					<div class="span9">
						<div class="widget widget-table action-table">
							<div class="widget-header"> <i class="icon-th-list"></i>
							  <h3>Fee Details</h3>
										<div class="controls year-dropdown">
                                            <div class="btn-group">
                                              <a class="btn btn-danger" href="#" id="selectedYearText"></a>
                                              <a class="btn btn-warning dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
                                              <ul class="dropdown-menu" id="year-dropdown">
                                                
                                              </ul>
											  <input type="hidden" id="selectedYearId">
                                            </div>
                                        </div>
										
							</div>
							<!-- /widget-header -->
							<div class="widget-content" style='overflow-x:auto'>
							  <table class="table table-striped table-bordered" id="fee-details-table">
								<thead>
								  <tr>
									<th> Month </th>
									<th> Amount</th>
									<th class="td-actions"> Status</th>
									<th> Due Date</th>
									<th> Reference</th>
								  </tr>
								</thead>
								<tbody>
								  
								</tbody>
							  </table>
							</div>
							<!-- /widget-content --> 
						  </div>
					</div> <!-- /span6 -->
				</div> <!-- /row-->
			</div> <!-- /container-->
		</div> <!-- /main-inner-->
	
	</div><!--/main-->
<?php include('./partials/jsInclude.php')?>
</body>
<script>
$(function(){
	$.ajax({
		url:'./Core/getSchoolYears.php',
		data:{'action':'getSchoolYears'},
		method: "GET",
		success: function(data){
			var years=JSON.parse(data);
			SetYearDropdown(years);
			GetFeeDetails($("#personId").val(),$("#selectedYearId").val());
		}
	});
	$(document).on('click', "#year-dropdown li a",function(){
		$("#selectedYearText").text($(this).text());
		$("#selectedYearId").val($(this).data('value'));
		GetFeeDetails($("#personId").val(),$("#selectedYearId").val());
	})
	
	$(document).on('click', ".row-expand",function(){
		$(this).next('tr').toggleClass('hidden');
	})

	$(document).on('click','.fee-print-btn', function(e){
		var RefNo=$(this).data('ref-no');
		$.ajax({
			url:'./Core/printreciept.php',
			data:{'RefNo':RefNo},
			method: "GET",
			success: function(data){
				var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
				$(w.document.body).html(data);
				w.print();
			}
		});
		return false;
	})
})
function SetYearDropdown(years){
	if(years.length>0){
		var currentYearId;
		var currentYearText;
		var list="";
		years.forEach(function(year){
			if(year.status=='Current'){
				currentYearId=year.gibbonSchoolYearID;
				currentYearText=year.name
			}
			list+="<li><a href='#' data-value='"+year.gibbonSchoolYearID+"'>"+year.name+"</a></li>";
		});
		$("#selectedYearText").text(currentYearText);
		$("#selectedYearId").val(currentYearId);
		$("#year-dropdown").html('').append(list);
	}
}
function GetFeeDetails(personId,yearId){
	if(personId=='' || yearId=='')
		return false;
	$.ajax({
		url:'./Core/getFeeDetails.php',
		data:{'personId':personId,'yearId':yearId},
		method: 'GET',
		success:function(data){
			SetFeeDetailsTable(data);
		}
	});
}
function SetFeeDetailsTable(data){
	if(data=='null'){
		$("#fee-details-table tbody").html("<tr><td colspan='4'>No data available.</td></tr>");
		return false;
	}
	var feeDetails=JSON.parse(data);
	if(feeDetails.length==0)
		return false;
	var tbody="";
	feeDetails.forEach(function(detail){
		tbody+="<tr class='row-expand'><td> "+detail.Month+" </td><td> <span class='pull-right'>"+parseFloat(detail.Amount).toFixed(2)+"</span> </td>";
		tbody+="<td class='td-actions'><a href='javascript:;' class='btn btn-small btn-"+(detail.Status=='Paid'?'success':'danger')+"'><i class='btn-icon-only icon-"+(detail.Status=='Paid'?'ok':'remove')+"'> </i> "+detail.Status+"</a></td>";
		tbody+="<td>"+(detail.Status=='Paid'?'-':detail.DueDate)+"</td>";
		tbody+="<td>"+(detail.Reference==null?'-':(detail.Reference+"<button class='btn btn-warning pull-right fee-print-btn' data-ref-no='"+detail.Reference+"'><i class='icon-print'></i></button>"))+"</td></tr>";
		var table="<tr class='hidden'> <td colspan='5'> <table class='table table-striped table-bordered'> <tr><th>Fee</th><th>Amount</th></tr>";
		detail.Fees.forEach(function(fee){
			table+="<tr><td>"+fee.Name+"</td><td><span class='pull-right'>"+parseFloat(fee.Amount).toFixed(2)+"</span></td></tr>";
		});
		table+="</table> </td> </tr>";
		tbody+=table+"<tr></tr>";
	})
	$("#fee-details-table tbody").html(tbody);
}
</script>
</html>
