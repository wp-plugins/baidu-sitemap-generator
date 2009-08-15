<?php

/*
Plugin Name:Baidu Sitemap Generator
Plugin URI: http://www.liucheng.name/?p=883
Description: This pulgin generates a Baidu XML-Sitemap for WordPress Blog. | 生成百度 Sitemap XML 文件。就相当于网站被百度--全球最大的中文搜索引擎订阅，进而为您的网站带来潜在的流量。
Author: 柳城博客
Version: 0.8.1
Author URI: http://www.liucheng.name/


*/

//ob_start (); 
/** define the field name of database **/
define('BAIDU_SITEMAP_OPTION','baidu_sitemapoption');


require_once("sitemap-function.php");

/** add a Menu,like "Baidu Sitemap" **/
function baidu_sitemap_menu() {
   /** Add a page to the options section of the website **/
   if (current_user_can('manage_options')) 				
 		add_options_page("Baidu-Sitemap","Baidu-Sitemap", 8, __FILE__, 'baidu_sitemap_optionpage');
}

/** custom message **/
function baidu_sitemap_topbarmessage($msg) {
	 echo '<div class="updated fade" id="message"><p>' . $msg . '</p></div>';
}


/** Baidu sitemap page **/
function baidu_sitemap_form() {
	$get_baidu_sitemap_options = get_option(BAIDU_SITEMAP_OPTION);
	//print_r($get_baidu_sitemap_options);
	if(empty($get_baidu_sitemap_options)){
		global $current_user;
		$lc_blog_url = get_bloginfo('url');
		get_currentuserinfo();
		$lc_admin_email = $current_user->user_email;
		$lc_updatePeri = "24";
		$lc_limits = "100";
	}else{
		list($lc_blog_url,$lc_admin_email,$lc_updatePeri,$lc_limits,$lc_sitemap_auto,$lc_order_1,$lc_order_2,$lc_order_3,$lc_comments,$lc_post_length,$lc_post_cat) = explode("|",$get_baidu_sitemap_options);
	}

	?>
		<div class="postbox-container" style="width:75%;">
			<div class="metabox-holder">	
				<div class="meta-box-sortables">			
						
		<div class="tool-box">
			<h3 class="title"><?php _e('Preferences','baidu_sitemap');?></h3>
			<p><?php _e('百度Sitemap的基本参数配置。以后将会根据需要丰富选项。 每次升级插件后，务必重新更新配置和更新 XML-Sitemap 文件。','baidu_sitemap');?></p>
			<a name="baidu_sitemap_options"></a><form name="baidu_sitemap_options" method="post" action="">
			<input type="hidden" name="action" value="build_options" />
			<table>
				<tr><td><label for="advanced_options"><h3><?php _e('General Options','baidu_sitemap');?></h3></label></td></tr>
				<tr><td><label for="lc_blog_url"><?php _e('Blog Homepage','baidu_sitemap');?></label></td><td><input type="text" size="50" name="lc_blog_url" value="<?php echo $lc_blog_url;?>" /></td><td><a title="<?php _e('最好不要以/结束','baidu_sitemap');?>">[?]</a><td></tr>
				<tr><td><label for="lc_admin_email"><?php _e('Email','baidu_sitemap');?></label></td><td><input type="text" size="50" maxlength="200" name="lc_admin_email" value="<?php echo $lc_admin_email;?>" /></td><td><a title="<?php _e('Baidu will contact you use this Email if necessary','baidu_sitemap');?>">[?]</a><td></tr>
				<tr><td><label for="lc_updatePeri"><?php _e('更新周期(单位：小时)','baidu_sitemap');?></label></td><td><input type="text" size="50" maxlength="200" name="lc_updatePeri"  value="<?php echo $lc_updatePeri;?>" /></td><td><a title="<?php _e('24小时/每天更新一次是比较合适的值。除非你的站每天有大量的文章发表。','baidu_sitemap');?>">[?]</a><td></tr>
				<tr><td><label for="lc_limits"><?php _e('Post Count','baidu_sitemap');?></label></td><td><input type="text" size="50" maxlength="200" name="lc_limits"  value="<?php echo $lc_limits;?>" /></td><td><a title="<?php _e('只需要将最近一个更新周期内新增的和变化了的文章包含在XML页面上，过多的数量只会增加服务器的负担','baidu_sitemap');?>">[?]</a><td></tr>
				<tr><td><label for="lc_sitemap_auto"><?php _e('Auto build the sitemap','baidu_sitemap');?></label></td><td><input type="checkbox" id="lc_sitemap_auto" name="lc_sitemap_auto" value="1" <?php if(empty($get_baidu_sitemap_options) || $lc_sitemap_auto=='1'){ echo 'checked="checked"'; } ?> /></td></tr>
				<?php advanced_options(); ?>
			</table>
			<p class="submit"><input type="submit" class="button-primary" value="<?php if(empty($get_baidu_sitemap_options)){_e('Active the options first','baidu_sitemap');}else{_e('Update options','baidu_sitemap');} ?>" /></p>
			</form>
		</div>


		<div class="tool-box">
			<h3 class="title"><?php _e('Write a XML file','baidu_sitemap');?></h3>
			<p><?php _e('When active the options, you can create a XML file here. or Rebulid the sitemap file after update options or other else.','baidu_sitemap');?></p>
			<p><?php rebuild_message();?></p>
		    <?php if(!empty($get_baidu_sitemap_options)){ ?>
				<a name="baidu_sitemap_build"></a><form name="baidu_sitemap_build" method="post" action="">
				<input type="hidden" name="action" value="build_xml" />
				<p class="submit"><input type="submit" class="button-primary" value="<?php if(file_exists(GetHomePath().'sitemap_baidu.xml')) { _e('Update XML file','baidu_sitemap'); } else { _e('生成XML文件','baidu_sitemap'); } ?>" /></p>
				</form>
			<?php }else{ print '<p>'; _e('There is nothing to do, Please Active the options first.','baidu_sitemap'); print '</p>';} ?>

		</div>
			<?php
			/** show the XML file if exist **/ 
			xml_file_exist();

			/** Show help information **/
			//baidu_sitemap_help(); 	
			?>
		</div>
		</div>
		</div>
     <?php
}


/** Baidu sitemap page **/
function baidu_sitemap_optionpage()
{
      /** Perform any action **/
		if(isset($_POST["action"])) {
			if ($_POST["action"]=='build_options') {update_baidu_sitemap(); }
		    if ($_POST["action"]=='build_xml') { build_baidu_sitemap();}
		}
		
		/** Definition **/
      echo '<div class="wrap"><div style="background: url(http://www.liucheng.name/wp-content/uploads/liucheng_name32.png) no-repeat;" class="icon32"><br /></div>';
		echo '<h2>Baidu Sitemap Generator</h2>';

		/** Introduction **/ 
		echo '<p>'. _e('This pulgin generates a Baidu XML-Sitemap for WordPress Blog.','baidu_sitemap') .'</p>';

		
		/** show the option Form **/ 
		baidu_sitemap_form();
		//test_form();

		/** Show the plugins Author **/
		lc_sidebar();
	

		//echo '</div>';
}

/** update the options **/
function update_baidu_sitemap() {
	if ($_POST['action']=='build_options'){
		$lc_blog_url = $_POST['lc_blog_url'];
		$lc_admin_email = $_POST['lc_admin_email'];
		$lc_updatePeri = $_POST['lc_updatePeri'];
		$lc_limits = $_POST['lc_limits'];
		$lc_sitemap_auto = $_POST['lc_sitemap_auto'];
		if(empty($lc_sitemap_auto)){ $lc_sitemap_auto = '0'; if(function_exists('wp_clear_scheduled_hook')) { wp_clear_scheduled_hook('do_this_auto'); } }
		$lc_order_1 = $_POST['lc_order_1'];
		$lc_order_2 = $_POST['lc_order_2'];
		$lc_order_3 = $_POST['lc_order_3'];
		$lc_comments = $_POST['lc_comments']; if(empty($lc_comments)) { $lc_comments ='0'; }
		$lc_post_length = $_POST['lc_post_length']; if(empty($lc_post_length)) { $lc_post_length ='0'; }
		$lc_post_cat = $_POST['lc_post_cat']; if(empty($lc_post_cat)) { $lc_post_cat ='0'; }
		$baidu_sitemap_options = implode('|',array($lc_blog_url,$lc_admin_email,$lc_updatePeri,$lc_limits,$lc_sitemap_auto,$lc_order_1,$lc_order_2,$lc_order_3,$lc_comments,$lc_post_length,$lc_post_cat));
		update_option(BAIDU_SITEMAP_OPTION,$baidu_sitemap_options); 
        baidu_sitemap_topbarmessage(__('Congratulate, Update options success','baidu_sitemap'));
	}
}


/** build the XML file, sitemap_baidu.xml **/
function build_baidu_sitemap() {
    global $wpdb, $posts, $wp_version;
	$get_baidu_sitemap_options = get_option(BAIDU_SITEMAP_OPTION);
	if(!empty($get_baidu_sitemap_options)){ list($lc_blog_url,$lc_admin_email,$lc_updatePeri,$lc_limits,$lc_sitemap_auto,$lc_order_1,$lc_order_2,$lc_order_3,$lc_comments,$lc_post_length,$lc_post_cat) = explode("|",$get_baidu_sitemap_options); }

	/** Get the current time **/
	$blogtime = current_time('mysql'); 
	list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = split( '([^0-9])', $blogtime );

    /** XML_begin **/
	$xml_begin = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	$xml_begin .= '<document xmlns:bbs="http://www.baidu.com/search/bbs_sitemap.xsd">'."\n";
	$xml_begin .= xml_annotate();
	$xml_begin .= '<webSite>'."$lc_blog_url".'</webSite>'."\n";
	$xml_begin .= '<webMaster>'."$lc_admin_email".'</webMaster>'."\n";
	$xml_begin .= '<updatePeri>'."$lc_updatePeri".'</updatePeri>'."\n";
	$xml_begin .= '<updatetime>'."$today_year-$today_month-$today_day $hour:$minute:$second".'</updatetime>'."\n";
	$xml_begin .= '<version>'."WordPress$wp_version".'</version>'."\n";
    //echo $xml_begin;

	/** get the post title,ID,post_date from database **/
	$sql = "SELECT DISTINCT ID
		FROM $wpdb->posts
		LEFT JOIN $wpdb->comments ON ( $wpdb->posts.ID = $wpdb->comments.comment_post_ID ) 
		WHERE post_password = ''
		AND post_type = 'post'
		AND post_status = 'publish'
		ORDER BY concat($lc_order_1,'-',$lc_order_2,'-',$lc_order_3) DESC 
		LIMIT 0,$lc_limits";
    $recentposts = $wpdb->get_results($sql);
    if($recentposts){
		foreach ($recentposts as $post) {
		   /** Post URL **/
		   $permalink = EscapeXML(stripslashes_deep(get_permalink($post->ID)));

		   /** Post **/
			 $my_post = get_post($post->ID, ARRAY_A);
			 $post_title = EscapeXML(stripslashes_deep($my_post['post_title']));
			 $post_date = $my_post['post_date'];

           /** show the comments info **/
		   if($lc_comments=='1') { 
				 $comment_count = $my_post['comment_count'];
				 $comment_array = get_approved_comments($post->ID);
				 if($comment_array){ 
				     $last_comment = array_pop($comment_array);
				     $my_comment = get_comment($last_comment->comment_ID, ARRAY_A);
				 	 $comment_date = $my_comment['comment_date'];
			      }else { $comment_date = $post_date; }
			 }

           /** show the post_length **/
		   if($lc_post_length=='1') { $post_content_str = strlen($my_post['post_content']); }

          /** show the cat name **/
		  if($lc_post_cat=='1') {
			 $category = get_the_category($post->ID);
				 if(count($category)=='1'){ $my_cat = EscapeXML(stripslashes_deep($category[0]->cat_name)); }
				 if(count($category)=='2'){ $my_cat = EscapeXML(stripslashes_deep($category[0]->cat_name.",".$category[1]->cat_name)); }
				 if(count($category)=='3'){ $my_cat = EscapeXML(stripslashes_deep($category[0]->cat_name.",".$category[1]->cat_name.",".$category[2]->cat_name)); }
				 if(count($category)=='4'){ $my_cat = EscapeXML(stripslashes_deep($category[0]->cat_name.",".$category[1]->cat_name.",".$category[2]->cat_name.",".$category[3]->cat_name)); }
				 if(count($category)=='5'){ $my_cat = EscapeXML(stripslashes_deep($category[0]->cat_name.",".$category[1]->cat_name.",".$category[2]->cat_name.",".$category[3]->cat_name.",".$category[4]->cat_name)); }
		  }

		   $xml_middle = '<item>'."\n";
		   //$xml_middle .= '<link>'."$lc_blog_url".'/?p='."$post_ID".'</link>'."\n";
		   $xml_middle .= '<link>'.$permalink.'</link>'."\n";
		   $xml_middle .= '<title>'."$post_title".'</title>'."\n";
		   $xml_middle .= '<pubDate>'."$post_date".'</pubDate>'."\n";
	       if($lc_comments=='1'){ $xml_middle .= '<bbs:lastDate>'.$comment_date.'</bbs:lastDate>'."\n"; 
		                          $xml_middle .= '<bbs:reply>'.$comment_count.'</bbs:reply>'."\n";
		                         }
	       //if($lc_comments=='1' && $comment_count>='1'){ $xml_middle .= '<bbs:lastDate>'.$comment_date.'</bbs:lastDate>'."\n"; 
		                                                 //$xml_middle .= '<bbs:reply>'.$comment_count.'</bbs:reply>'."\n";
		                                                //}
		   if($lc_post_length=='1'){ $xml_middle .= '<bbs:mainLen>'.$post_content_str.'</bbs:mainLen>'."\n"; }
		   if($lc_post_cat=='1'){  $xml_middle .= '<bbs:boardid>'.$my_cat.'</bbs:boardid>'."\n"; }
		   $xml_middle .= '</item>'."\n";
           $xml_middle_done .= $xml_middle;
		}
	}

    /** XML_end **/
	$xml_end = '</document>';

    /** XML_ALL **/
    $baidu_xml = $xml_begin.$xml_middle_done.$xml_end;

	/** save XML file as sitemap_baidu.xml **/
	$fileName = GetHomePath();
	$filename = $fileName.'sitemap_baidu.xml';
	if(IsFileWritable($filename)){ 
		file_put_contents("$filename","$baidu_xml"); 		
		/** Messages  **/
		baidu_sitemap_topbarmessage(__('Congratulate, Build the XML file success','baidu_sitemap'));
	}else{ 
		/** Messages  **/
		baidu_sitemap_topbarmessage(__('Directory is not writable. please chmod your directory to 777.','baidu_sitemap'));
	}

 //baidu_sitemap_is_auto(); 
 if(function_exists('wp_clear_scheduled_hook')) { wp_clear_scheduled_hook('do_this_auto'); }
}
	

/** Tie the module into Wordpress **/
add_action('admin_menu','baidu_sitemap_menu');
add_action('init','baidu_sitemap_is_auto',1001,0);
/** load the language file **/
add_filter('init','load_baidu_language');

?>