<?php 
function MymainMenu($connection2,$guid)
{
	$toparray=array();
	$subarray=array();
	$combine=array();
	$output="" ;
	
if (isset($_SESSION[$guid]["gibbonRoleIDCurrent"])==FALSE) {
		$output.="<ul id='nav'>" ;
		$output.="<li class='active'><a href='" . $_SESSION[$guid]["absoluteURL"] . "/index.php'>" . _('Home') . "</a></li>" ;
		$output.="</ul>" ;
	}
	else 
	{
		try {
			
			$sql="SELECT DISTINCT(menu_name),id,menu_type,parent_id,url FROM menu where active_inactive=1 GROUP BY menu_type,menu_name ORDER BY order_sequence ASC";
			$result=$connection2->prepare($sql);
			$result->execute();
		}
		catch(PDOException $e) { 
			$output.="<div class='error'>" ;
			$output.=$e->getMessage() ;
			$output.="</div>" ;	
		}
		$output.="<ul id='nav'>" ;
		$dboutbut=$result->fetchAll();
		
		
		
		foreach ($dboutbut as $value) {
			if($value["menu_type"]=="top")
			{
				$toparray[$value["menu_name"]]=$value["id"];
			}
		}
		
		foreach ($toparray as $key=>$value) {
			foreach ($dboutbut as $dbvalue) {
				if($value==$dbvalue["parent_id"])
				{
					//array_push($subarray, $dbvalue["menu_name"]);
					$subarray[$dbvalue["menu_name"]]=$dbvalue["url"];
				}
			}
			if($subarray)
			{
				$combine[$key]=$subarray;
			}
			else 
			{
				$combine[$key]='index.php';
			}
			
			$subarray=array();
		}
		
		
		foreach ($combine as $key=>$value) {
			$output.="<li><a href='".getUrlFromName($key,$dboutbut)."'>".$key."</a>";
			if(is_array($value))
			{
				
				$output.="<ul>" ;
				foreach ($value as $subkey=>$subvalue) {
					$output.="<li><a href='".$_SESSION[$guid]["absoluteURL"]."/index.php?q=".$subvalue."'>".$subkey."</a></li>";
				}
				$output.="</li></ul>" ;
			}
			else 
			{
				$output.="</li>" ;
			}
		}
		
		$output.="</ul>";
		
		return $output;
		
	}
	
}

function getUrlFromName($name,$result)
	{
		$url='';
		foreach ($result as $value) {
			if($value["menu_name"]==$name)
			{
				$url=$value["url"];
				break;
			}
		}	
		return $url;
}

function getRawMenu($connection2,$guid)
{
	$toparray=array();
	$subarray=array();
	$combine=array();
	$output="" ;
try {
			
			$sql="SELECT DISTINCT(menu_name),id,menu_type,parent_id,url FROM menu GROUP BY menu_type,menu_name ORDER BY order_sequence ASC";
			$result=$connection2->prepare($sql);
			$result->execute();
		}
		catch(PDOException $e) { 
			$output.="<div class='error'>" ;
			$output.=$e->getMessage() ;
			$output.="</div>" ;	
		}
		$output.="<ul id='root'>" ;
		$dboutbut=$result->fetchAll();
		
		
		
		foreach ($dboutbut as $value) {
			if($value["menu_type"]=="top")
			{
				$toparray[$value["menu_name"]]=$value["id"];
			}
		}
		
		foreach ($toparray as $key=>$value) {
			foreach ($dboutbut as $dbvalue) {
				if($value==$dbvalue["parent_id"])
				{
					//array_push($subarray, $dbvalue["menu_name"]);
					$subarray[$dbvalue["menu_name"]]=$dbvalue["url"];
				}
			}
			if($subarray)
			{
				$combine[$key]=$subarray;
			}
			else 
			{
				$combine[$key]='index.php';
			}
			
			$subarray=array();
		}
		
		foreach ($combine as $key=>$value) {
			$output.="<li class='parent' id='".$key."' level='top'><a href='".$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/menu/rearrange_menu.php&menu=".$key."'>".$key."</a>";
			if(is_array($value))
			{
				
				$output.="<ul>" ;
				foreach ($value as $subkey=>$subvalue) {
					$output.="<li class='child' id='".$subkey."' level='sub'><a href='".$_SESSION[$guid]["absoluteURL"]."/index.php?q=/modules/menu/rearrange_menu.php&menu=".$subkey."'>".$subkey."</a></li>";
				}
				$output.="</li></ul>" ;
			}
			else 
			{
				$output.="</li>" ;
			}
		}
		
		$output.="</ul>";
		
		return $output;
}


?>
