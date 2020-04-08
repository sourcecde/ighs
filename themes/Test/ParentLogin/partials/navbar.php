
<div class="navbar navbar-fixed-top">
	
	<div class="navbar-inner">
		
		<div class="container">
			
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="./">
				Calcutta Public School	
				<br><i class="icon-home subheader"></i>
						 <span id='school-address' class='subheader'></span>
				<br><i class="icon-phone subheader"></i>
						 <span id='school-phone' class='subheader'></span>
			</a>
<?php
	if(isset($_SESSION['user'])){
?>
			<div class="nav-collapse">
				<ul class="nav pull-right">
					
					<li class="dropdown">						
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" id='username-nav'>
							<i class="icon-user"></i> 
							<?=$_SESSION['user']['preferredName']?>
							<b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="./changepassword.php"><h3><i class="icon-cog"></i> Change Password</h3></a></li>
							<li><a href="./logout.php"><h3><i class="icon-signout"></i> Logout</h3></a></li>
						</ul>						
					</li>
				</ul>
				
			</div><!--/.nav-collapse -->	
<?php
	}
?>
			
		</div> <!-- /container -->
		
	</div> <!-- /navbar-inner -->
	
</div> <!-- /navbar -->

