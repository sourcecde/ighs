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
	$("#print_atendence").click(function(){
		var left=0;
		if($('#left_student').is(":checked"))
			{
			left=1;
			}
		var url=$("#print_page_url").val();
		var src_date=$("#currentDate").val();
		var scctionid=$("#gibbonRollGroupID").val();
		window.open(url+'?src_date='+src_date+'&scctionid='+scctionid+'&left='+left,'mypopup','status=1,width=700,height=500,scrollbars=1');
	})
	/* Print Monthwise Attendance */
		 /*print part*/
		  $('#print_button_MONTHWISE_ATTD').click(function(){
			  $('#print_form').submit();
			});
		  /* print part */
		  /* Section Change Yearwise */
		  $('#gibbonSchoolYearID').change(function(){
			  var yID=$(this).val();
					$.ajax
					({
						type: "POST",
						url: "modules/Attendance/ajax_getSection.php",
						data: { yID:yID},
						success: function(msg)
						{ 
						$("#gibbonRollGroupID").empty().append(msg);	 			
						}
					});
		  });
		  /* Section Change Yearwise */
	/* Print Monthwise Attendance */
	
	
})