<?php

function filter_comments($str){
	//echo "Trying to find comments in <b>$str</b><br/>";
	while (strpos($str,"&amp;lt;!--")){
		//echo "start found at:" . strpos($str,"!--") . ", and end found at:".strpos($str,"--",strpos($str,"!--"));
		$str = substr($str,0,strpos($str,"&amp;lt;!--"));
	}
	
	return trim_r($str);
}
function fitler_equals($str){
	$return_str = substr($str,strpos($str,"=")+1);
	return $return_str;
}
function fetch_tag($html,$field){
	$error_str = "<font color=\"red\">error</font>";
	if ($field == "Released"){
		$limit = 200;	
	}
	else{
		$limit = 500;
	}
	//$field .= "&nbsp;";
	$code_curr = strchr($html,$field);
	
	$key = "<br />";
	/*echo "<b>$code_curr</b><br />";
	if (strpos($code_curr,$key)){
		echo "found <b>$key</b> in $code_curr <br />";
	}
	
	*/
	if(strlen(filter_string(substr($code_curr,strlen($field),strpos($code_curr,$key)-strlen($field)))) > $limit)
		return $error_str;
	else
		$return_str = filter_string(substr($code_curr,strlen($field),strpos($code_curr,$key)-strlen($field)));
	
	
	
	if (strpos($return_str,"="))
		return $error_str;
	else
		return html_entity_decode($return_str);
	
}

function trim_r($str){
	return strip_tags(trim(str_replace("&nbsp;"," ",$str)));
}
function filter_brackets($str){
	//echol("filtering braces:$str");
	
	
	while (strpos($str,"(")){
		$str= substr_replace($str,"",strpos($str,"("),strpos($str,")")-strpos($str,"(")+1);
	}
	
	return $str;
}
function filter_square_brackets($str){
	$str = str_replace("[[","",$str);
	$str = str_replace("]]","",$str);
	//echo "<br />sqr brc:$str<br />";
	return $str;
}

function filter_curely_brackets($str){
		if (strpos($str,"}}")){
			if (strpos($str,"|")){
			$str = substr($str,strpos($str,"|")+1);
			$str = str_replace("|","-",$str);
		}
		else{
		}
	}
	$str = str_replace("{{","",$str);
	$str = str_replace("}}","",$str);
	
	return $str;
}
function filter_tag($str,$tag){
	
	if ($tag != "br" && $tag != "br/" && $tag != "br&nbsp;/"){

		$look_for = "&amp;lt;$tag";
		$look_for_end = "&amp;lt;/$tag&amp;gt;";
		$str_to_replace = substr($str,strpos($str,$look_for),strrpos($str,$look_for_end));
		
		//echo "replaceing <b>$str_to_replace</b> from $str <br />";
		$str = str_replace($str_to_replace,"",$str);
	}
	
	else{
		$look_for = "&amp;lt;$tag&amp;gt;";
		//echo "replaceing &amp;lt;$tag&amp;gt; <b>$str_to_replace</b> from $str <br />";

		$str = str_replace($look_for,"",$str);
	}
	return $str;
}


function filter_OR($str){
	if (strpos($str,"|")){
		$str = substr($str,strpos($str,"|")+1);
	}
	else{
	}

		return $str;
}
function fetch_required($str){
	$new_str = "";
	if (strpos($str,",")){
		$str_arr = explode(",",$str);
		
		foreach($str_arr as $value){
			if (strpos($value,"|")){
				$new_str.= substr($value,strpos($value,"|")+1) . ", ";
			}
			else{
				$new_str .= "$value, ";
			}
		}
		return substr($new_str,0,strlen($new_str)-2);
	}
	else{
		if (strpos($str,"|")){
				$str= substr($str,strpos($str,"|")+1);
			}
			else{
			}
		return $str;
	}
}
function filter_all_tags($str){
	$str = filter_tag($str,"small");
	$str = filter_tag($str,"ref");
	$str = filter_tag($str,"br");
	$str = filter_tag($str,"br&nbsp;/");
	$str = filter_tag($str,"br/");
	
	return $str;
}
function filter_featuring($str){
	if (strpos($str,"featuring")){
		$str_arr = explode("featuring",$str);
		
		foreach($str_arr as $value){
			if (strpos($value,"|")){
				$new_str.= substr($value,strpos($value,"|")+1) . "featuring ";
			}
			else{
				$new_str .= "$value featuring ";
			}
		}
		return substr($new_str,0,strlen($new_str)-strlen("featuring "));
	}
	return $str;
}
function fetch_album_art($file_name){
	$file_name = trim($file_name);
	//$file_name = str_replace(" ","_",$file_name);
	//echo "checking file $file_name<br />";
	$file_name=urlencode($file_name);
	//echo "fetching album art with the filename: <b>$file_name</b><br />";
	
	if ($file_name != ""){
		$url = "http://en.wikipedia.org/w/api.php?action=query&titles=File:$file_name&prop=imageinfo&iiprop=url&format=xml";
		
		$xmlDocNew = new DOMDocument();
		$xmlDocNew->load($url);
		$xmlThis =  $xmlDocNew->saveXML();	
		$params = $xmlDocNew->getElementsByTagName('ii');
		if ($params->length == 0)
			return false;
		else
			return $params->item(0)->getAttribute('url');
	}
	else{
		return $file_name;
	}
}

function filter_results($str){
	$str = html_entity_decode($str);
	$replace_str = substr($str,strpos($str,"[[")+2,strpos($str,"]]")-3);
	if (strpos($replace_str,"|")){
		$title = addslashes(substr($replace_str,0,strpos($replace_str,"|")));
		
		$name = substr($replace_str,strpos($replace_str,"|")+1);
		$str_replace = "<a href=\"javascript:load_results('$title')\" onclick=\"load_results('$title')\">$name</a>";
	}
	else{
		$str_replace = "<a href=\"javascript:load_results('$replace_str')\" onclick=\"load_results('$replace_str')\">$replace_str</a>";
	}
	
	$str = str_replace("[[$replace_str]]",$str_replace,$str);
		
	return $str;
}

function read_with_title($title){
	$title = rawurlencode($title);
	$xmlDoc = new DOMDocument();
	$xmlDoc->load("http://en.wikipedia.org/w/api.php?action=query&prop=revisions&titles=$title&rvprop=content&format=xml");
	$xml =  $xmlDoc->saveXML();
	$xml = highlight_string($xml,true);
	return $xml;

}

function search_query($query){
	$url = "https://ajax.googleapis.com/ajax/services/search/web?v=1.0&"
	    . "q=$query&key=ABQIAAAAgK4BvrcdX4oiyQll5Lju3xTo5YWHrGd3Pm5KQGsK9TRJV7MPARQqzEbRos4Jl9FuG-dgQyHTDoxfQA&userip=USERS-IP-ADDRESS";
	
	// sendRequest
	// note how referer is set manually
	ini_set('user_agent','Firefox');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER,"http://tinrit.com");
	$body = curl_exec($ch);
	curl_close($ch);
	
	// now, process the JSON string
	return json_decode($body,true);
}

function read_code($master_code,$error){
	$return_str = "<div id=\"right_side\"><p class=\"description\">	";
	if (!strchr($master_code,"{{Infobox")){
		
		$return_str .=  "There are multiple results found:<br />";
		$results = explode("*",$master_code);
		$i = 0;
		foreach($results as $value){
			$i++;
			if ($i > 1 && $i <= 11){
				$j = $i - 1;
				$return_str .= "$j. " . filter_results($value) . "<br />";
			}
		
		}
		
	}
	else{
	
		$info_code = strchr($master_code,"{{Infobox");
		$type = filter_comments(substr($info_code,strlen("{{Infobox"),strcspn($info_code,"|")-strlen("{{Infobox")));
		$code = strchr($info_code,"Name");
		
		$type = strtolower($type);
		$type = trim($type);
		if ($type != "single" && $type != "song"){
			$return_str .= "This is not a song, its a <b>$type</b>, be more specific please";
		}
		else{
		
			$song_title = fetch_tag($code,"Name");
			$album_art_name = fetch_tag($code,"Cover");
			$artist = fetch_tag($code,"Artist");
			$album = fetch_tag($code,"Album");
			$release_date = fetch_tag($code,"Released");
			$genre = fetch_tag($code,"Genre");
			$label = fetch_tag($code,"Label");
			$writers = fetch_tag($code,"Writer");
			$producers = fetch_tag($code,"Producer");
		
								
			//$return_str .= "Title Type:       <span class='info'>$type</span><br />";
			$return_str .=  "Title Name:       <span class='info'>$song_title</span><br />";
			$return_str .=   "Artist:       <span class='info'>$artist</span><br />";
			$return_str .=   "Album:       <span class='info'>$album</span><br />";
			$return_str .=   "Release Date:       <span class='info'>$release_date</span><br />";
			$return_str .=   "Genre:       <span class='info'>$genre</span><br />";
			$return_str .=   "Label:       <span class='info'>$label</span><br />";
			$return_str .=   "Writer(s):       <span class='info'>$writers</span><br />";
			$return_str .=   "Producer(s):       <span class='info'>$producers</span><br />";
			$return_str .=  "</p></div>";
								
			if ($album_art_name != ""){
				//fetch_album_art($album_art_name);
				$album_art = fetch_album_art($album_art_name);
				if ($album_art != false)
				$return_str .=  "<div id=\"left_side\"><h2>Album Art:</h2><a href='$album_art' target='_blank'><img src=\"" . $album_art . "\" style=\"width:200px;\"/></a><br /></div>";
				
				
			}
		}
	
	}
	if ($error == 0){
		echo "</div>";
	}
	else{
		echo "</div>$master_code<hr />";
	}
	return $return_str;	
}
function echol($str){
	echo "<b>$str</b><br />";
}
function filter_string($str){
	//$str = filter_brackets($str);
	$str = filter_all_tags($str);
	$str = filter_comments($str);
	$str = fitler_equals($str);
	$str = filter_square_brackets($str);
	$str = filter_curely_brackets($str);
	$str = filter_featuring($str);
	$str = fetch_required($str);
	return $str;
}
?>