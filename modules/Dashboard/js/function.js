/* 
Contains all the function called for rendering dashboard elements.
@Nazmul
*/
var url=$('#ajax_url').val();
	function getStudentNo(year){
			$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getStudentNO',year_id:year},
	 			success: function(msg)
	 			{
	 				var data=JSON.parse(msg);
					//console.log(JSON.stringify(data[1]));
					//console.log(msg);
					var chart = new CanvasJS.Chart("studentNoContainer",
					{
						title:{
							text: "Total Student: "+data[0],
							fontSize: 16,
							verticalAlign: 'top',
							horizontalAlign: 'left'
						},
								animationEnabled: true,
						data: [
						{        
							type: "doughnut",
							startAngle:20,
							toolTipContent: "{label} => {y}",
							indexLabel: "{label}",
							dataPoints: data[1]
						}
						]
					});
					chart.render();
				}		
	 			});
	}
	function getTodaysCollection(year,date){
			$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getTodaysCollection',year_id:year,date:date},
	 			success: function(msg)
	 			{
					var data=JSON.parse(msg);
					//console.log(msg);
					var chart = new CanvasJS.Chart("todaysCollectionContainer",
					{
						title:{
							text: "Collection of "+date+": "+data[0]+".00",
							fontSize: 16,
							fontFamily: "arial black"
						},
								animationEnabled: true,
						legend: {
							verticalAlign: "bottom",
							horizontalAlign: "center"
						},
						theme: "theme1",
						data: [
						{        
							type: "pie",
							indexLabelFontFamily: "Garamond",       
							indexLabelFontSize: 20,
							indexLabelFontWeight: "bold",
							startAngle:0,
							indexLabelFontColor: "MistyRose",       
							indexLabelLineColor: "darkgrey", 
							indexLabelPlacement: "inside", 
							toolTipContent: "{name}: {y}.00/-",
							showInLegend: true,
							indexLabel: "{y}.00", 
							dataPoints: data[1]
						}
						]
					});
					chart.render();
				}
			});
	}
	function getTodaysAttendance(year,date){
			$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getTodaysAttendance',year_id:year,date:date},
	 			success: function(msg)
	 			{
					//console.log(msg);
					var data=JSON.parse(msg);
					  var chart = new CanvasJS.Chart("todaysAttendanceContainer",
						{
						  title:{
						  text: "Attendance of "+data[1] ,
						  fontSize: 16
						  },
						  axisY:{
							title:"Student"   
						  },
						  animationEnabled: true,
						  data: [
						  {        
							type: "stackedColumn",
							toolTipContent: "{label}<br/><span style='\"'color: {color};'\"'><strong>{name}</strong></span>: {y}",
							name: "Present",
							showInLegend: "true",
							dataPoints: data[0]['In']
						  },  {        
							type: "stackedColumn",
							toolTipContent: "{label}<br/><span style='\"'color: {color};'\"'><strong>{name}</strong></span>: {y}",
							name: "Absent",
							showInLegend: "true",
							dataPoints: data[0]['Out']
						  }            
						  ]
						  ,
						  legend:{
							cursor:"pointer",
							itemclick: function(e) {
							  if (typeof (e.dataSeries.visible) ===  "undefined" || e.dataSeries.visible) {
								  e.dataSeries.visible = false;
							  }
							  else
							  {
								e.dataSeries.visible = true;
							  }
							  chart.render();
							}
						  }
						});

						chart.render();
				}		
			});
	}
	function getPaymentHistory(year,month){
			$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getPaymentHistory',month:month,year:year},
	 			success: function(msg)
	 			{
					//console.log(msg);
					var data=JSON.parse(msg);
					    var chart = new CanvasJS.Chart("paymentHistoryContainer",
						{      
						  theme:"theme2",
						  title:{
							text: "Collection History: "+data[0]
						  },
						  animationEnabled: true,
						  axisY :{
							includeZero: false,
							valueFormatString: "#",
							suffix: ""
							
						  },
						  toolTip: {
							shared: "false"
						  },
						  data: [
						  {        
							type: "spline", 
							showInLegend: true,
							name: "Rupees. ",
							toolTipContent: "{label}<br>Rs: <b>{y}.00</b>",
							//markerSize: 10,        
							 color: "#e74c3c",
							dataPoints: data[1]
						  } 
						  ],
						  legend:{
							cursor:"pointer",
							itemclick : function(e) {
							  if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
								e.dataSeries.visible = false;
							  }
							  else {
								e.dataSeries.visible = true;
							  }
							  chart.render();
									}
									
								  },
								});

							chart.render();
				}
			});
	}
	
	function getStaff(){
			$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getStaff'},
	 			success: function(msg)
	 			{
					$('#staff_no').text(msg);
				}
			});
	}
	function getFeeCount(date){
		$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getFeeCount',date:date},
	 			success: function(msg)
	 			{
					$('#fee_count').text(msg);
					$('#fee_paid_date').html(date);
					//console.log(msg);
				}
			});
	}
	function getTransportUser(year){
		$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getTransportUser', year_id:year},
	 			success: function(msg)
	 			{
					$('#transport_user').text(msg);
					//console.log(msg);
				}
			});
	}
	function getPendingApplication(year){
		$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getPendingApplication', year_id:year},
	 			success: function(msg)
	 			{
					$('#pending_application').text(msg);
					//console.log(msg);
				}
			});
	}
	function getBirthday(){
		$.ajax
	 		({
	 			type: "POST",
	 			url: url,
	 			data: {action:'getBirthday'},
	 			success: function(msg)
	 			{
					$('#birth_day').html(msg);
					//console.log(msg);
				}
			});
	}