<?php
/*!-------- include utils ---------------------------!*/
require_once("utils.php");
/*!-------- start-up Xenforo -----------------------!*/
require("./XenForoSDK.php");
$xf = new XenForoSDK(dirname(__FILE__) . '/../forum');   /* globally scoped by plugin scripts */
//custom proxy Xenforo scripts
require_once("XenForo_FrontC.php");
require_once("HtmlPublicC.php");
/*!-------- start-up Wordpress ----------------------!*/
//wordpress api
require_once('WPContent/wp-blog-header.php');
?>
<?php
global $current_user;     //wordpress user
$xfuser = $xf->getUser(); //xenforo user
/*!-------- integrate xenforo + wordpress logins -----!*/
if (!is_user_logged_in() && $xf->isLoggedIn()) {
	if (username_exists($xfuser["username"])) {
		//Xenforo username already exists in the Wordpress database so just login Xenforo user.
		$wpuser = get_user_by('login', $xfuser['username']);
		if (!is_wp_error($wpuser)) 
		{
			wp_clear_auth_cookie();
			wp_set_current_user($wpuser->ID);
			wp_set_auth_cookie($wpuser->ID);
		}
	}
	else
	//Xenforo username doesn't exist in Wordpress database so just insert user into database.
	{
		$user_data = array();
		$user_data["user_login"]    = $xfuser["username"];
		$user_data["user_pass"]     = md5(uniqid(rand(), true));
		$user_data["user_email"]    = $xfuser["email"];
		wp_insert_user($user_data);
	}
} else if (is_user_logged_in() && !$xf->isLoggedIn()) {
	//If user is logged in Wordpress but not in Xenforo, logout user from Wordpress.
	//Should work in theory, but this logs you out when you're
	//in the admin panel.
	//wp_logout();
}
 

/*!-------- build content / logic ---------------------------!*/
$newParams = array();
$content  = '<link rel="stylesheet" type="text/css" href="' . Get_bloginfo('template_directory') . '/style.css" />'; //include wordpress theme stylesheet

//get wordpress template
//snippet comes from template-loader.php
$template = false;
	if     ( is_404()            && $template = get_404_template()            ) :
	elseif ( is_search()         && $template = get_search_template()         ) :
	elseif ( is_front_page()     && $template = get_front_page_template()     ) :
	elseif ( is_home()           && $template = get_home_template()           ) :
	elseif ( is_post_type_archive() && $template = get_post_type_archive_template() ) :
	elseif ( is_tax()            && $template = get_taxonomy_template()       ) :
	elseif ( is_attachment()     && $template = get_attachment_template()     ) :
		remove_filter('the_content', 'prepend_attachment');
	elseif ( is_single()         && $template = get_single_template()         ) :
	elseif ( is_page()           && $template = get_page_template()           ) :
	elseif ( is_category()       && $template = get_category_template()       ) :
	elseif ( is_tag()            && $template = get_tag_template()            ) :
	elseif ( is_author()         && $template = get_author_template()         ) :
	elseif ( is_date()           && $template = get_date_template()           ) :
	elseif ( is_archive()        && $template = get_archive_template()        ) :
	elseif ( is_comments_popup() && $template = get_comments_popup_template() ) :
	elseif ( is_paged()          && $template = get_paged_template()          ) :
	else :
		$template = get_index_template();
	endif;
//filter current template before including it
$buffer = "";
if ( $template = apply_filters( 'template_include', $template ))  {
		ob_start();
			include( $template );
			$buffer = ob_get_contents();
		ob_end_clean();
}

//disable search bar "magic findy box"
$newParams["searchBarDisabled"] = true;

//unselect the forum navigation tab
$newParams["tabs"]["forums"]["selected"] = false;
$newParams["selectedTab"] = false;
//create and select a blog navigation tab
//$newParams["selectedTab"] = true;
//$newParams["tabs"]["blog"]["selected"] = true;
//$newParams["tabs"]["blog"]["href"] = "http://onemoreblock.com/blog/";
//$newParams["tabs"]["blog"]["title"] = new XenForo_Phrase("blog");
//setup sidebar
ob_start();
	get_sidebar();
	$sidebar = ob_get_contents();
	$newParams["sidebar"] = $sidebar;//getSidebar();
ob_end_clean();
//$newParams["noVisitorPanel"] = true;
//setup title
if (function_exists(get_template_title)) {
	$newParams["title"] = get_template_title();
} else {
	$newParams["title"] = "One More Blog";
}
//setup redirection
$newParams["requestPaths"]["fullBasePath"] = "http://onemoreblock.com/forum/";


$content .= '<section id="content" role="main">';
$content .= $buffer;
$content .= '</section>';

/*!-------- render web page -------------------------!*/
$fc = new XenForo_FrontC(new XenForo_Dependencies_Public());
$fc->run($content, $newParams);
return;



/*
//Build passed variable data
$passed_data = "?";
foreach ($_REQUEST as $key => $value) {
	$passed_data .= "$key=$value&";
}
$passed_data = substr($passed_data, 0, -1); //remove trailing '&' or '?'

//Retrieve website content
echo url_get_contents("http://onemoreblock.com/forum/pages/blog/" . $passed_data);
return;
*/
?>