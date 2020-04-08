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
	$('.close_panel').click(function(){
		$('#hide_body').fadeOut();
		$('.edit_panel').hide();
	})
	/* View Staff Attendance*/
	$(".box").each(function(){
		if($(this).prop('cellIndex')==6){
			$(this).css("border-color","red");
			$(this).find('h2').css("color","#DC143C");
		}
	});
	/* View Staff Attendance*/
	/* View Staff Attendance Daywise*/
	$('.edit_attendance').click(function(){
		var id=$(this).prop('id');
		$('#logID').val(id);
		$('#name_e').text($('#name_'+id).text());
		//$('#type_e').val($('#type_'+id).text());
		var t=$('#type_'+id).val();
		//console.log('test'+t);
		$("#type_e option[value='"+t+"']").attr("selected","selected");
		$('#reason_e').val($('#r_'+id).text());
		
		$('#hide_body').show();
		$('.edit_panel').fadeIn();
	})
	$('#update_a_d').click(function(){
		$('#hide_body').fadeOut();
		$('.edit_panel').hide();
		var id=$('#logID').val();
		var type=$('#type_e').val();
		var reason=$('#reason_e').val();
		var linkurl="modules/Staff Attendance/process_ajax.php";
			$.ajax
			({
				type: "POST",
				url: linkurl,
				data: {action:'update_a_d',id:id, type:type, reason:reason},
				success: function(msg)
				{	console.log('Test:'+msg);
					//alert('Test:'+msg);
					alert("Updated sucessfully!!");
					location.reload();
				}
			}); 
	})
	/* View Staff Attendance Daywise*/
	/* View Staff Attendance Individual*/
	$('.box_i').click(function(){
		var arr=$.parseJSON($(this).find('small').html());
		$('#hide_body').show();
		$('#details_panel').fadeIn();
		$('#d_i_t').html($(this).find('p').html());
		$('#d_i_r').html(arr[1]);
		$('#d_i_tb').html(arr[2]);
		$('#d_i_ti').html(arr[3]);
	})
	/* View Staff Attendance Individual*/
	/* Manage Leave Rule */
		var linkurl='modules/Staff Attendance/process_ajax.php';
	$('#add_leave_rule').click(function(){
		$('#hide_body').show();
		$('#create_leave_rule').fadeIn();
		$('#submit_rule').val('ADD');
		$('#action').val('add');
	})
	$('#submit_rule').click(function(){
		var action=$('#action').val();
		add_update(action);
	})
	$('.edit_leave_rule').click(function(){
		var id_s=$(this).attr("id");
		var id_a=id_s.split('_');
		var id=id_a[1];
		$('#hide_body').show();
		$('#create_leave_rule').fadeIn();
		$('#add_rule').val('UPDATE');
		$('#action').val('update');
		$('#rule_id').val(id);
		var sn=$('#'+id+'_sn').text();
		var caption=$('#'+id+'_caption').html();
		$('#short_name').val($.trim(sn))
		$('#caption').val($.trim(caption))
		console.log(sn);
		console.log(caption);
	})
	function add_update(action){
		var caption=$('#caption').val();
		var short_name=$('#short_name').val();
		var id=$('#rule_id').val();
		if(short_name=='') {
			alert("Please add a short name.");
			$('#short_name').focus();
			return;
		}
		else if(caption=='') {
			alert("Please add a caption.");
			$('#caption').focus();
			return;
		}
		
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:action, caption:caption, short_name:short_name, id:id},
 			success: function(msg)
 			{
				//alert(msg);
				alert(action=='add'?'Added Sucessfully':'Updated Sucessfully');
				location.reload();
				$('#hide_body').hide();
		        $('#create_leave_rule').fadeOut();
 			}
 		});
	}
	$('.delete_leave_rule').click(function(){
		var id_s=$(this).attr("id");
		var id_a=id_s.split('_');
		var id=id_a[1];
		var n =confirm("Are you really want to delete this entry?");
		if(n) {
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:'delete',id:id},
 			success: function(msg)
 			{
				alert("Deleted successfully!!");
				location.reload();
 			}
 		});
		}
	})
	
	/* Manage Leave Rule */
	/* View Credited Leave*/
	var linkurl='modules/Staff Attendance/process_ajax.php';
	$('.leave_box').blur(function(){
		var id=$(this).prop('id');
		var value=$(this).text();
		var r=confirm("Do You Really want to update?");
		if(r){
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:'leave_update',id:id,value:value},
 			success: function(msg)
 			{
				console.log(msg);
				//alert("Updated successfully!!");
				//location.reload();
 			}
 		});
		}
		else
			location.reload();
	})
	/* View Credited Leave*/
})