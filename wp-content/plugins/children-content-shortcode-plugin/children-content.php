<?php
/*
Plugin Name: Children Content Shortcode
Plugin URI: http://MyWebsiteAdvisor.com/tools/wordpress-plugins/children-content-shortcode/
Description: Shortcode creates an index-like list of child pages displaying content of each page
Version: 1.2
Author: MyWebsiteAdvisor
Author URI: http://MyWebsiteAdvisor.com
*/

/*
Children Content Shortcode (Wordpress Plugin)
Copyright (C) 2011 MyWebsiteAdvisor.com
Contact me at http://MyWebsiteAdvisor.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


//tell wordpress to register the children-content shortcode
add_shortcode("children-content", "sc_show_children_content");


function sc_show_children_content($atts, $content = null){
		
	if(isset($atts['id']) && is_numeric($atts['id'])){
		//id specified by shortcode attribute
		$parent_id = $atts['id'];
	}else{
		//get the id of the current article that is calling the shortcode
		$parent_id = get_the_ID();
	}
	
	$output = "";
	
	//$aPages = array();
	$i = 0;
	
	if ( $children = get_children(array(
		'post_parent' => $parent_id,
		'post_type' => 'page')))
	{
		foreach( $children as $child ) {
			$title = $child->post_title;
			//$content = $child->post_content;
			$content = apply_filters('the_content', $child->post_content);
			$link = get_permalink($child->ID);
			//$link = get_permalink($child->ID);	
 			$output .= "<div>";
			$output .= "<a href='$link'><h1>$title</h1></a>";
			$output .= "<p>". $content ."</p>";
			$output .= "</div>";
			$output .= "<hr>";
			
			//$output .= '<li><a href="'.$link.'" >'.$title.'</a></li>' . "\n";
		}
	} 
	//return  $output;
	return $output;

}

?>