<?php
if($_POST){
	echo $_POST['print_page'];
?>
<script>
window.print();
</script>
<?php	
}
?>
<style>
table tr {
	border: 1px solid;	-webkit-column-break-inside: avoid;          page-break-inside: avoid;               break-inside: avoid;
}
.rightA{
	text-align: right;
}
.footerT td{
	font-weight: bold;
}
table {
	border:1px solid #000000;
}table th{	padding: 1px 5px;	font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 12px; }table td{	padding: 1px 5px;	font-size:12px; color:#000000;}
#short-table th{
	border-width: 0 .5px 1px 0;border-style: solid; 
}
#short-table td{
	border-bottom:.5px solid #000000; 	border-right:.5px solid #000000; 
}thead {display: table-header-group;}
#details-table tr{	padding: 1px 5px; border-width:.5px;border-style: solid; font-family:Arial, Helvetica, sans-serif; font-weight: bold;font-size: 12px; }#details-table thead tr th{	border-width: 1px 0; border-style: solid;}.border-bottom{	border-bottom:1px solid #000000; }</style>