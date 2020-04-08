/*
Contains all scrit for rendering dashboard elemnts.
@Nazmul
 */
$(document).ready(function(){
	var url=$('#ajax_url').val();
	var school_year=$('#school_year').val();
	var data_month=$('#data_month').val();
	var data_day=$('#data_day').val();
	var attendanceLoad=false;
	var paymentLoad=false;
	
	getStaff();
	getStudentNo(school_year);
	getTodaysCollection(school_year,data_day);
	getFeeCount(data_day);
	getTransportUser(school_year);
	getPendingApplication(school_year);
	getBirthday();
	
	$(window).scroll(function() {
		var height = $(window).scrollTop();
		//console.log(height);
		if(height  > 50 && !attendanceLoad) {
			getTodaysAttendance($('#school_year').val(),$('#data_day').val());
			attendanceLoad=true;
		}
		if(height  > 400 && !paymentLoad) {
			getPaymentHistory($('#school_year').val(),$('#data_month').val());
			paymentLoad=true;
		}
	});
	
	
	$('#school_year').change(function(){
		getStudentNo($(this).val());
		getTodaysCollection($(this).val(),$('#data_day').val());
		getTransportUser($(this).val());
		getPendingApplication($(this).val());
		getPaymentHistory($('#school_year').val(),$('#data_month').val());
	});
	$('#data_month').change(function(){
		getPaymentHistory($('#school_year').val(),$('#data_month').val());
	});
	
	$('#data_day').click(function(){
		getTodaysAttendance($('#school_year').val(),$(this).val());
		getTodaysCollection($('#school_year').val(),$(this).val());
		getFeeCount($(this).val());
	});
	$('.tile').hover(function(){
		$(this).find('.fa').toggleClass('fa-pulse');
		$(this).find('.d').toggleClass('big');
	});
	
})