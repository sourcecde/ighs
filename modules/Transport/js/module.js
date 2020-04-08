$(document).ready(function(){
	$("#add_spot_price_link").click(function(){
		
	})
	$("#checkall").click(function(){
		if (this.checked)
			{
				$(".student_list_chk").each(function(){
					this.checked=true;
				})
			}
		else
			{
			$(".student_list_chk").each(function(){
				this.checked=false;
			})
			}
	})
	
	$("#add_monthly_entry").click(function(){
		var linkurl=$("#process_url").val();
		var month=$("#month_name").val();
		var year=$("#year_name").val();
		if(year=='')
		{
			alert("Please select year");
			$("#year_name").focus();
			return;
		}
		var personidarr=[];
		$(".student_list_chk").each(function(){
			if($(this).attr("checked"))
			{
				personidarr.push($(this).val());
			}
		})
		
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:'monthlyentryprocess',enrollid:personidarr,month:month,year:year},
 			success: function(msg)
 			{
 				//console.log(msg);
 				alert("Successfully Entered!");
				//alert(msg);
 			}
 			});		
	})
	
			$("#print_monthly_entry").click(function(){
			var s=$("#print_data").val();
			var url="modules/Transport/monthly_entry_list_print.php?action=print&sql="+s;
			window.open(url,"popupWindow", "width=600,height=600,scrollbars=yes");
		}) 
		$("#print_student_list").click(function(){
			var s=$("#print_data").val();
			var url="modules/Transport/student_list_print.php?action=print&sql="+s;
			window.open(url,"popupWindow", "width=600,height=600,scrollbars=yes");
		})
		$("#print_student_list_contact").click(function(){
			var s=$("#print_data").val();
			var url="modules/Transport/student_list_contact_print.php?action=print&sql="+s;
			window.open(url,"popupWindow", "width=600,height=600,scrollbars=yes");
		})
		
		
		$(".v_edit").click(function(){
			var id=$(this).attr("id");
			var type=$("#type_"+id).val();
			var route=$("#route_"+id).val();
			$('#v_type_e option').each(function(){
								  if ($(this).text() == type)
									$(this).attr("selected","selected");
								});
			$('#route_id_e option').each(function(){
								  if ($(this).text() == route)
									$(this).attr("selected","selected");
								});
			$('#v_dtls_e').val($("#dtls_"+id).val());
			$('#v_id_e').val(id);
			$('#hide_body').show();
			$('#modal_v_edit').fadeIn();
		})
		$("#v_update").click(function(){
			var id=$('#v_id_e').val();
			var type=$('#v_type_e').val();
			var dtls=$("#v_dtls_e").val();
			var route=$("#route_id_e").val();
			
			var url=$("#edit_url").val();
		if(dtls!=null) {	
		$.ajax
 		({
 			type: "POST",
 			url: url,
 			data: {action:'edit',id:id,type:type,dtls:dtls,route:route},
 			success: function(msg)
 			{
				
				$('.modal_v').hide();
				$('.hide_body').fadeOut();
				alert("Edited Sucessfully");
				location.reload();
 			}
 			});	
		}
		})
		
		$('#add_vehicle').click(function(){
			$('#hide_body').show();
			$('#modal_v').fadeIn();
		})
		$('.v_close').click(function(){
			$('.modal_v').hide();
			$('#hide_body').fadeOut();
		})
		
		$("#v_add").click(function(){
			var v_dtls=$('#v_dtls').val();
			var v_type=$('#v_type').val();
			var route=$('#route_id').val();
			var url=$("#edit_url").val();
		if(v_dtls!=null && route!='') {	
		$.ajax
 		({
 			type: "POST",
 			url: url,
 			data: {action:'add',type:v_type,dtls:v_dtls,route:route},
 			success: function(msg)
 			{
				
				$('#modal_v').hide();
				$('#hide_body').fadeOut();
				//alert(msg);
				alert("Added Sucessfully");
				location.reload();
 			}
 			});	
		}
		}) 
	/* Manage Drop Location */
	var url_drop_location=$('#process_url').val();
	$('.drop_location_edit').click(function(){
		var id=$(this).attr('id');
		
		var location=$('#location_'+id).html();
		var distance=$('#distance_'+id).html();
		$('#location_edit').val(location);
		$('#distance_edit').val(distance);
		$('#drop_location_update_id').val(id);
		$('#hide_body').show();
		$('#modal_v_edit').fadeIn();
	});
	$('#drop_location_update').click(function(){
		var location1=$('#location_edit').val();
		var distance=$('#distance_edit').val();
		var id=$('#drop_location_update_id').val();
		$.ajax
		({
			type: "POST",
 			url: url_drop_location,
 			data: {action:'edit',id:id,location:location1,distance},
 			success: function(msg)
 			{
				
				$('#modal_v_edit').hide();
				$('#hide_body').fadeOut();
				alert("Updated Sucessfully!!");
				location.reload();
 			}
		});
	});
	$('#add_drop_location').click(function(){
		$('#hide_body').show();
		$('#modal_v_add').fadeIn();
	});
	$('#add_route').click(function(){
		$('#modal_r').fadeIn();
	});
	$('.r_close').click(function(){
		$('.modal_r').fadeOut();
	});
	$('#r_add').click(function(){
		var route=$("#route").val();
		var vehicle_id=$("#vehicle_id").val();
		var route_process_url=$("#route_process_url").val();
		//alert(route_process_url);
		$.ajax
		({
			type: "POST",
 			url: route_process_url,
 			data: {action:'add',route:route,vehicle_id:vehicle_id},
 			success: function(msg)
 			{
				console.log(msg);
				$('#modal_r').fadeOut();
				alert("Added Sucessfully!!");
				location.reload();
 			}
		});
	});
	$('.r_edit').click(function(){
		$('#modal_r_edit').fadeIn();
		var id=$(this).attr("id").split("_");
		$("#r_id_e").val(id[0]);
		$("#route_e").val(id[1]);
		$("#vehicle_id_e option[value="+id[2]+"]").attr('selected','selected');
	});
	$('#r_update').click(function(){
		var route=$("#route_e").val();
		var id=$("#r_id_e").val();
		var vehicle_id=$("#vehicle_id_e").val();
		var route_process_url=$("#r_edit_url").val();
		//alert(route_process_url);
		$.ajax
		({
			type: "POST",
 			url: route_process_url,
 			data: {action:'edit',id:id,vehicle_id:vehicle_id,route:route},
 			success: function(msg)
 			{
				console.log(msg);
				$('#modal_r_edit').fadeOut();
				alert("Edited Sucessfully!!");
				location.reload();
 			}
		});
	});
	$('#drop_location_add').click(function(){
		var location1=$('#location_add').val();
		var distance=$('#distance_add').val();
		if(location1=='')
			return;
		$.ajax
		({
			type: "POST",
 			url: url_drop_location,
 			data: {action:'add',location:location1,distance},
 			success: function(msg)
 			{
				console.log(msg);
				$('#modal_v_add').hide();
				$('#hide_body').fadeOut();
				alert("Added Sucessfully!!");
				location.reload();
 			}
		});
	});
	/* Manage Drop Location */
	/* Manage Drop Location Price*/
	var url_location_price=$('#process_url').val();
	$(document).on('click','.spot_price_edit',function(){
		var ids=$(this).attr('id');
		var id_arr=ids.split('_');
		var id=id_arr[1];
		$('#location_price_update_id').val(id);
		$('#price_edit').val($('#v_'+id).val());
		$('#hide_body').show();
		$('#modal_v_edit').fadeIn();
	});
	$(document).on('click','.spot_price_delete',function(){
		var ids=$(this).attr('id');
		var id_arr=ids.split('_');
		var id=id_arr[1];
		var c=confirm("Do you really want to delete this record ?");
		if(!c)
			return;
		$.ajax
		({
			type:"POST",
			url:url_location_price,
			data:{action:'delete',id:id},
			success:function(){
				alert("Deleted Sucessfully!!");
				location.reload();
			}
		});
	});
	$('#location_price_update').click(function(){
		var price=$('#price_edit').val();
		var id=$('#location_price_update_id').val();
		$.ajax
		({
			type:"POST",
			url:url_location_price,
			data:{action:'edit',id:id,price:price},
			success:function(){
				$('#modal_v_edit').hide();
				$('#hide_body').fadeOut();
				alert("Updated Sucessfully!!");
				location.reload();
			}
		});
	});
	/* Manage Drop Location Price*/	
	$('#search_by_acc').click(function(){
		var account_number=$("#account_number").val();
		var checkurl="modules/Transport/ajax_get_enrollemnt_id_by_personid.php";
		//alert(account_number);
		$.ajax
 		({
 			type: "POST",
 			url: checkurl,
 			data: {action:'enrollID', account_number:account_number},
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
 					//$('#student_enrollid option[value="' + msg + '"]').prop('selected', true);
 					$('#src_student option[value="' + msg + '"]').prop('selected', true);
					}
			}
		}); 
	})
	$('#search_by_acc_pID').click(function(){
		var account_number=$("#account_number").val();
		var checkurl="modules/Transport/ajax_get_enrollemnt_id_by_personid.php";
		//alert(account_number);
		$.ajax
 		({
 			type: "POST",
 			url: checkurl,
 			data: {action:'personID', account_number:account_number},
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
 					$('#student_id option[value="' + msg + '"]').prop('selected', true);
					$('#src_student option[value="' + msg + '"]').prop('selected', true);
					}
			}
		}); 
	})
})