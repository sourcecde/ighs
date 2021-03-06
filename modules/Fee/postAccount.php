<?php
@session_start() ;
if (isActionAccessible($guid, $connection2, "/modules/Fee/postAccount.php")==FALSE) {
	//Acess denied
	print "<div class='error'>" ;
		print _("You do not have access to this action.") ;
	print "</div>" ;
}
else {
$query="SELECT * FROM `gibbonschoolyear` ORDER BY `sequenceNumber`";
$result=$connection2->prepare($query);
$result->execute();
$yDataArr=$result->fetchAll();
//$from_date=DateConverterIndianFormat($_SESSION[$guid]['gibbonSchoolYearFirstDay']);
//$to_date=date('d/m/Y');
?>
<input type='hidden' id='postURL' value='<?=$_SESSION[$guid]["absoluteURL"] . "/modules/" . $_SESSION[$guid]["module"] ."/processPostAccount.php" ?>'>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td><b>Select Year :</b>
	<select name="year" id="year">
	<?php foreach($yDataArr as $y){
		$selected=$y['status']=='Current'?"selected":"";
		echo "<option value='{$y['gibbonSchoolYearID']}' $selected >{$y['name']}</option>";
	}
	?>
	</select>
	</td>
	<td>
		<input type='button' id='postAccountData' value='POST'>
	</td>
</tr>
</table>

<div id='msg'></div>

<div id='hideBody' style='position: fixed; top:0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); display: none;'>
	<div id="cssload-pgloading">
		<div class="cssload-loadingwrap">
			<ul class="cssload-bokeh">
				<li></li>
				<li></li>
				<li></li>
				<li></li>
			</ul>
		</div>
	</div>
</div>
<?php

}
function DateConverter($date)
{
	$datearr=explode("/", $date);
	$systemdate=$datearr[2].'-'.$datearr[1].'-'.$datearr[0];
	return $systemdate;
}

function DateConverterIndianFormat($date)
{
	$datearr=explode("-", $date);
	$systemdate=$datearr[2].'/'.$datearr[1].'/'.$datearr[0];
	return $systemdate;
}

?>
<script type="text/javascript">
	$(function() {
		$( "#from_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$( "#to_date" ).datepicker({ dateFormat: 'dd/mm/yy' });
		$('#postAccountData').click(function(){
			if(confirm("Are you Sure?")){
				$('#hideBody').show();
				var posturl=$('#postURL').val();
				var yearID=$('#year').val();
				$.ajax
				({
					type: "POST",
					url: posturl,
					data: {yearID: yearID},
					success: function(msg)
					{
						 console.log(msg);
						 $('#msg').html(msg);
						 $('#hideBody').hide();
					}
				});
				
			}
		});
	});
</script>

<style>
#cssload-pgloading {}

#cssload-pgloading:after {
		content: "";
		z-index: -1;
		position: absolute;
		top: 0; right: 0; bottom: 0; left: 0;
}
#cssload-pgloading .cssload-loadingwrap {position:absolute;top:45%;bottom:45%;left:25%;right:25%;}
#cssload-pgloading .cssload-bokeh {
		font-size: 97px;
		width: 1em;
		height: 1em;
		position: relative;
		margin: 0 auto;
		list-style: none;
		padding:0;
		border-radius: 50%;
		-o-border-radius: 50%;
		-ms-border-radius: 50%;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
}

#cssload-pgloading .cssload-bokeh li {
		position: absolute;
		width: .2em;
		height: .2em;
		border-radius: 50%;
		-o-border-radius: 50%;
		-ms-border-radius: 50%;
		-webkit-border-radius: 50%;
		-moz-border-radius: 50%;
}

#cssload-pgloading .cssload-bokeh li:nth-child(1) {
		left: 50%;
		top: 0;
		margin: 0 0 0 -.1em;
		background: rgb(0,193,118);
		transform-origin: 50% 250%;
		-o-transform-origin: 50% 250%;
		-ms-transform-origin: 50% 250%;
		-webkit-transform-origin: 50% 250%;
		-moz-transform-origin: 50% 250%;
		animation: 
				cssload-rota 1.3s linear infinite,
				cssload-opa 4.22s ease-in-out infinite alternate;
		-o-animation: 
				cssload-rota 1.3s linear infinite,
				cssload-opa 4.22s ease-in-out infinite alternate;
		-ms-animation: 
				cssload-rota 1.3s linear infinite,
				cssload-opa 4.22s ease-in-out infinite alternate;
		-webkit-animation: 
				cssload-rota 1.3s linear infinite,
				cssload-opa 4.22s ease-in-out infinite alternate;
		-moz-animation: 
				cssload-rota 1.3s linear infinite,
				cssload-opa 4.22s ease-in-out infinite alternate;
}

#cssload-pgloading .cssload-bokeh li:nth-child(2) {
		top: 50%; 
		right: 0;
		margin: -.1em 0 0 0;
		background: rgb(255,0,60);
		transform-origin: -150% 50%;
		-o-transform-origin: -150% 50%;
		-ms-transform-origin: -150% 50%;
		-webkit-transform-origin: -150% 50%;
		-moz-transform-origin: -150% 50%;
		animation: 
				cssload-rota 2.14s linear infinite,
				cssload-opa 4.93s ease-in-out infinite alternate;
		-o-animation: 
				cssload-rota 2.14s linear infinite,
				cssload-opa 4.93s ease-in-out infinite alternate;
		-ms-animation: 
				cssload-rota 2.14s linear infinite,
				cssload-opa 4.93s ease-in-out infinite alternate;
		-webkit-animation: 
				cssload-rota 2.14s linear infinite,
				cssload-opa 4.93s ease-in-out infinite alternate;
		-moz-animation: 
				cssload-rota 2.14s linear infinite,
				cssload-opa 4.93s ease-in-out infinite alternate;
}

#cssload-pgloading .cssload-bokeh li:nth-child(3) {
		left: 50%; 
		bottom: 0;
		margin: 0 0 0 -.1em;
		background: rgb(250,190,40);
		transform-origin: 50% -150%;
		-o-transform-origin: 50% -150%;
		-ms-transform-origin: 50% -150%;
		-webkit-transform-origin: 50% -150%;
		-moz-transform-origin: 50% -150%;
		animation: 
				cssload-rota 1.67s linear infinite,
				cssload-opa 5.89s ease-in-out infinite alternate;
		-o-animation: 
				cssload-rota 1.67s linear infinite,
				cssload-opa 5.89s ease-in-out infinite alternate;
		-ms-animation: 
				cssload-rota 1.67s linear infinite,
				cssload-opa 5.89s ease-in-out infinite alternate;
		-webkit-animation: 
				cssload-rota 1.67s linear infinite,
				cssload-opa 5.89s ease-in-out infinite alternate;
		-moz-animation: 
				cssload-rota 1.67s linear infinite,
				cssload-opa 5.89s ease-in-out infinite alternate;
}

#cssload-pgloading .cssload-bokeh li:nth-child(4) {
		top: 50%; 
		left: 0;
		margin: -.1em 0 0 0;
		background: rgb(136,193,0);
		transform-origin: 250% 50%;
		-o-transform-origin: 250% 50%;
		-ms-transform-origin: 250% 50%;
		-webkit-transform-origin: 250% 50%;
		-moz-transform-origin: 250% 50%;
		animation: 
				cssload-rota 1.98s linear infinite,
				cssload-opa 6.04s ease-in-out infinite alternate;
		-o-animation: 
				cssload-rota 1.98s linear infinite,
				cssload-opa 6.04s ease-in-out infinite alternate;
		-ms-animation: 
				cssload-rota 1.98s linear infinite,
				cssload-opa 6.04s ease-in-out infinite alternate;
		-webkit-animation: 
				cssload-rota 1.98s linear infinite,
				cssload-opa 6.04s ease-in-out infinite alternate;
		-moz-animation: 
				cssload-rota 1.98s linear infinite,
				cssload-opa 6.04s ease-in-out infinite alternate;
}







@keyframes cssload-rota {
		from { }
		to { transform: rotate(360deg); }
}

@-o-keyframes cssload-rota {
		from { }
		to { -o-transform: rotate(360deg); }
}

@-ms-keyframes cssload-rota {
		from { }
		to { -ms-transform: rotate(360deg); }
}

@-webkit-keyframes cssload-rota {
		from { }
		to { -webkit-transform: rotate(360deg); }
}

@-moz-keyframes cssload-rota {
		from { }
		to { -moz-transform: rotate(360deg); }
}

@keyframes cssload-opa {
		0% { }
		12.0% { opacity: 0.80; }
		19.5% { opacity: 0.88; }
		37.2% { opacity: 0.64; }
		40.5% { opacity: 0.52; }
		52.7% { opacity: 0.69; }
		60.2% { opacity: 0.60; }
		66.6% { opacity: 0.52; }
		70.0% { opacity: 0.63; }
		79.9% { opacity: 0.60; }
		84.2% { opacity: 0.75; }
		91.0% { opacity: 0.87; }
}

@-o-keyframes cssload-opa {
		0% { }
		12.0% { opacity: 0.80; }
		19.5% { opacity: 0.88; }
		37.2% { opacity: 0.64; }
		40.5% { opacity: 0.52; }
		52.7% { opacity: 0.69; }
		60.2% { opacity: 0.60; }
		66.6% { opacity: 0.52; }
		70.0% { opacity: 0.63; }
		79.9% { opacity: 0.60; }
		84.2% { opacity: 0.75; }
		91.0% { opacity: 0.87; }
}

@-ms-keyframes cssload-opa {
		0% { }
		12.0% { opacity: 0.80; }
		19.5% { opacity: 0.88; }
		37.2% { opacity: 0.64; }
		40.5% { opacity: 0.52; }
		52.7% { opacity: 0.69; }
		60.2% { opacity: 0.60; }
		66.6% { opacity: 0.52; }
		70.0% { opacity: 0.63; }
		79.9% { opacity: 0.60; }
		84.2% { opacity: 0.75; }
		91.0% { opacity: 0.87; }
}

@-webkit-keyframes cssload-opa {
		0% { }
		12.0% { opacity: 0.80; }
		19.5% { opacity: 0.88; }
		37.2% { opacity: 0.64; }
		40.5% { opacity: 0.52; }
		52.7% { opacity: 0.69; }
		60.2% { opacity: 0.60; }
		66.6% { opacity: 0.52; }
		70.0% { opacity: 0.63; }
		79.9% { opacity: 0.60; }
		84.2% { opacity: 0.75; }
		91.0% { opacity: 0.87; }
}

@-moz-keyframes cssload-opa {
		0% { }
		12.0% { opacity: 0.80; }
		19.5% { opacity: 0.88; }
		37.2% { opacity: 0.64; }
		40.5% { opacity: 0.52; }
		52.7% { opacity: 0.69; }
		60.2% { opacity: 0.60; }
		66.6% { opacity: 0.52; }
		70.0% { opacity: 0.63; }
		79.9% { opacity: 0.60; }
		84.2% { opacity: 0.75; }
		91.0% { opacity: 0.87; }
}
</style>