$(document).ready(function(){
    
    $('#confirm-box').hide();
function getSection(y_id,S){
	$.ajax
	({
		type: "POST",
		url: "modules/Messenger/ajaxSMS.php",
		data: { action:'getSection',year_id: y_id},
		success: function(msg)
		{ 
			$("#"+S).empty().append(msg);
		}
	});
}
$('#filter_year').change(function(){
	var y=$(this).val();
	if(y!='')
		getSection(y,'rollGroups');
	else
		$("#rollGroups").empty().append("<option value=''>Loading...</option>");
});
$('.selector_head').click(function(){
	$(this).next('.selector_body').toggleClass('hidden_panel');
	if($(this).attr('id')=='rollwise')
		$('#roles option:selected').removeAttr('selected');
	else if($(this).attr('id')=='groupwise')
		$('#rollGroups option:selected').removeAttr('selected');
	else if($(this).attr('id')=='transportuser'){
		$('#transports option:selected').removeAttr('selected');
		$('#vehicles option:selected').removeAttr('selected');
	}
});
$('#message_body').keyup(function(){
	$('#character_count').html($(this).val().length+ ($(this).val().match(/\n/g)||[]).length);
});
$('.cancel').click(function(){
	location.reload();
});
$('#send_sms').click(function(){
	if($('#subject').val()==''){
		alert('Please enter a Subject!!');
		$('#subject').focus();
		return;
	}
	else if($('#message_body').val()==''){
		alert('Please enter a Message!!');
		$('#message_body').focus();
		return;
	}
	var roles=$('#roles').val();
	var rollGroups=$('#rollGroups').val();
	var transports=$('#transports').val();
	var vehicles=$('#vehicles').val();
	var filter_studentID=$('#filter_studentID').val();
	var filter_staffID=$('#filter_staffID').val();
	var defaulter=$('#defaulter').val();
	var new_admission=$('#new_admission').val();
	$.ajax
	({
		type: "POST",
		url: $('#contact_url').val(),
		data: { action:'fetchContact',roles,rollGroups:rollGroups,transports: transports,vehicles:vehicles,filter_studentID:filter_studentID,filter_staffID:filter_staffID,defaulter:defaulter,new_admission:new_admission},
		success: function(msg)
		{ 
			console.log(msg);
			var data=JSON.parse(msg);
			console.log(data[0]);
			displayContact(data);
			$('#confirm-box').show();
		}
	});
});
function displayContact(data){
	var op="";
	var count=0;
	$.each(data[1],function(k,v){
		op+="<tr><td>"+v['name']+"</td><td>"+v['phone']+"</td></tr>";
		count++;
	});

	$.each(data[0],function(k,v){
		op+="<tr><td>"+v['name']+"</td><td>"+v['phone']+"</td></tr>";
		count++;
	});
	
	$('#contact_data').val(JSON.stringify(data[0]));
	$('#subject_data').val($('#subject').val());
	$('#message_data').val($('#message_body').val());
	$('#message_count').html(count);
	$('#contact_list').html(op);
};
$('.smsLogHeader').click(function(){
	id=$(this).prop('id');
	$.ajax
	({
		type: "POST",
		url: "modules/Messenger/ajaxSMS.php",
		data: { action:'getRecipient',id:id},
		success: function(msg)
		{ 
			$('#DP'+id).html(msg);
		}
	});
	$(this).next('.smsLogDetails').toggleClass('hidden_panel');
});
$('.sort_radio').click(function(){
	var processURL=$('#processURL').val();
	if($('#account').is(':checked')){
		var action="accounts";
	}
	else{
		var action="name";
	}
	$.ajax({
		type: "GET",
		url: processURL,
		data: { action: action},
		success: function(msg)
		{ 
			$('#filter_studentID').empty().append("<option value=''>Select Student</option>"+msg)
		}
	});
});
$('.year_radio').click(function(){
	var processURL=$('#processURL').val();
	if($('#upcoming').is(':checked')){
		var action="Upcoming";
	}
	else{
		var action="Current";
	}
	$.ajax({
		type: "GET",
		url: processURL,
		data: { action: action},
		success: function(msg)
		{ 
			$('#new_admission').empty().append("<option value=''>Select Student</option>"+msg)
		}
	});
});
});

$(document).on('click',"#discard_pw",function(){
		if($(".select:checked").length >= 1){
		var chkArray = [];
		var linkurl=$("#linkurl").val();
		//alert($("#gibbonPersonIDsms").val());
		$(".select:checked").each(function() {
		chkArray.push($(this).val());
		});
		var selected;
		selected = chkArray.join(',') ;
		debugger;
		$.ajax({
			type: "POST",
 			url: linkurl,
 			data: {action:'discard_sms',P_ids:selected},
 			success: function(msg)
 			{
 				 alert(msg);
				 console.log(msg);
				 location.reload(); 
 			}
		});
		}
		else{
			alert("Please select at least one of the checkbox");
		}
	});