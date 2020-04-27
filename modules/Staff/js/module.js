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
	/* Staff View */
	var linkURL=$('#linkURL').val();
		$.ajax
		({
			type: "POST",
			url: linkURL,
			data: {action:'full'},
			success: function(msg)
			{
				$("#records").append(msg);
				$('#myTable').DataTable();
			}
		});
		$('#left').change(function(){
			var linkURL=$('#linkURL').val();
			if($('#left').is(':checked')){
				$.ajax
				({
					type: "POST",
					url: linkURL,
					data: {action:'left'},
					success: function(msg)
					{
						$("#records").empty();
						$("#records").append(msg);
						$('#myTable').DataTable();
					}
				});
			}
			else{
				$.ajax
				({
					type: "POST",
					url: linkURL,
					data: {action:'full'},
					success: function(msg)
					{
						$("#records").empty();
						$("#records").append(msg);
						$('#myTable').DataTable();
					}
				});				
			}
		});
	/* Staff View */
	/* Manage salary Rule */
		var linkurl='modules/Staff/process_salary_rule.php';
	$('.close_rule').click(function(){
		$('#hide_body').hide();
		$('#create_salary_rule').fadeOut();
	})
	$('#add_salary_rule').click(function(){
		$('#hide_body').show();
		$('#create_salary_rule').fadeIn();
		$('#add_rule').val('ADD');
		$('#action').val('add');
	})
	$('#add_rule').click(function(){
		var action=$('#action').val();
		add_update(action);
	})
	$('.edit_rule').click(function(){
		var id_s=$(this).attr("id");
		var id_a=id_s.split('_');
		var id=id_a[1];
		$('#hide_body').show();
		$('#create_salary_rule').fadeIn();
		$('#add_rule').val('UPDATE');
		$('#action').val('update');
		$('#rule_id').val(id);
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:'fetch_data',id:id},
 			success: function(msg)
 			{
				var s=msg.split("_");
				$('#caption').val(s[0]);
				$('#impact option[value="' + s[1] + '"]').prop('selected', true);
 				$('#active option[value="' + s[2] + '"]').prop('selected', true);
 			}
 		});
	})
	function add_update(action){
		var caption=$('#caption').val();
		var impact=$('#impact').val();
		var active=$('#active').val();
		var id=$('#rule_id').val();
		if(caption=='') {
			alert("Please add a caption.");
			return;
		}
		else if(impact=='') {
			alert("Please select a impact.");
			return;
		}
		$.ajax
 		({
 			type: "POST",
 			url: linkurl,
 			data: {action:action, caption:caption, impact:impact, active:active, id:id},
 			success: function(msg)
 			{
				//alert(msg);
				alert(action=='add'?'Added Sucessfully':'Updated Sucessfully');
				location.reload();
				$('#hide_body').hide();
		        $('#create_salary_rule').fadeOut();
 			}
 		});
	}
	$('.delete_rule').click(function(){
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

	/* Manage salary Rule */
	/*create Pay structure */
	var url2='modules/Staff/process_pay_structure.php';
	$('#select_month_year').click(function(){
		$('#create_panel').hide();
		var month=$('#month_f').val();
		var year=$('#year_f').val();
		if(month=='') {
			alert("Select Month");
			$('#month_f').focus();
			return;
		}
		if(year=='') {
			alert("Select Year");
			$('#year_f').focus();
			return;
		}
		$.ajax
 		({
 			type: "POST",
 			url: url2,
 			data: {action:'check_duplicate',month:month, year:year},
 			success: function(msg)
 			{
				if(msg>0) {
					var r= confirm("Entry for selected month of selected year already exist. Do you want to override?");
				if(!r) {
					location.reload();
					return;
				}
					$('#duplicate_entry').val('1');
					$('#month_s').val(month);
					$('#year_s').val(year);
					//console.log(month+"_"+year);
					createPanel(month,year,1);
				}
				else{
					$('#month_s').val(month);
					$('#year_s').val(year);
					createPanel(month,year,0);
				}
 			}
 		});
		
	})
	function fetch_data(month,year){
		$.ajax
 		({
 			type: "POST",
 			url: url2,
 			data: {action:'fetch_data', month:month, year:year},
 			success: function(msg)
 			{
				//console.log(msg);
				if(msg.length==0)
					return;
				var s=msg.split('#');
				$.each(s,function(k,value){
					var r=value.split('@');
					if(r[0]=='0_97')
						$('#percentagePF').val(r[1]);
					else if(r[0]=='0_96')
						$('#percentageESI').val(r[1]);
					else
						$('#'+r[0]).val(r[1]);
					//console.log(r[0]);
					//console.log(r[1]);
				});
 			}
 		});
	};
	function createPanel(month,year,old){
		$.ajax
 		({
 			type: "POST",
 			url: url2,
 			data: {action:'create_table', month:month, year:year},
 			success: function(msg)
 			{
				$('#create_panel').html(msg);
				$('#create_panel').show();
				if(old==1)
					month++;
				console.log(month+'_'+old)
				fetch_data(month,year);
 			}
 		});
	};
	
	/*create Pay structure */
	/* Payment Salary */
		$(".atnd_day").blur(function(){
			var id_s=$(this).attr("id");
			var id_a=id_s.split('#');
			var sid=id_a[0];
			var ad=$(this).val();
					var c_name=sid+'_rule';
					//alert(c_name);
			$('.'+c_name).each(function(){
				var id_s=$(this).attr("id");
				var ov=$('#'+id_s+'_old').val();
				var wd=$('#'+sid+'_working_day').val();
				var nv=Math.round(ov*(ad/wd));
				
				$(this).val(nv);
			});
		})
		$("#form_month_year").submit(function(e){
			var form=$(this);
			var month=$('#month_f').val();
			var year=$('#year_f').val();
			if(month==''){
				e.preventDefault();
				alert("Please Select Month");
				$('#month_f').focus();
				return;
			}
			if(year==''){
				e.preventDefault();
				alert("Please Select Year");
				$('#year_f').focus();
				return;
			}
		})
	/* Payment Salary */
	/* View Pay Structure*/
	var url3='modules/Staff/process_pay_structure_edit.php';
		$('.edit_rule_ps').click(function(){
			$("#hide_body").show();
			$("#edit_panel_ps").fadeIn();
			var id_s=$(this).attr("id");
			var id_a=id_s.split("_");
			var sid=id_a[1];
			var month=id_a[2];
			var year=id_a[3];
			$('#sid_v').val(sid);
			$('#month_v').val(month);
			$('#year_v').val(year);
			$.ajax
			({
				type: "POST",
				url: url3,
				data: {action:'fetch_data', month:month, year:year, sid:sid},
				success: function(msg)
				{
					//alert(msg);
					var data_a=msg.split('#');
					$(data_a).each(function(){
						var temp=this.split('_');
						$("#rule_input_"+temp[0]).val(temp[1]);
					})
				}
			});
		})
		$('.close_panel').click(function(){
			$("#hide_body").fadeOut();
			$(".edit_panel").hide();
		})
		$('#update_ps').click(function(){
			var data="";
			$(".rule_input").each(function(){
				var id=$(this).attr("id");
				var amount=$(this).val();
				data+=id+"_"+amount+"/";
			})
			var sid=$('#sid_v').val();
			var month=$('#month_v').val();
			var year=$('#year_v').val();
			$.ajax
			({
				type: "POST",
				url: url3,
				data: {action:'update_ps', month:month, year:year, sid:sid, data: data},
				success: function(msg)
				{	//alert(msg);
					alert("Updated sucessfully!!");
					location.reload();
				}
			}); 
		})
		$('.delete_rule_ps').click(function(){
			var id_s=$(this).attr("id");
			var id_a=id_s.split("_");
			var sid=id_a[1];
			var month=id_a[2];
			var year=id_a[3];
			$.ajax
			({
				type: "POST",
				url: url3,
				data: {action:'delete_ps', month:month, year:year, sid:sid},
				success: function(msg)
				{	console.log(msg);
					alert("Deleted sucessfully!!");
					location.reload();
				}
			}); 
		})
		$('.button-add').click(function(){
			$("#hide_body").show();
			$("#add_panel_ps").fadeIn();
			var id_s=$(this).attr("id");
			var id_a=id_s.split("_");
			var month=id_a[1];
			var year=id_a[2];
			$('#month_add').val(month);
			$('#year_add').val(year);
		})
		$('#add_ps').click(function(){
			var data="";
			$(".rule_input_a").each(function(){
				var id=$(this).attr("id");
				var amount=$(this).val();
				data+=id+"_"+amount+"/";
			})
			var sid=$('#staff_add').val();
			var month=$('#month_add').val();
			var year=$('#year_add').val();
			//console.log(data);
			$.ajax
			({
				type: "POST",
				url: url3,
				data: {action:'add_ps', month:month, year:year, sid:sid, data: data},
				success: function(msg)
				{	//alert(msg);
					console.log(msg);
					alert("Added sucessfully!!");
					location.reload();
				}
			});
		})
	/* View Pay Structure*/
	/* PF ECR */
	var url4="modules/Staff/pf_ajax.php";
	$('.close_panel').click(function(){
		$('.hide_body').hide();
		$('.close_panel').hide();
		$('#display_panel').hide();
	});
	$('#pf_ecr_view').click(function(){
		var month=$('#month').val();
		var yearID=$('#yearID').val();
			$.ajax
			({
				type: "POST",
				url: url4,
				data: {action:'fetchECRData', month:month, yearID:yearID},
				success: function(msg)
				{	//alert(msg);
					//console.log(msg);
					var data=JSON.parse(msg);
					showPanel(data);
				}
			});
	});
	$('#pf_ecr_csv').click(function(){
		var month=$('#month').val();
		var yearID=$('#yearID').val();
			$.ajax
			({
				type: "POST",
				url: url4,
				data: {action:'fetchECRData', month:month, yearID:yearID},
				success: function(msg)
				{	//alert(msg);
					//console.log(msg);
					$('#dataCSV').val(msg);
					$('#form_csv').submit();
				}
			});
	});	
	$('#pf_ecr_ecr').click(function(){
		var month=$('#month').val();
		var yearID=$('#yearID').val();
			$.ajax
			({
				type: "POST",
				url: url4,
				data: {action:'fetchECRData', month:month, yearID:yearID},
				success: function(msg)
				{	//alert(msg);
					//console.log(msg);
					$('#dataECR').val(msg);
					$('#form_ecr').submit();
				}
			});
	});
	function showPanel(data){
		//console.log(data);
		var output="<table width='100%'><tr><th>UAN<br><small>No.<small></th><th>Member Name</th><th>Gross</th><th>EPF Gross</th><th>EPS Gross</th><th>EDLI Gross</th><th>EPF Contribution<br><small>(EE Share)</small></th><th>EPS Contribution</th><th>Diff EPF and EPS<br><small>Contribution (ER Share)</small></th><th>NCP Days</th><th>Refund of Advances</th>";
		$.each(data,function(k,v){
			output+="<tr>";
			for(var i=0;i<11;i++){
				output+="<td>"+v[i]+"</td>";
			}
			output+="</tr>";
		});
		output+="</table>";
		$('#display_panel').html(output);
		$('.hide_body').show();
		$('.close_panel').show();
		$('#display_panel').show();
		//console.log(output);
	}
	$('.form_popup').submit(function(){
						window.open('', 'formpopup', 'width=600,height=500,resizeable');
						this.target = 'formpopup';
					});
	/* PF ECR */

	/* Manage Employee Contract */
		var linkurlcontract='modules/Staff/process_employee_contract.php';
	$('.close_contract').click(function(){
		$('#hide_body').hide();
		$('#create_employee_contract').fadeOut();
	})
	$('#add_contract_employee').click(function(){
		$('#hide_body').show();
		$('#create_employee_contract').fadeIn();
		$('#add_contract').val('ADD');
		$('#action').val('add');
	})
	$('#add_contract').click(function(){
		var action=$('#action').val();
		add_new_contract(action);
	})
	$('.edit_contract').click(function(){
		var id_s=$(this).attr("id");
		var id_a=id_s.split('_');
		var id=id_a[1];
		$('#hide_body').show();
		$('#create_employee_contract').fadeIn();
		$('#add_contract').val('UPDATE');
		$('#action').val('update');
		$('#contract_id').val(id);
		$.ajax
 		({
 			type: "POST",
 			url: linkurlcontract,
 			data: {action:'fetch_data',id:id},
 			success: function(msg)
 			{
				var s=msg.split("_");
				var ns = s[0].split("-");
				var es = s[1].split("-");
				//alert(ns[0]);
				$('#sdate').val(ns[2]+'/'+ns[1]+'/'+ns[0]);
				$('#edate').val(es[2]+'/'+es[1]+'/'+es[0]);
				//$('#impact option[value="' + s[1] + '"]').prop('selected', true);
 				//$('#active option[value="' + s[2] + '"]').prop('selected', true);
 			}
 		});
	})
	function add_new_contract(action){
		var sdate=$('#sdate').val();
		var edate=$('#edate').val();
		var staff_id=$('#staff_id').val();
		var contract_id=$('#contract_id').val();
		$.ajax
 		({
 			type: "POST",
 			url: linkurlcontract,
 			data: {action:action, sdate:sdate, edate:edate, staff_id:staff_id, contract_id:contract_id},
 			success: function(msg)
 			{
				//alert(msg);
				alert(action=='add'?'Added Sucessfully':'Updated Sucessfully');
				location.reload();
				$('#hide_body').hide();
		        $('#create_employee_contract').fadeOut();
 			}
 		});
	}
	$('.delete_contract').click(function(){
		var id_s=$(this).attr("id");
		var id_a=id_s.split('_');
		var id=id_a[1];
		//alert(id);
		var n =confirm("Are you really want to delete this entry?");
		if(n) {
		$.ajax
 		({
 			type: "POST",
 			url: linkurlcontract,
 			data: {action:'delete',id:id},
 			success: function(msg)
 			{
				alert("Deleted successfully!!");
				location.reload();
 			}
 		});
		}
	})
})