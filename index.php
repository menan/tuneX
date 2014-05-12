<?php
include ("./functions.php");

$debug = $_GET['debug'];
if (isset($_GET['q'])){
	$search_for = str_replace(" ","-",$_GET['q']) . "-wikipedia";
	$json = search_query($search_for);
	//echol (count($json['responseData']['results']));
	$title = strip_tags($json['responseData']['results'][0]['title']);
	$title = trim_r(substr($title,0,strlen($title) - strlen(strstr($title,"- Wikipedia"))));
	
	$code = read_with_title($title);
	
	echo read_code($code,$debug);

}
else if (isset($_GET['t'])){
	$title = $_GET['t'];
	echo "title:$title<br />";
	$code = read_with_title($title);

	echo read_code($code);
}
?>
