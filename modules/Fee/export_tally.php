<?php
@session_start() ;
$search=NULL;
//Set timezone from session variable
date_default_timezone_set($_SESSION[$guid]["timezone"]);

if (isActionAccessible($guid, $connection2, "/modules/Fee/export_tally.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
?>
		<table width="80%" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					Start Date: <input type="text" name="startDate" id="cal_startDate" value="">
				</td>
				<td>
					End Date: <input type="text" name="endDate" id="cal_endDate">
				</td>
				<td>
					<input type='submit' value='Download' id="DownloadTallyCSV">
				</td>
			</tr>
		</table>
		
		<div id='body'></div>
<?php
}
?>
<script type="text/javascript">
		var exportUrl="<?php print $_SESSION[$guid]["absoluteURL"] . "/modules/Fee/GenerateTallyCSV.php" ?>";
		$(function() {
			$( "#cal_startDate" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$( "#cal_endDate" ).datepicker({ dateFormat: 'dd/mm/yy' });
			$('#DownloadTallyCSV').on('click',function(){
				var startDate=$('#cal_startDate').val();
				var endDate=$('#cal_endDate').val();
				if(startDate.trim().length==0){
					alert("Please enter Start date");
					$('#cal_startDate').focus();
					return false;
				}
				else if(endDate.trim().length==0){
					alert("Please enter End date");
					$('#cal_endDate').focus();
					return false;
				}
				 $.ajax
				({
					type: "POST",
					url: exportUrl,
					data: {startDate:startDate, endDate:endDate},
					success: function(msg){
						var uri='data:text/csv;charset=utf-8,'+escape(msg);
						var link=document.createElement("a");
						link.href=uri;
						link.download="TallyExport"+new Date().toLocaleDateString("en-IN")+".csv";
						link.style="visiblity:none";
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
						//$("#body").append(msg);				
					    //console.log(msg);
					}
				});
			});
		});
</script>