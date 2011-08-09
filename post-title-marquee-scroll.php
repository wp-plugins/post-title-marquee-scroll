<?php

/*
Plugin Name: Post title marquee scroll
Description: Post title marquee scroll is a simple wordpress plugin to create the marquee scroll in the website with post title. In the admin we have option to choose the category and display order. We can add this plugin directly in the theme files. Also we have widget and short code option.
Author: Gopi.R
Version: 2.0
Plugin URI: http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/
Author URI: http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/
Donate link: http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/
*/

function ptmsshow()
{
	global $wpdb;
	
	@$ptms_scrollamount = get_option('ptms_scrollamount');
	@$ptms_scrolldelay = get_option('ptms_scrolldelay');
	@$ptms_direction = get_option('ptms_direction');
	@$ptms_style = get_option('ptms_style');
	
	@$ptms_noofpost = get_option('ptms_noofpost');
	@$ptms_categories = get_option('ptms_categories');
	@$ptms_orderbys = get_option('ptms_orderbys');
	@$ptms_order = get_option('ptms_order');
	@$ptms_spliter = get_option('ptms_spliter');
	
	if(!is_numeric($ptms_scrollamount)){ $ptms_scrollamount = 2; } 
	if(!is_numeric($ptms_scrolldelay)){ $ptms_scrolldelay = 5; } 
	if(!is_numeric($ptms_noofpost)){ $ptms_noofpost = 10; }
	
	$sSql = query_posts('cat='.$ptms_categories.'&orderby='.$ptms_orderbys.'&order='.$ptms_order.'&showposts='.$ptms_noofpost);
	
	if ( ! empty($sSql) ) 
	{
		@$count = 0;
		foreach ( $sSql as $sSql ) 
		{
			@$title = stripslashes($sSql->post_title);
			@$link = get_permalink($sSql->ID);
			if($count > 0)
			{
				@$spliter = $ptms_spliter;
			}
			@$ptms = @$ptms . @$spliter . "<a href='".$link."'>" . @$title . "</a>";
			
			$count = $count + 1;
		}
	}
	$ptms_marquee = $ptms_marquee . "<div style='padding:3px;' class='ptms_marquee'>";
	$ptms_marquee = $ptms_marquee . "<marquee style='$ptms_style' scrollamount='$ptms_scrollamount' scrolldelay='$ptms_scrolldelay' direction='$ptms_direction' onmouseover='this.stop()' onmouseout='this.start()'>";
	$ptms_marquee = $ptms_marquee . $ptms;
	$ptms_marquee = $ptms_marquee . "</marquee>";
	$ptms_marquee = $ptms_marquee . "</div>";
	echo $ptms_marquee;	
}

add_filter('the_content','ptms_show_filter');

function ptms_show_filter($content)
{
	return 	preg_replace_callback('/\[POST-MARQUEE(.*?)\]/sim','ptms_show_filter_callback',$content);
}

function ptms_show_filter_callback($matches) 
{
	
	global $wpdb;
	
	@$ptms_scrollamount = get_option('ptms_scrollamount');
	@$ptms_scrolldelay = get_option('ptms_scrolldelay');
	@$ptms_direction = get_option('ptms_direction');
	@$ptms_style = get_option('ptms_style');
	
	@$ptms_noofpost = get_option('ptms_noofpost');
	@$ptms_categories = get_option('ptms_categories');
	@$ptms_orderbys = get_option('ptms_orderbys');
	@$ptms_order = get_option('ptms_order');
	@$ptms_spliter = get_option('ptms_spliter');
	
	if(!is_numeric($ptms_scrollamount)){ $ptms_scrollamount = 2; } 
	if(!is_numeric($ptms_scrolldelay)){ $ptms_scrolldelay = 5; } 
	if(!is_numeric($ptms_noofpost)){ $ptms_noofpost = 10; }
	
	//$sSql = query_posts('cat='.$ptms_categories.'&orderby='.$ptms_orderbys.'&order='.$ptms_order.'&showposts='.$ptms_noofpost);
	 
	$sSqlMin = "select p.ID, p.post_title, wpr.object_id, ". $wpdb->prefix . "terms.name , ". $wpdb->prefix . "terms.term_id ";
	$sSqlMin = $sSqlMin . "from ". $wpdb->prefix . "terms ";
	$sSqlMin = $sSqlMin . "inner join ". $wpdb->prefix . "term_taxonomy on ". $wpdb->prefix . "terms.term_id = ". $wpdb->prefix . "term_taxonomy.term_id ";
	$sSqlMin = $sSqlMin . "inner join ". $wpdb->prefix . "term_relationships wpr on wpr.term_taxonomy_id = ". $wpdb->prefix . "term_taxonomy.term_taxonomy_id ";
	$sSqlMin = $sSqlMin . "inner join ". $wpdb->prefix . "posts p on p.ID = wpr.object_id ";
	$sSqlMin = $sSqlMin . "where taxonomy= 'category' and p.post_type = 'post' and p.post_status = 'publish'";
	//$sSqlMin = $sSqlMin . "order by object_id; ";
	
	if( ! empty($ptms_categories) )
	{
		$sSqlMin = $sSqlMin . " and ". $wpdb->prefix . "terms.term_id in($ptms_categories)";
	}
	
	if( ! empty($ptms_orderbys) )
	{
		
		if($ptms_orderbys <> "rand" )
		{
			$sSqlMin = $sSqlMin . " order by p.$ptms_orderbys";
			
			if( ! empty($ptms_order) )
			{
				$sSqlMin = $sSqlMin . " $ptms_order";
			}
		}
		else
		{
			$sSqlMin = $sSqlMin . " order by rand()";
		}
		
	}
	
	if( ! empty($ptms_noofpost) )
	{
		$sSqlMin = $sSqlMin . " limit 0, $ptms_noofpost";
	}
	
	//echo $sSqlMin;
	
	$sSql = $wpdb->get_results($sSqlMin);
	
	if ( ! empty($sSql) ) 
	{
		@$count = 0;
		foreach ( $sSql as $sSql ) 
		{
			@$title = stripslashes($sSql->post_title);
			@$link = get_permalink($sSql->ID);
			if($count > 0)
			{
				@$spliter = $ptms_spliter;
			}
			@$ptms = @$ptms . @$spliter . "<a href='".$link."'>" . @$title . "</a>";
			
			$count = $count + 1;
		}
	}
	$ptms_marquee = $ptms_marquee . "<div style='padding:3px;' class='ptms_marquee'>";
	$ptms_marquee = $ptms_marquee . "<marquee style='$ptms_style' scrollamount='$ptms_scrollamount' scrolldelay='$ptms_scrolldelay' direction='$ptms_direction' onmouseover='this.stop()' onmouseout='this.start()'>";
	$ptms_marquee = $ptms_marquee . $ptms;
	$ptms_marquee = $ptms_marquee . "</marquee>";
	$ptms_marquee = $ptms_marquee . "</div>";
	return $ptms_marquee;	
}


function ptms_install() 
{
	add_option('ptms_title', "Post title marquee scroll");
	
	add_option('ptms_scrollamount', "2");
	add_option('ptms_scrolldelay', "5");
	add_option('ptms_direction', "left");
	add_option('ptms_style', "color:#FF0000;font:Arial;");

	add_option('ptms_noofpost', "10");
	add_option('ptms_categories', "");
	add_option('ptms_orderbys', "ID");
	add_option('ptms_order', "DESC");
	add_option('ptms_spliter', " - ");
}

function ptms_widget($args) 
{
	extract($args);
	if(get_option('ptms_title') <> "")
	{
		echo $before_widget;
		echo $before_title;
		echo get_option('ptms_title');
		echo $after_title;
	}
	ptmsshow();
	if(get_option('ptms_title') <> "")
	{
		echo $after_widget;
	}
}
	
function ptms_control() 
{
	echo "Post title marquee scroll";
	echo "<br>";
	echo "<a href='http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/' target='_blank'>Check official website for live demo</a>";
	echo "<br>";
}

function ptms_widget_init()
{
  	register_sidebar_widget(__('Post title marquee scroll'), 'ptms_widget');   
	
	if(function_exists('register_sidebar_widget')) 
	{
		register_sidebar_widget('Post title marquee scroll', 'ptms_widget');
	}
	
	if(function_exists('register_widget_control')) 
	{
		register_widget_control(array('Post title marquee scroll', 'widgets'), 'ptms_control');
	} 
}

function ptms_deactivation() 
{

}

function ptms_option() 
{
	global $wpdb;
	echo '<h2>Post title marquee scroll</h2>';
	
	$ptms_title = get_option('ptms_title');
	
	$ptms_scrollamount = get_option('ptms_scrollamount');
	$ptms_scrolldelay = get_option('ptms_scrolldelay');
	$ptms_direction = get_option('ptms_direction');
	$ptms_style = get_option('ptms_style');
	
	$ptms_noofpost = get_option('ptms_noofpost');
	$ptms_categories = get_option('ptms_categories');
	$ptms_orderbys = get_option('ptms_orderbys');
	$ptms_order = get_option('ptms_order');
	$ptms_spliter = get_option('ptms_spliter');
	
	if ($_POST['ptms_submit']) 
	{
		$ptms_title = stripslashes($_POST['ptms_title']);
		
		$ptms_scrollamount = stripslashes($_POST['ptms_scrollamount']);
		$ptms_scrolldelay = stripslashes($_POST['ptms_scrolldelay']);
		$ptms_direction = stripslashes($_POST['ptms_direction']);
		$ptms_style = stripslashes($_POST['ptms_style']);
		
		$ptms_noofpost = stripslashes($_POST['ptms_noofpost']);
		$ptms_categories = stripslashes($_POST['ptms_categories']);
		$ptms_orderbys = stripslashes($_POST['ptms_orderbys']);
		$ptms_order = stripslashes($_POST['ptms_order']);
		$ptms_spliter = stripslashes($_POST['ptms_spliter']);
		
		update_option('ptms_title', $ptms_title );
		
		update_option('ptms_scrollamount', $ptms_scrollamount );
		update_option('ptms_scrolldelay', $ptms_scrolldelay );
		update_option('ptms_direction', $ptms_direction );
		update_option('ptms_style', $ptms_style );
		
		update_option('ptms_noofpost', $ptms_noofpost );
		update_option('ptms_categories', $ptms_categories );
		update_option('ptms_orderbys', $ptms_orderbys );
		update_option('ptms_order', $ptms_order );
		update_option('ptms_spliter', $ptms_spliter );
	}
	
	echo '<form name="ptms_form" method="post" action="">';
	
	echo '<p>Title :<br><input  style="width: 250px;" type="text" value="';
	echo $ptms_title . '" name="ptms_title" id="ptms_title" /></p>';
	
	echo '<p>Scroll amount :<br><input  style="width: 100px;" type="text" value="';
	echo $ptms_scrollamount . '" name="ptms_scrollamount" id="ptms_scrollamount" /></p>';
	
	echo '<p>Scroll delay :<br><input  style="width: 100px;" type="text" value="';
	echo $ptms_scrolldelay . '" name="ptms_scrolldelay" id="ptms_scrolldelay" /></p>';
	
	echo '<p>Scroll direction :<br><input  style="width: 100px;" type="text" value="';
	echo $ptms_direction . '" name="ptms_direction" id="ptms_direction" /> (Left/Right)</p>';
	
	echo '<p>Scroll style :<br><input  style="width: 250px;" type="text" value="';
	echo $ptms_style . '" name="ptms_style" id="ptms_style" /></p>';
	
	echo '<p>Spliter :<br><input  style="width: 100px;" type="text" value="';
	echo $ptms_spliter . '" name="ptms_spliter" id="ptms_spliter" /></p>';
	
	echo '<p>Number of post :<br><input  style="width: 100px;" type="text" value="';
	echo $ptms_noofpost . '" name="ptms_noofpost" id="ptms_noofpost" /></p>';
	
	echo '<p>Post categories :<br><input  style="width: 200px;" type="text" value="';
	echo $ptms_categories . '" name="ptms_categories" id="ptms_categories" /> (Example: 1, 3, 4) <br> Category IDs, separated by commas.</p>';
	
	echo '<p>Post orderbys :<br><input  style="width: 200px;" type="text" value="';
	echo $ptms_orderbys . '" name="ptms_orderbys" id="ptms_orderbys" /> (Any 1 from below list) <br> ID/author/title/rand/date/category/modified</p>';
	
	echo '<p>Post order : <br><input  style="width: 100px;" type="text" value="';
	echo $ptms_order . '" name="ptms_order" id="ptms_order" /> ASC/DESC </p>';
	
	echo '<input name="ptms_submit" id="ptms_submit" lang="publish" class="button-primary" value="Update" type="Submit" />';
	echo '</form>';
	?>
    <h2>Plugin configuration help</h2>
    <ul>
    	<li><a href="http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/" target="_blank">Drag and drop the widget</a></li>
        <li><a href="http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/" target="_blank">Short code for posts and pages</a></li>
        <li><a href="http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/" target="_blank">Add directly in the theme</a></li>
    </ul>
    <h2>Check official website</h2>
    <ul>
    	<li><a href="http://www.gopiplus.com/work/2011/08/08/post-title-marquee-scroll-wordpress-plugin/" target="_blank">Check official website for live demo</a></li>
    </ul>
    <?php
}

function ptms_add_to_menu() 
{
	add_options_page('Post title marquee scroll', 'Post title marquee scroll', 'manage_options', __FILE__, 'ptms_option' );
}

add_action('admin_menu', 'ptms_add_to_menu');
add_action("plugins_loaded", "ptms_widget_init");
register_activation_hook(__FILE__, 'ptms_install');
register_deactivation_hook(__FILE__, 'ptms_deactivation');
add_action('init', 'ptms_widget_init');
?>