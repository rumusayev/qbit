<?php 
$content = strip_tags($content_data->content);
$content_arr = preg_split('/'.$request->parameters['s'].'/i', $content);

foreach ($content_arr as $key=>$content_part)
{
	$content_middle = substr($content_part, 15, -15);	
	$content_arr[$key] = str_replace($content_middle, '...', $content_part);
}

$content = implode('<span style="background-color: #FFFF00">'.$request->parameters['s'].'</span>', $content_arr); 

echo '<div><a href="'.Backstage::gi()->portal_url.'pages/getPage?p='.$content_data->page_name.'">'.$content.'</a></div>';
?>