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
$("#src_type_print").click(function(){
	var url=$("#print_page_url").val();
	var src_type=$("#search_type").val();
	var start_from=$("#startDateFrom").val();
	var end_to=$("#startDateTo").val();
	var select_class=$("#select_class").val();
	var select_section=$("#select_section").val();
	window.open(url+'?type='+src_type+'&from_date='+start_from+'&todate='+end_to+'&select_class='+select_class+'&select_section='+select_section,null,"'',width=1000,scrollbars=1,status=yes,toolbar=no,menubar=no,location=no");
})

$(".print_receipt").click(function(){
	var url=$("#print_money_receipt_url").val();
	var idarr=($(this).attr("id")).split('_');
	var id=idarr[2];
	window.open(url+'?id='+id,null,"height=400,width=700,status=yes,toolbar=no,menubar=no,location=no");
})

$('.deleteDocx').click(function(){
	var id=$(this).prop('id');
	var linkurl=$('#deleteUrl').val();
	//alert(linkurl);
	var r=confirm("Are you sure you want to delte this attachment?");
	if(r){
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:'delete',id:id},
 			success: function(msg)
 			{
				//console.log(msg);
 				 alert('Deleted Successfully!!');
 				location.reload();
 			}
 			});
	}
	
});
$("#filterClass,#schoolYear").change(function(){
	//alert("Hululu");
	var yearGroup=$("#filterClass").val();
	var schoolYear=$("#schoolYear").val();
	var url=$("#rollgroup_url").val();
	$.ajax({
		type: "POST",
		url: url,
		data: {yearGroup: yearGroup, schoolYear: schoolYear},
		success: function(msg)
		{
			console.log(msg);
			$("#filterSection").empty().append(msg);
		}
	});
});
})