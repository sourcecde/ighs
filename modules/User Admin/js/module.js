/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/
$(document).ready(function(){
	
	//Get person by acc acc_no
	$("#go").click(function(){
		var account_number=$("#account_number").val();
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
					$('#gibbonPersonID[value="' + msg + '"]').prop('selected', true);
 				}
 			}
 			});
	})
	
	//Family Edit
	$("#addGuardian").click(function(){
		if($(this).is(":checked")){
			$(".guardian").show();
			$('#guardianRelationRow').show();
			$('#guardianRelation').prop("required",true);
			$('#officialName3').prop("required",true);
			$('#email3').prop("required",true);
			$('#phone13').prop("required",true);			
		}
		else{
			$(".guardian").hide();
			$('#guardianRelationRow').hide();
			$('#guardianRelation').prop("required",false);
			$('#officialName3').prop("required",false);
			$('#email3').prop("required",false);
			$('#phone13').prop("required",false);	
		}
	});
	$("input[name='contactPriority1']").change(function(){
		var contactpriority=$(this).val();
		if(contactpriority==1){
			$("input[name='contactPriority2'][value='1']").prop("checked",false);
			$("input[name='contactPriority2'][value='2']").prop("checked",true);
			$("input[name='contactPriority3'][value='1']").prop("checked",false);
			$("input[name='contactPriority3'][value='2']").prop("checked",true);
		}
		else{
			$("input[name='contactPriority2'][value='1']").prop("checked",true);
			$("input[name='contactPriority2'][value='2']").prop("checked",false);			
		}
	});
	$("input[name='contactPriority2']").change(function(){
		var contactpriority=$(this).val();
		if(contactpriority==1){
			$("input[name='contactPriority1'][value='1']").prop("checked",false);
			$("input[name='contactPriority1'][value='2']").prop("checked",true);
			$("input[name='contactPriority3'][value='1']").prop("checked",false);
			$("input[name='contactPriority3'][value='2']").prop("checked",true);
		}
		else{
			$("input[name='contactPriority1'][value='1']").prop("checked",true);
			$("input[name='contactPriority1'][value='2']").prop("checked",false);			
		}
	});	
	$("input[name='contactPriority3']").change(function(){
		var contactpriority=$(this).val();
		if(contactpriority==1){
			$("input[name='contactPriority1'][value='1']").prop("checked",false);
			$("input[name='contactPriority1'][value='2']").prop("checked",true);
			$("input[name='contactPriority2'][value='1']").prop("checked",false);
			$("input[name='contactPriority2'][value='2']").prop("checked",true);
		}
		else{
			$("input[name='contactPriority2'][value='1']").prop("checked",true);
			$("input[name='contactPriority2'][value='2']").prop("checked",false);			
		}
	});	
	$("#emergencySubmit").click(function(){
		if($("#emergency1Name").val().length>0 || $("#emergency1Relation").val().length>0){
			$("#emergency1Phone").attr("required",true);
		}
		else{
			$("#emergency1Phone").attr("required",false);
		}
		if($("#emergency2Name").val().length>0 || $("#emergency2Relation").val().length>0){
			$("#emergency2Phone").attr("required",true);
		}
		else{
			$("#emergency2Phone").attr("required",false);
		}
	});
	//Family Edit
	/*User Admin*/
	$('#hasTC').click(function(){
		if($('#hasTC').val()=='Y'){
			$('#tcNumber').prop("disabled",false);
			$('#tcDate').prop("disabled",false);
		}
		else{
			$('#tcNumber').prop("disabled",true);
			$('#tcDate').prop("disabled",true);
		}
	});
	/*User Admin*/
	/*$(".select_student_dropdown").change(function(){
		var studentid=$(this).val();
		var linkurl=$("#check_student_class_url").val();
		if(studentid!='')
			{
			$.ajax
	 		({
	 			type: "POST",
	 			url: linkurl,
	 			data: {action:'getclass',studentid:studentid},
	 			success: function(msg)
	 			{
	 				//$('.select_student_dropdown option[value='+msg+']').attr('selected','selected');
	 				//$(this).attr('selected','selected');
	 				$("#gibbonYearGroupID").get(0).selectedIndex = msg;
	 			}
	 			});
			}
	})*/
	
	$("#account_number").blur(function(){
		var account_number=$(this).val();
		var personid=$('.select_student_dropdown').val();
		
		
		var linkurl=$("#check_accountno_url").val();
		//ajax_check_unique_account_number.php
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {accountno:account_number,personid:personid},
 			success: function(msg)
 			{
 				
 				if(msg>0)
 					{
	 					$("#account_number_error").html("This number already exist!");
	 					$("#account_number_error").show();
	 					$("#account_number_correct").hide();
 					}
 				else
 					{
	 					$("#account_number_error").hide();
	 					$("#account_number_correct").html("Valid Number");
	 					$("#account_number_correct").show();
 					}
 			}
 			});
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
	$('.mClose').click(function(){
		$('#hide_body').hide();
		$('.cModal').hide();
	});
	$('.editRoll').click(function(){
		var enrollID=$(this).attr("id");
		var name=$('#Name_'+enrollID).text();
		var roll=$('#Roll_'+enrollID).text();
		var sectionID=$('#SectionID_'+enrollID).text();
		var yearGroupID=$('#ClassID_'+enrollID).val();
		var className=$('#Class_'+enrollID).text();
		var edate=$('#EDate_'+enrollID).text();
		var account=$('#Account_'+enrollID).text();
		var admission=document.getElementById('Admission_'+enrollID).value;
		var phone=$('#Phone_'+enrollID).text();
		var aadhar=document.getElementById('Aadhar_'+enrollID).value;
		var father=document.getElementById('Father_'+enrollID).value;
		var mother=document.getElementById('Mother_'+enrollID).value;
		var address=document.getElementById('Address_'+enrollID).value;
		var url=$("#rollGroupURL").val();
		var yearID=$("#filterYear").val();
		// var test = document.getElementById('prodId').value;
		// console.log(test);
		$('#e_name').text(name);
		$('#enrollID').val(enrollID);
		$('#edit_name').val(name);
		$('#edit_class').val(className);
		$('#edit_roll').val(roll);
		//$("#edit_section option").hide();
		$("."+yearGroupID).show();
		//$("#edit_section option[value='"+sectionID+"']").attr("selected","selected");
		console.log(sectionID);
		console.log(admission);
		$('#edit_edate').val(edate);
		$('#edit_admission').val(admission);
		$('#edit_account').val(account);
		$('#edit_phone').val(phone);
		$('#edit_aadhar').val(aadhar);
		$('#edit_father').val(father);
		$('#edit_mother').val(mother);
		$('#edit_address').val(address);
		$('#hide_body').show();
		$('#modal_roll_edit').show();
		$.ajax({
			type: "POST",
			url: url,
			data: {yearGroup:yearGroupID,schoolYear:yearID},
			success: function(msg){
				$("#edit_section").empty().append(msg);
				$("#edit_section option[value='"+sectionID+"']").attr("selected","selected");
			}
		});
	});
	$('#editRollNo').click(function(){
		var enrollID=$('#enrollID').val();
		var name=$('#edit_name').val();
		var account=$('#edit_account').val();
		var className=$('#edit_class').val();
		var roll=$('#edit_roll').val();
		var sectionID=$('#edit_section').val();
		var sectionName=$('#edit_section option:selected').text();
		//alert(sectionName);
		var admission=$('#edit_admission').val();
		var updateUrl=$('#updateUrl').val();
		var aadhar = $('#edit_aadhar').val();
		var phone = $('#edit_phone').val();
		var father = $('#edit_father').val();
		var mother = $('#edit_mother').val();
		var address = $('#edit_address').val();
		if(roll!=''){
		$.ajax
 		({
 			type: "POST",
 			url: updateUrl,
 			data: {enrollID:enrollID, roll:roll,section:sectionID,
 					account_no:account,admission:admission,aadhar:aadhar,
 					phone:phone,father:father,mother:mother,address:address},
 			success: function(msg)
 			{
				var msg1=msg.split('_');
				if(msg1[0]==1){
					$('#Account_'+enrollID).text(account);
				}
				$('#Name_'+enrollID).text(name);
				$('#Class_'+enrollID).text(className);
				$('#Section_'+enrollID).text(sectionName);
				$('#Admission_'+enrollID).text(admission);
				$('#Roll_'+enrollID).text(roll);
				$('#hide_body').hide();
				$('.cModal').hide();
				cAlert(msg1[1]);
				console.log(msg);
				//location.reload();
 			}
		});
		}
		else{
			cAlert("ERROR: Roll No. Cannot be Empty!")
		}
	});
	function cAlert(message){
		$('#message').text(message);
		$('#hide_body').show();
		$('#messageAlert').show();
	};
})