<?php
@session_start() ;

if (isActionAccessible($guid, $connection2, "/modules/Fee/missingFee.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
	$sql="SELECT * FROM `gibbonschoolyear` WHERE `status`='Current' OR `status`='Upcoming'";
	$result=$connection2->prepare($sql);
	$result->execute();
	$year=$result->fetchAll();
?>
<h3>This operation may take longer time. Do you want to prooced?</h3>
<select id='yearID'>
<?php
foreach($year as $y){
	$s='';
	if($y['status']=='Current'){
		$s='selected';
	}
	print "<option value='".$y['gibbonSchoolYearID']."'>".$y['name']."</option>";
}
?>
</select>
<input type='button' id='missingFee' value='OK' style='Color: white; background: #ff731b; border: none;'>
<h3 style='text-align: center;' id='message'> </h3>
<?php	
echo "<div id='loading' style='display:none; position:fixed; width:100%;height:100%; top:0px; left:0px;'>";
	echo "
			<div id='loading'>
				<h2 style='text-align: center;'>Please wait......</h2>
                <ul class='bokeh'>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
		";
echo "</div>";
}
?>
<input type='hidden' name='ajaxURL' id='ajaxURL' value='<?=$_SESSION[$guid]["absoluteURL"] ."/modules/" . $_SESSION[$guid]["module"] . "/processMissingFee.php"?>'>
<script>
$('#missingFee').click(function(){
	$('#loading').fadeIn();
	var url=$('#ajaxURL').val();
	var yearID=$("#yearID").val();
	$.ajax({
		type: "POST",
		 url: url,
		 data: {action: 'postMissingFee',yearID: yearID},
		 success: function(msg){
			 $('#loading').fadeOut();
			 $('#message').html(msg);
		 }
	});
});
</script>