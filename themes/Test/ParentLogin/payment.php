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
					<div class="span2">
						<div class="plan green" style="margin: 10px 0;">
							<div class="plan-header">
								<div class="plan-title">
									<div class="btn-group center-btn-group">
										<a class="btn btn-danger" href="#" id="selectedYearText">Select Year</a>
										<a class="btn btn-warning dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
										<ul class="dropdown-menu" id="year-dropdown">
																							
										</ul>
										<input type="hidden" id="selectedYearId">
									</div>	        		
								</div>
							</div>
							<div class="plan-features">
								<ul>
									<li><h3>Class: <span id="yearwise-class"></span></h3></li>
									<li><h3>Section: <span id="yearwise-section"></span></h3></li>
									<li><h3>Roll: <span id="yearwise-roll"></span></h3></li>
								</ul>
							</div>
						</div>
					</div>

					<div class="span2">
									<div class="plan green" style="margin: 10px 0;">
										<div class="plan-header">
											<div class="plan-title">
												<h3>Select Months:</h3>	        		
											</div>
										</div>
										<div class="plan-features month-list">
											<ul id="payable-month-list">					
												
											</ul>
										</div>
									</div>
					</div>

					<div class="span8">
						<div class="widget widget-table action-table">
							<div class="widget-header"> <i class="icon-th-list"></i>
							  <h3>Payment Summary</h3>
										
							</div>
							<!-- /widget-header -->
							<div class="widget-content text-center">
							 		<table id="fee-detail-table" class="table table-striped table-bordered" style="padding:10px">
										<thead>
										  <tr>
											<th> Fee Head </th>
											<th> Amount</th>
										  </tr>
										</thead>
										<tbody>
										  
										</tbody>
									</table>
									<button id="payment-btn" class="btn btn-primary btn-large hidden" style="margin: 10px;">Make Payment</button>
									<form method="POST" action="./Core/PaymentRequest.php" id="payment-form">
										<input type="hidden" name='yearId' id='p-yearId'>
										<input type="hidden" name='months' id='p-months'>
									</form>
							</div>
							<!-- /widget-content --> 
						  </div>
					</div>

				</div>
			</div> <!-- /container-->
		</div> <!-- /main-inner-->
	
	</div><!--/main-->
	<a href="./" class="float-btn"><i class='icon-arrow-left'></i></a>
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
			GetPayableMonths($("#personId").val(),$("#selectedYearId").val());
			GetYearWiseData($("#personId").val(),$("#selectedYearId").val());
		}
	});
	$(document).on('click', "#year-dropdown li a",function(){
		$("#selectedYearText").text($(this).text());
		$("#selectedYearId").val($(this).data('value'));
		GetPayableMonths($("#personId").val(),$("#selectedYearId").val());
		GetYearWiseData($("#personId").val(),$("#selectedYearId").val());
		$("#fee-detail-table tbody").html('');
		$("#payment-btn").addClass('hidden');
	})
	$(document).on('click','.month-checkbox',function(){
		if($(this).is(':checked')){
			$(this).parent().parent().nextAll().find('.month-checkbox').first().removeAttr("disabled");
			var monthArray=[];
			$('.month-checkbox').each(function(){
				if($(this).is(':checked')){
					monthArray.push($(this).val());
				}
				else{
					return false;
				}
			});
			GetPayableFees($("#personId").val(),$("#selectedYearId").val(),monthArray);
		}
		else{
			$('.month-checkbox').prop('checked', false).prop('disabled', true);
			$('.month-checkbox').first().removeAttr("disabled");
			$("#fee-detail-table tbody").html('');
			$("#payment-btn").addClass('hidden');

		}
	})
	$(document).on('click','#payment-btn', function(){
		$("#p-yearId").val($("#selectedYearId").val());
		var monthArray=[];
			$('.month-checkbox').each(function(){
				if($(this).is(':checked')){
					monthArray.push($(this).val());
				}
				else{
					return false;
				}
			});
		$("#p-months").val(monthArray);
		$("#payment-form").submit();
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
function GetPayableMonths(personId,yearId){
	$.ajax({
		url: './Core/getPayment.php',
		data:{'action':'getPayableMonths','personId':personId,'yearId':yearId},
		method: "GET",
		success: function(data){
			SetPayableMonths(data);
		}
	});
}
function SetPayableMonths(data){
	if(data==''){
		$("#payable-month-list").html('').append("<li>No payable month.</li>");
		return false;
	}
	var months=JSON.parse(data);
	var list="";
	months.forEach(function(elem,index){
		list+="<li><label class='checkbox month-name'><input type='checkbox' class='month-checkbox' value='"+elem[Object.keys(elem)[0]]+"' "+(index!=0?"disabled='true'":"")+">"+Object.keys(elem)[0]+"</label></li>";
	});
	$("#payable-month-list").html('').append(list);
}
function GetPayableFees(personId,yearId,months){
	$.ajax({
		url: './Core/getPayment.php',
		data:{'action':'getPayableFees','personId':personId,'yearId':yearId,'months':months},
		method: "GET",
		success: function(data){
			SetPayableFees(data);
		}
	});
}
function SetPayableFees(data){
	if(data==''){
		$("#fee-detail-table tbody").html('');
		$("#payment-btn").addClass('hidden');
		return false;
	}
	var fees=JSON.parse(data);
	var rows="";
	var total=0;
	Object.keys(fees).forEach(function(elem){
		rows+="<tr><td> "+elem+" </td><td><span class='pull-right'>"+parseFloat(fees[elem]).toFixed(2)+"</span></td></tr>";
		total+=parseFloat(fees[elem]);
	})
	rows+="<tr><td class='payment-total'> Total </td><td class='payment-total'><span class='pull-right'>"+total.toFixed(2)+"</span></td></tr>";
	$("#fee-detail-table tbody").html('').append(rows);
	$("#payment-btn").removeClass('hidden');
}
function GetYearWiseData(personId,yearId){
	$.ajax({
		url: './Core/getPayment.php',
		data:{'action':'getYearWiseData','personId':personId,'yearId':yearId},
		method: "GET",
		success: function(data){
			SetYearWiseData(data);
		}
	});
}
function SetYearWiseData(data){
	var yclass='';
	var ysection='';
	var yroll='';
	if(data!="false"){
		var profile=JSON.parse(data);
		yclass=profile.class;
		ysection=profile.section;
		yroll=profile.rollOrder!=null?profile.rollOrder:'';
	}
	$("#yearwise-class").html(yclass);
	$("#yearwise-section").html(ysection);
	$("#yearwise-roll").html(yroll);
	if(yroll==''){
		$("#yearwise-roll").parent().addClass('hidden');
	}
	else{
		$("#yearwise-roll").parent().removeClass('hidden');
	}
}
</script>
</html>
