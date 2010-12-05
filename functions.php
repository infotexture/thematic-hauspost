<?php
/**
 * Customized theme functions (post headers, footers, etc.)
 * $Id$ 
 */

//
//  Custom Child Theme Functions
//

// I've included a "commented out" sample function below that'll add a home link to your menu
// More ideas can be found on "A Guide To Customizing The Thematic Theme Framework" 
// http://themeshaper.com/thematic-for-wordpress/guide-customizing-thematic-theme-framework/

// Adds a home link to your menu
// http://codex.wordpress.org/Template_Tags/wp_page_menu
// Revised for v0.9.7.7 per
// <http://themeshaper.com/forums/topic/thematic-0976-is-online-important-release-notes>
// and <http://developing.thematic4you.com/2010/04/breaking-things-to-fix-others/>
function childtheme_menu_args($args) {
    $args = array(
        /*'show_home' => 'Start',*/
        'sort_column' => 'menu_order',
        'menu_class' => 'menu',
        /*'exclude' => '6,508,511',   Hide Start, Impressum & Quellen in page menu */
        'echo' => false
    );
	return $args;
}
add_filter('wp_page_menu_args','childtheme_menu_args', 20);

// Update for Thematic v0.9.7.7
// <http://themeshaper.com/forums/topic/thematic-0976-is-online-important-release-notes>
//
// Unleash the power of Thematic's dynamic classes
define('THEMATIC_COMPATIBLE_BODY_CLASS', true);
define('THEMATIC_COMPATIBLE_POST_CLASS', true);
// Unleash the power of Thematic's comment form
define('THEMATIC_COMPATIBLE_COMMENT_FORM', true);
// Unleash the power of Thematic's feed link functions
define('THEMATIC_COMPATIBLE_FEEDLINKS', true);

//
//  End default Child Theme Functions from 'thematicsamplechildtheme', begin customizations
//

// Remove blog title from header, let background image link to home per
// <http://wizardinternetsolutions.com/wordpress/thematic/thematic-header-image-way/>
// Add Header Image // Add Header Image
/*
function thematic_logo_image() {
 echo '<a href="'.get_bloginfo('url').'" title="'.get_bloginfo('name').'" ><span id="header-image">&nbsp;</span></a>';
}
add_action('thematic_header','thematic_logo_image',6);
*/


// Edits based on "How to change Postheader" <http://www.bendler.tv/?p=327>

// Information in Post Header
function hauspost_postheader() {
    global $post, $authordata;
    
    if (is_single() || is_page()) {
        $posttitle = '<h1 class="entry-title">' . get_the_title() . "</h1>\n";
    } elseif (is_404()) {    
        $posttitle = '<h1 class="entry-title">' . __('Not Found', 'thematic') . "</h1>\n";
    } else {
        $posttitle = '<h2 class="entry-title"><a href="';
        $posttitle .= get_permalink();
        $posttitle .= '" title="';
        $posttitle .= __('Permalink to ', 'thematic') . the_title_attribute('echo=0');
        $posttitle .= '" rel="bookmark">';
        $posttitle .= get_the_title();   
        $posttitle .= "</a></h2>\n";
    }
/*  Post headers: entry-meta stuff (author & date)  */ 
    $postmeta = '<div class="entry-meta">';
    /* Hide 
    // Author 
    $postmeta .= '<span class="author vcard">';
    $postmeta .= __('By ', 'thematic') . '<a class="url fn n" href="';
    $postmeta .= get_author_link(false, $authordata->ID, $authordata->user_nicename);
    $postmeta .= '" title="' . __('View all posts by ', 'thematic') . get_the_author() . '">';
    $postmeta .= get_the_author();
    $postmeta .= '</a></span>';
    // Date
    $postmeta .= '<span class="entry-date">';
    // Hide ugly ISO date abbreviation
    // $postmeta .= get_the_time('Y-m-d\TH:i:sO') . '">';
    $postmeta .= get_the_time('j. F Y');
    $postmeta .= '</span>';
    */    
    $postmeta .= "</div><!-- .entry-meta -->\n";
   
        if ($post->post_type == 'page' || is_404()) {
        $postheader = $posttitle;        
    } else {
        $postheader = $posttitle . $postmeta;    
    }

        echo apply_filters( 'hauspost_postheader', $postheader ); // Filter to override default post header
}
add_filter ('thematic_postheader', 'hauspost_postheader'); // Crazy important!

// Information in Post Footer
function hauspost_postfooter() {
    global $id, $post;

    // Create $posteditlink    
    $posteditlink .= '<a href="' . get_bloginfo('wpurl') . '/wp-admin/post.php?action=edit&amp;post=' . $id;
    $posteditlink .= '" title="' . __('Edit post', 'thematic') .'">';
    $posteditlink .= __('Edit', 'thematic') . '</a>';
    
    // Display the post categories  
    $postcategory = '<div class="entry-utility">';
    $postcategory .= '<span class="cat-links">';
    if (is_single()) {
        $postcategory .= __('This entry was posted in ', 'thematic') . get_the_category_list(', ');
        $postcategory .= '</span>';
    } elseif ( is_category() && $cats_meow = thematic_cats_meow(', ') ) { /* Returns categories other than the one queried */
        $postcategory .= __('Also posted in ', 'thematic') . $cats_meow;
        $postcategory .= '</span><span class="meta-sep"> </span>';
    } else {
        $postcategory .= __('Posted in ', 'thematic') . get_the_category_list(', ');
        $postcategory .= '</span><span class="meta-sep"> | </span>';
    }
    // Add the postmeta stuff from the header (Veröffentlicht in <cat> von <author> am <date>.)
    $postmeta .= '<span class="author vcard">';
    $postmeta .= __('By ', 'thematic') . '<a class="url fn n" href="';
    $postmeta .= get_author_link(false, $authordata->ID, $authordata->user_nicename);
    $postmeta .= '" title="' . __('View all posts by ', 'thematic') . get_the_author() . '">';
    $postmeta .= get_the_author();
    $postmeta .= '</a></span><span class="meta-sep"> am </span>';
    $postmeta .= '<span class="entry-date"><abbr class="published" title="';
    $postmeta .= get_the_time('Y-m-d\TH:i:sO') . '">';
    $postmeta .= get_the_time('j. F Y');
    $postmeta .= '</abbr></span><span class="meta-sep">. </span>';    
    
    // Display the tags
    if (is_single()) {
        $tagtext = __(' and tagged', 'thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span>');
    } elseif ( is_tag() && $tag_ur_it = thematic_tag_ur_it(', ') ) { /* Returns tags other than the one queried */
        $posttags = '<span class="tag-links">' . __(' Also tagged ', 'thematic') . $tag_ur_it . '</span> <span class="meta-sep">|</span>';
    } else {
        $tagtext = __('Tagged', 'thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span> <span class="meta-sep">|</span>');
    }
    
    // Display comments link and edit link
    if (comments_open()) {
        $postcommentnumber = get_comments_number();
        if ($postcommentnumber > '1') {
            $postcomments = ' <span class="comments-link"><a href="' . get_permalink() . '#comments" title="' . __('Comment on ', 'thematic') . the_title_attribute('echo=0') . '">';
            $postcomments .= get_comments_number() . __(' Comments', 'thematic') . '</a></span>';
        } elseif ($postcommentnumber == '1') {
            $postcomments = ' <span class="comments-link"><a href="' . get_permalink() . '#comments" title="' . __('Comment on ', 'thematic') . the_title_attribute('echo=0') . '">';
            $postcomments .= get_comments_number() . __(' Comment', 'thematic') . '</a></span>';
        } elseif ($postcommentnumber == '0') {
            $postcomments = ' <span class="comments-link"><a href="' . get_permalink() . '#comments" title="' . __('Comment on ', 'thematic') . the_title_attribute('echo=0') . '">';
            $postcomments .= __('Leave a comment', 'thematic') . '</a></span>';
        }
    } else {
        $postcomments = ' <span class="comments-link">' . __('Comments closed', 'thematic') .'</span>';
    }
    // Display edit link
    if (current_user_can('edit_posts')) {
        $postcomments .= ' <span class="meta-sep">|</span> ' . $posteditlink;
    }               
    
    // Display permalink, comments link, and RSS on single posts
    $postconnect .= __('. Bookmark the ', 'thematic') . '<a href="' . get_permalink() . '" title="' . __('Permalink to ', 'thematic') . the_title_attribute('echo=0') . '">';
    $postconnect .= __('permalink', 'thematic') . '</a>.';
    if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) { /* Comments are open */
        $postconnect .= ' <a class="comment-link" href="#respond" title ="' . __('Post a comment', 'thematic') . '">' . __('Post a comment', 'thematic') . '</a>';
        $postconnect .= __(' or leave a trackback: ', 'thematic');
        $postconnect .= '<a class="trackback-link" href="' . trackback_url(FALSE) . '" title ="' . __('Trackback URL for your post', 'thematic') . '" rel="trackback">' . __('Trackback URL', 'thematic') . '</a>.';
    } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) { /* Only trackbacks are open */
        $postconnect .= __(' Comments are closed, but you can leave a trackback: ', 'thematic');
        $postconnect .= '<a class="trackback-link" href="' . trackback_url(FALSE) . '" title ="' . __('Trackback URL for your post', 'thematic') . '" rel="trackback">' . __('Trackback URL', 'thematic') . '</a>.';
    } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) { /* Only comments open */
        $postconnect .= __(' Trackbacks are closed, but you can ', 'thematic');
        $postconnect .= '<a class="comment-link" href="#respond" title ="' . __('Post a comment', 'thematic') . '">' . __('post a comment', 'thematic') . '</a>.';
    } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) { /* Comments and trackbacks closed */
        $postconnect .= __(' Both comments and trackbacks are currently closed.', 'thematic');
    }
    // Display edit link on single posts
    if (current_user_can('edit_posts')) {
        $postconnect .= ' ' . $posteditlink;
    }
    
    // Add it all up
    if ($post->post_type == 'page' && current_user_can('edit_posts')) { /* For logged-in "page" search results */
        $postfooter = '<div class="entry-utility">' . $posteditlink;
        $postfooter .= "</div><!-- .entry-utility -->\n";    
    } elseif ($post->post_type == 'page') { /* For logged-out "page" search results */
        $postfooter = '';
    } else {
        if (is_single()) {
            $postfooter = $postcategory . $postmeta . $posttags . $postconnect;
        } else {
            $postfooter = $postcategory . $postmeta . $posttags . $postcomments;
        }
        $postfooter .= "</div><!-- .entry-utility -->\n";    
    }
    
    // Put it on the screen
    echo apply_filters( 'hauspost_postfooter', $postfooter ); // Filter to override default post footer
}
add_filter ('thematic_postfooter', 'hauspost_postfooter'); // Crazy important!

// $Id$
?>