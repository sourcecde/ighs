$(document).ready(function(){
	$(".delete_fee").click(function(){
		var r=confirm("Sure want to delete this rule?");
		if(r)
			{
			var id=$(this).attr("id");
			//alert(id);
			var linkurl=$("#delete_url").val();
			$.ajax
	 		({
	 			type: "POST",
	 			url: linkurl,
	 			data: {action:'delete_rule',id:id},
	 			success: function(msg)
	 			{
	 				 alert('Rule Deleted');
	 				location.reload();
	 			}
	 			});
			}
		
	})
	
	$(".condession").click(function(){
		var tempid=$(this).attr("id");
		var idarr=tempid.split("_");
		var id=idarr[0];
		var fee_type=$("#"+id+"_fee_type_name").html();
		var monthname=$("#"+id+"_month_name").html();
		$("#hidden_fee_payable_id").val(id);
		$("#concession_text").html(fee_type+" Of "+monthname);
		$("#give_concession").show();
	})
	
	$("#close_concession").click(function(){
		$("#give_concession").hide();
	})
	
	$("#submit_concession").click(function(){
		var id=$("#hidden_fee_payable_id").val();
		var amount=parseFloat($("#"+id+"_amount").text());
		var linkurl=$("#cocession_url").val();
		var concession_amount=$("#concession_amount").val();
		 if(concession_amount > amount) {
			alert('Concession amount should be less than amount!!');
			return;
		} 
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:'give_concession',id:id,concession_amount:concession_amount},
 			success: function(msg)
 			{
 				 alert('Concession Given');
 				$("#give_concession").hide();
 				location.reload();
 			}
 			});
	})
	
	$("#concession_amount").keyup(function(){
		var concession=$("#concession_amount").val();
		
		if(isNaN(concession))
			{
			alert("Please insert number");
			$("#concession_amount").val(0);
			}
	})
	
	$("#go").click(function(){
		var account_number=$("#account_number").val();
		resetPaymentStatus();
		var enrollid='';
		var checkurl=$("#get_personID_from_accno_url").val();
		$.ajax
 		({
 			type: "POST",
 			url: checkurl,
 			data: {account_number:account_number},
 			success: function(msg)
 			{
 				console.log(msg);
 				if(msg=='0')
 					{
 					alert("Account Number does not exist");
 					return false;
 					}
 				else {
					$('#student_personID option[value="' + msg + '"]').prop('selected', true);
					getPaymentStatus();
					var personID=$('#student_personID').val();
					var yearID=$('#fianacialyear').val();
					getStudentDetails(personID, yearID);
 				}
 			}
 			});
	})
	$("#student_personID,#fianacialyear").change(function(){
		resetPaymentStatus();
		getPaymentStatus();
		var personID=$('#student_personID').val();
		var yearID=$('#fianacialyear').val();
		getStudentDetails(personID, yearID);
	});
function resetPaymentStatus(){
	$("#print").hide();
		var mohtharr=['yearly','jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
		var monthamountarr=$("#all_fee_type_json").val().split(",");
		for(var j=0;j<mohtharr.length;j++)
			{
			$("#"+mohtharr[j]).show();
			$("#tdchk_"+mohtharr[j]).css('color','black');
			$("#tdchklbl_"+mohtharr[j]).css('color','black');
			
			$("#"+mohtharr[j]).attr("disabled",false);
			$("#"+mohtharr[j]).attr("checked",false);
			}
		for(var j=0;j<monthamountarr.length;j++)
			{
			$("#"+monthamountarr[j]).val(0.00);
			}
		$("#total_amount").val(0.00);
}
function getPaymentStatus(){
					var linkurl=$("#show_history_url").val();
					var fianacialyear=$("#fianacialyear").val();
					var personID=$("#student_personID").val();
 					if(personID)
					{
					$.ajax
					({
						type: "POST",
						url: linkurl,
						data: {action:'show_history_payment',personID:personID,year:fianacialyear},
						success: function(msg)
						{ 
							console.log(msg);
							$('.selecte_month_class').removeClass('paid');
							for(var i=0;i<msg['montharr'].length;i++)
							{
								$("#"+msg['montharr'][i]).attr("disabled",true).addClass('paid');
								$("#tdchk_"+msg['montharr'][i]).css('color','rgb(7, 252, 19)');
								$("#tdchklbl_"+msg['montharr'][i]).css('color','rgb(7, 252, 19)');
							}
								
							$("#studentEnrolmentID").val(msg['studentEnrolmentID']);
							var notPaidmonths=$('.selecte_month_class').not('.paid');
							notPaidmonths.each(function(){
								$(this).attr("disabled",true);
							});
							notPaidmonths.first().removeAttr("disabled");
							if(msg['isPaymentDue']){
								$('#payment-due-alert').css('display','block');
								$('#pay').removeClass('payment-allow').addClass('payment-block').attr('disabled',true);
							}
							else{
								$('#payment-due-alert').css('display','none');
								$('#pay').removeClass('payment-block').addClass('payment-allow').removeAttr('disabled');
							}
						}
						});
					}
}
	
	$("#fine_amount,#specialFeeAmount").blur(function(){
		var fine_amount=parseFloat($("#fine_amount").val());
		var specialFeeAmount=parseFloat($("#specialFeeAmount").val());
		if(isNaN(fine_amount)){
			$("#fine_amount").val(0);
			fine_amount=0;
		}
		if(isNaN(specialFeeAmount)){
			$("#specialFeeAmount").val(0);
			specialFeeAmount=0;
		}
		console.log(fine_amount);
		/*
		var previoustoal=parseFloat($("#total_amount").val());
		*/
		var total_fee=0;
		$(".fee_type_value_class").each(function(){
			
			total_fee+=parseFloat($(this).val());
		})
		var transport=0;
		if($("#include_transport").is(':checked')) {
		transport=parseFloat($("#transport_amount").val());
		}
		var newtoala=parseFloat(total_fee+fine_amount+transport+specialFeeAmount);
		$("#total_amount").val(newtoala.toFixed(2));
	})
	
	$("#condition").change(function(){
		var monthamountarr=$("#all_fee_type_json").val().split(",");
				for(var j=0;j<monthamountarr.length;j++)
				{
				$("#"+monthamountarr[j]).val(0.00);
				}
			$('#transport_amount').val(0.00);
			$("#fine_amount").val(0.00);
			$("#total_amount").val(0.00);
			$('.selecte_month_class').attr('checked', false);
	})
	
	$(".selecte_month_class").click(function(){
		 if (this.checked) {
			 var monthno=$(this).val();
			 
			 var studentEnrolmentID=$("#studentEnrolmentID").val();
			 var financialyear=$("#fianacialyear").val();			 if(studentEnrolmentID=='')				{				 alert("Please select student!");				 $("#student_personID").focus();				 return false;				 } 
			 // if($(".selecte_month_class:checked").length <= 1){
				// checkdefault(monthno,financialyear,studentEnrolmentID);
			 // }
			 var condition=$("#condition").val();
			 $('#include_transport').attr('checked', false);
			 
			 var linkurl=$("#month_fee_url").val();
			 var chkBox=this;
			 $.ajax
		 		({
		 			type: "POST",
		 			url: linkurl,
		 			data: {action:'getmonthfee',studentEnrolmentID:studentEnrolmentID,monthno:monthno,financialyear:financialyear,condition:condition},
		 			success: function(msg)
		 			{
		 				console.log(msg);
		 				//alert(msg);
		 				//console.log(msg);
		 				var pervious_total=parseFloat($("#total_amount").val());
		 				var newtotal=0;
						if(condition!='only_trans') {
							var feetypestr=$("#all_fee_type_json").val();
							var feetypearr=feetypestr.split(",");
							for(var i=0;i<feetypearr.length;i++)
								{
										var feetype=feetypearr[i];
										var previousval=parseFloat($("#"+feetype).val());
										//console.log(previousval);
										var newval=0;
										if(!isNaN(msg[feetype]))
										{
											newval=parseFloat(msg[feetype]);
										}
										
										//console.log(newval);
										var currentval=parseFloat(previousval+newval);
										pervious_total=parseFloat(pervious_total+newval);
										$("#"+feetype).val(currentval.toFixed(2));
										if(currentval!=0){
											$('#fee_row'+feetype).show();
										}
								}
						}
						if(condition!='ex_trans') {
								var transport_fee=parseFloat($("#transport_amount").val());
								if(!isNaN(msg['transport']))
										{
											transport_fee+=parseFloat(msg['transport']);
											pervious_total+=parseFloat(msg['transport']);
											$('#transport_amount').val(transport_fee.toFixed(2));
											if(transport_fee>0){
											$('#include_transport').attr('checked', true);
											$('#fee_row_transport').show();
											}
										}
						}
							$("#total_amount").val(pervious_total.toFixed(2));
						//For restricting March & April Payment together
						if(monthno!=3){
							$(chkBox).parents('tr').first().nextAll().find('.selecte_month_class').first().removeAttr("disabled");
						}
		 			}
		 			});
		    }
		 //this is uncheked
		 else
			 {
			 var monthamountarr=$("#all_fee_type_json").val().split(",");
				for(var j=0;j<monthamountarr.length;j++)
				{
				$("#"+monthamountarr[j]).val(0.00);
				$("#fee_row"+monthamountarr[j]).hide();
				$('#fee_row_transport').hide();
				}
			$('#transport_amount').val(0.00);
			$("#total_amount").val(0.00);
			
				$('.selecte_month_class').each(function(){
					$(this).attr('checked', false);
				});
				var notPaidmonths=$('.selecte_month_class').not('.paid');
				notPaidmonths.each(function(){
					$(this).attr("disabled",true);
				});
				notPaidmonths.first().removeAttr("disabled");
			 }
			
	})
	
	$("#pay").click(function(){
	    //alert("abc");
	    //return false;
	    
			if($('#specialFeeAmount').val()>0 && $('#specialFee').val()==''){
				alert("Selecet a special fee!!");
				$('#specialFee').focus();
				return;
			}
			var payment_dateformat=$("#payment_date").val().split("/");
			var payment_date=payment_dateformat[2]+'-'+payment_dateformat[1]+'-'+payment_dateformat[0];
			var fianacialyear=$('#fianacialyear').val();
				checkLock(payment_date);
	});
	var flag=0;
	function checkdefault(monthno,financialyear,studentEnrolmentID){
		var checkdefaultURL=$("#checkdefaultURL").val();
		$.ajax({
			type:"POST",
			url:checkdefaultURL,
			data: {monthno:monthno,financialyear:financialyear,studentEnrolmentID:studentEnrolmentID},
			success: function(msg){
					if(msg!=""){
						alert(msg);
						console.log(msg);
						location.reload();
					}
			}
		});
	}
	/* Check Lock */
	function checkLock(date){
		var linkurl=$("#month_fee_url").val();
		var r=false;
		console.log(date);
		$.ajax
	 		({
	 			type: "POST",
	 			url: linkurl,
	 			data: {
					action:'checklock',payment_date:date},
	 			success: function(msg)
	 			{
					console.log("T"+msg);
					if(msg!=0){
						cAlert("Payment is locked on given Month. You should Unlock payment on that Month or try on different Date");
						//alert("Payment is locked on given Month. You should Unlock payment on that Month or try on different Date");
						
						return;
					}
					else
						makePayment();
				}
			});
	}
	/*  Check Lock */
	/* Payment */
	function makePayment(){
		var r=confirm("Payment Confirmation ?");
		if(r)
			{
			var linkurl=$("#month_fee_url").val();
			var condition=$("#condition").val();
			var montharr=new Array();
			var fine_amount=$("#fine_amount").val();
			var total_amount=$("#total_amount").val();
			var monthnamearr=[];
			
			var transport=0;
			if($("#include_transport").is(':checked')){
				transport=1;
				}else{
				transport=0
				}
			if($("#payment_date").val()=='')
			{
			alert("Please insert payment date!");
			$("#payment_date").focus();
			return;
			}
			var payment_dateformat=$("#payment_date").val().split("/");
			var payment_date=payment_dateformat[2]+'-'+payment_dateformat[1]+'-'+payment_dateformat[0];
			
			var vouchar_no=$("#vouchar_no").val();
			var payment_mode=$("#payment_mode").val();
			var bankID=$("#bankID").val();
			if(payment_mode!='cash' && bankID=='0'){
				alert("Please Select Bank Name!");
				$("#bankID").focus();
				return;
			}
			var tracking_id=$("#tracking_id").val();
			var order_id=$("#order_id").val();
			var cheque_no=$("#cheque_no").val();
			var cheque_bank=$("#cheque_bank").val();
			var cheque_datearr=$("#cheque_date").val().split("/");
			var cheque_date=cheque_datearr[2]+'-'+cheque_datearr[1]+'-'+cheque_datearr[0];
			var studentEnrolmentID=$("#studentEnrolmentID").val();
			var gibbonPersonID=$("#student_personID").val();
			var schoolyear=$("#fianacialyear").val();
			var specialFee={};
			specialFee['id']=$('#specialFee').val();
			specialFee['amount']=$('#specialFeeAmount').val();
			
			$('input:checkbox.selecte_month_class').each(function () {
				if(this.checked)
				{
					var sThisVal=$(this).val();
					montharr.push(sThisVal);
					monthnamearr.push('\''+this.id+'\'');
				}
			  });
			if(montharr.length==0 && $('#specialFee').val()=='')
				{
				alert("Please select month");
				return false;
				}
				
				$.ajax
				({
					type: "POST",
					url: linkurl,
					data: {
						action:'payment',montharr:montharr,fine_amount:fine_amount,total_amount:total_amount,payment_date:payment_date,vouchar_no:vouchar_no,payment_mode:payment_mode,bankID:bankID,cheque_no:cheque_no,cheque_date:cheque_date,cheque_bank:cheque_bank,tracking_id:tracking_id,order_id:order_id,studentEnrolmentID:studentEnrolmentID,gibbonPersonID:gibbonPersonID,transport:transport,monthnamearr:monthnamearr,schoolyear:schoolyear,condition:condition,specialFee:specialFee},
					success: function(msg)
					{
						console.log(msg);
						var arr=msg.split("_");
						//alert(msg);
						$("#payment_master_id").val(arr[0]);
				
						$("#show_voucher_no").text('Generated voucher No: '+arr[1]);
						$('#transport_amount').val(0); 
						$("#fine_amount").val(0);
						$("#total_amount").val(0);
						
						$("#hide_body").show();
						$("#voucher_no_panel").fadeIn();
					}
					}); 
				
			}
	}
	/* Payment */

	
	$("#close_voucher_no").click(function(){
		
	 	$("#voucher_no_panel").hide();
		$("#hide_body").fadeOut();
		location.reload();
	})
	
	$("#print_voucher").click(function(){
		var url=$("#print_page_url").val();
		var print_master_id=$("#payment_master_id").val();
		window.open(url+'?pmid='+print_master_id,null,"height=400,width=700,status=yes,toolbar=no,menubar=no,location=no");
		
	})
	
	$("#concession_print_page").click(function(){
		var url=$("#cocession_report_url").val()+'?a=1';
		var student_personID=$("#student_personID").val();
		var year_id=$("#year_id").val();
		var class_id=$("#class_id").val();
		var month_filter=$('#month_filter').val();
		var fee_type_filter=$("#fee_type_filter").val();
		var payment_status_filter=$("#payment_status_filter").val();
		var display_mode_filter=$("#display_mode_filter").val();
		
		if(student_personID!=0)
			url+='&student_personID='+student_personID;
		if(year_id!='')
			url+='&year_id='+year_id;
		if(class_id!='')
			url+='&class_id='+class_id;
		if(month_filter!='')
			url+='&month_filter='+month_filter;
		if(fee_type_filter!='')
			url+='&fee_type_filter='+fee_type_filter;
		if(payment_status_filter!='')
			url+='&payment_status_filter='+payment_status_filter; 
		if(display_mode_filter!='')
			url+='&display_mode_filter='+display_mode_filter
		window.open(url,null,"height=400,width=700,status=yes,toolbar=no,menubar=no,location=no");
	})
	$("#print_headwise").click(function(){
		var url=$("#print_page_url").val();
		var from_date=$("#print_from_date").val();
		var to_date=$("#print_to_date").val();
		var p_mode=$("#p_mode").val();
		var year=$("#year_id").val();
		var p_monthduration=$("#p_monthduration").val();
		window.open(url+'?from_date='+from_date+'&to_date='+to_date+'&year='+year+'&p_mode='+p_mode+'&p_monthduration='+p_monthduration,null,"height=400,width=700,status=yes,toolbar=no,menubar=no,location=no");
	})
	
	$(".print_list_print").click(function(){
		var url=$("#print_page_url").val();
		var idarr=$(this).attr("id").split("_");
		var id=idarr[0];
		window.open(url+'?pmid='+id,null,"height=400,width=700,status=yes,toolbar=no,menubar=no,location=no");
	})
	$("#defaulter_print").click(function(){
		var left=0;
		if($('#left_student').is(":checked"))
			{
			left=1;
			}
	
		
			var url=$("#print_page_url").val();
			var src_to_date=$("#src_to_date").val();
			var year=$("#year_id").val();
			var classname=$("#class_name").val();
			var sectionname=$("#section_name").val();
			var monthcondition=$("#month_condition").val();
			var monthnamecondition=$("#month_name_condition").val();
			//window.open(url+'?src_to_date='+src_to_date+'&class='+classname+'&monthcondition='+monthcondition,null,",,status=yes,toolbar=no,menubar=no,location=no");
			window.open(url+'?src_to_date='+src_to_date+'&class='+classname+'&monthcondition='+monthcondition+'&monthnamecondition='+monthnamecondition+'&sectionname='+sectionname+'&left='+left+'&year='+year,'mypopup','status=1,width=500,height=500,scrollbars=1');
		})
	
	
	$("#defaulter_form").submit(function(){
		var src_to_date=$("#src_to_date").val();
		if ($('input[name^=selected_month_report]:checked').length <= 0) {
        alert("Please select months.");
		$('input[name^=selected_month_report]:checked').focus();
		return false;
    }
		
		if(src_to_date=='')
			{
			alert("Please select end date");
			$("#src_to_date").focus();
			return false;
			}
	})
	
	$(".voucher_type_class").click(function(){
		
		var typevalue=$(this).val();
		
		switch(typevalue)
		{
		case '1':
			$("#vouchar_no").attr("readonly",true);
			break;
			
		case '2':
			$("#vouchar_no").attr("readonly",false);
			break;
		}
	})
	
	$("#include_transport").click(function(){
		var montharr=[];
		var studentenrollid=$("#student_enrollid").val();
		var linkurl=$("#check_transport_url").val();
		if(this.checked)
			{
				$(".selecte_month_class").each(function(){
					if(this.checked)
						{
						montharr.push('\''+this.id+'\'');
						}
				})
				if(studentenrollid!='' && montharr.length>0)
					{
					$.ajax
			 		({
			 			type: "POST",
			 			url: linkurl,
			 			data: {action:'checkmonth',studentenrollid:studentenrollid,montharr:montharr},
			 			success: function(msg)
			 			{
			 				//alert(msg);
			 				//console.log(msg);
			 				$("#transport_amount").val(msg);
			 				var prevtpt=parseFloat($("#total_amount").val());
			 				var transport=parseFloat(msg);
			 				var newtot=parseFloat(prevtpt+transport).toFixed(2);
			 				$("#total_amount").val(newtot);
			 			}
			 			});
					}
				
			}
		else{
			var transport=$("#transport_amount").val();
			$("#transport_amount").val(0);
			var totamt=$("#total_amount").val();
			var newtot=(totamt-transport).toFixed(2);
			$("#total_amount").val(newtot);
		}
	})
	
	/* Function for changing section Year wise */
	$("#year_id").change(function(){
		var year_id=$("#year_id").val();
			$.ajax
	 		({
	 			type: "GET",
	 			url: "modules/Fee/custom_ajax.php",
	 			data: { year_id_select_section: year_id},
	 			success: function(msg)
	 			{ 
				$("#section_name").empty().append(msg);	 			
				$("#section_name_all").empty().append("<option value=''>All</option>"+msg);	 			
	 				//console.log(msg);
	 			}
	 			});
	})
	
	
	$(".print_list_delete2").click(function(){
			
				var idarr=$(this).attr("id").split("_");
				var id=idarr[0];
				var r=confirm("Do You really want to delete this entry?");
				//alert(id);
				if(r) {
					var linkurl=$("#delete_fee_url").val();
					$.ajax
					({
						type: "POST",
						url: linkurl,
						data: {action:'deletepayment',payment_master_id:id},
						success: function(msg)
						{
							//alert(msg);
							alert("Successfully Deleted");
							location.reload();
						}
					});
				}
	
	})
	
	
	$('#search_by_acc').click(function(){
		var account_number=$("#account_number").val();
		var checkurl=$("#get_personID_from_accno_url").val();

		//alert(account_number);
		$.ajax
 		({
 			type: "POST",
 			url: checkurl,
 			data: {account_number:account_number},
 			success: function(msg)
 			{
 				console.log(msg);
 				if(msg=='0')
 					{
 					alert("Account Number does not exist");
 					return false;
 					}
 				else
 					{
 					$('#student_personID option[value="' + msg + '"]').prop('selected', true);
 					$('#src_student option[value="' + msg + '"]').prop('selected', true);
					}
			}
		}); 
	});
	
	function getStudentDetails(personID, yearID){
		var url=$('#studentDetailsUrl').val();
		$.ajax
 		({
 			type: "POST",
 			url: url,
 			data: {personID: personID, yearID: yearID},
 			success: function(msg)
 			{
				var data=$.parseJSON(msg);
				$('#s_name').html(data['preferredName']);
				$('#s_class').html(data['class'] + " " + data['section']);
				$('#s_roll').html(data['rollOrder']);
				$('#s_accno').html(parseInt(data['account_number'])+0);
				$('#detail_panel').fadeIn();
			console.log(data);
			}
		});
	};
	
	$("#checkall").click(function(){
		if (this.checked)
			{
				$(".list_chk").each(function(){
					this.checked=true;
				})
			}
		else
			{
			$(".list_chk").each(function(){
				this.checked=false;
			})
			}
	})
	
	$("#lock_payment").click(function(){
		var r=confirm("Do you really want to lock this selected entry ?");
		if(r)
		{
			var idarr=[];
			$(".list_chk").each(function(){
				if($(this).attr("checked"))
				{
					idarr.push($(this).val());
				}
			})
			var linkurl="modules/Fee/payment_lock_ajax.php";
			$.ajax
					({
						type: "POST",
						url: linkurl,
						data: {action:'lock_payment',id: idarr},
						success: function(msg)
						{
							//alert(msg);
							alert("Successfully Locked");
							location.reload();
						}
					});
			
		}
	})
	
	$("#unlock_payment").click(function(){
		var r=confirm("Do you really want to unlock this selected entry ?");
		if(r)
		{
			var idarr=[];
			$(".list_chk").each(function(){
				if($(this).attr("checked"))
				{
					idarr.push($(this).val());
				}
			})
			var linkurl="modules/Fee/payment_lock_ajax.php";
			$.ajax
					({
						type: "POST",
						url: linkurl,
						data: {action:'unlock_payment',id: idarr},
						success: function(msg)
						{
							//alert(msg);
							alert("Successfully Unocked");
							location.reload();
						}
					});
			
		}
	})
	
	function cAlert(message){
		$("#alert_message").text(message);
		$("#hide_body").show();
		$("#alert").fadeIn();
	}
	$("#close_alert").click(function(){
		$("#alert").hide();
		$("#hide_body").fadeOut();
	})
	
	/* Defaulter Report */
		$("#view").change(function(){	
			if($(this).val()=='Short'){
				$('.detail').hide();
				$('.short').show();
			}
			else{
				$('.short').hide();
				$('.detail').show();
			}
		});
		$("#year_id").change(function(){
			getSection($(this).val());
		});
		function getSection(y_id){
					$.ajax
					({
						type: "GET",
						url: "modules/Fee/custom_ajax.php",
						data: { year_id_select_section: y_id},
						success: function(msg)
						{ 
						$("#section_name").empty().append(msg);	 			
						//$("#section_name").prepend("<option value=''> All </option>");	 			
							//console.log(msg);
						}
					});
		}			
		$('#defaulter_print_c').click(function(){
					var months=$('#month_name').val();
					var date=$('#src_to_date').val();
					//var head="<h3><center>Defaulter Report for the month of "+months+" as on "+date+"</center></h3>";
				var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
				var html=$('#print_page').html();
				//console.log(html);
				$(w.document.body).html(html);
				//$(w.document.body).prepend(head);
				$(w.document.body).append(style);
				w.print();
		});
	/* Defaulter Report */
	$('#collection_print_c').click(function(){
				var months=$('#month_name').val();
				var date=$('#src_to_date').val();
				var head="<h3><center>Collection Report for the month of "+months+" as on "+date+"</center></h3>";
				var w=window.open("","","height=600,width=700,status=yes,toolbar=no,menubar=no,location=no");
				var html=$('#print_page').html();
				console.log(html);
				$(w.document.body).html(html);
				$(w.document.body).prepend(head);
				$(w.document.body).append($('#cstyle').html());
				$(w.document.body).find('table').addClass('p_table');
				$(w.document.body).find('th').addClass('p_head');
				$(w.document.body).find('td').addClass('p_td');
				w.print();
		});
	/* Ledger */
		$('#selectAll').click(function(){
			var flag=$(this).is(":checked");
				$('.name_select').each(function(){
					$(this).prop('checked',flag);
				});
		});
		$('.student').click(function(){
			$('#expand').hide();
			$('.focused').removeClass('focused');
			$(this).addClass('focused');
			$('#expand').toggle('fold',{},1200);
			var idarr=$(this).prop('id').split('_');
			var id=idarr[1];
			var url=$('#ajaxURL').val();
			var action=$('#action').val();
			var dataArray=$.parseJSON($('#dataArray').val());
			//$('#expand_panel').html(action+'<br>'+JSON.stringify(dataArray));
					$.ajax
					({
						type: "POST",
						url: url,
						data: { action: action,personID: id, dataString: JSON.stringify(dataArray)},
						success: function(msg)
						{ 
							$('#expand').html(msg);	
						}
					}); 
			
		});
		$('body').on('click','.headSlide',function(){
			$(this).find('td').toggleClass('slided');
			$(this).nextUntil('.headSlide').slideToggle(300);
		});
		$('#ledger_print').click(function(){
			$('#print_year').val($('#year_id').val());
			$('#print_section').val($('#section_name').val());
			var personID_arr=new Array();
			$('.name_select').each(function(){
				if(this.checked){
					var ids=$(this).attr('id');
					var idarr=ids.split("_");
					personID_arr.push(idarr[1]);
				}
			});
		$('#personID_array').val(JSON.stringify(personID_arr));
			$('.form_ledger_print').submit();
		});
	/* Ledger */
})