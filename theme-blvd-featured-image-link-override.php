<?php
/*
Plugin Name: Theme Blvd Featured Image Link Override
Description: When using a theme with Theme Blvd framework version 2.1.0+, this plugin allows you to set featured image link options globally throughout your site.
Version: 1.0.2
Author: Jason Bobich
Author URI: http://jasonbobich.com
License: GPL2
License: GPL2

    Copyright 2012  Jason Bobich

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/*
WAIT! Before we move on, we need to clear up one, little thing...

What the hell does "filo" stand for? -- You will see this used through
out the plugin, and it standard for "featured image link override" ...
No biggy.
*/

/**
 * Add filo options to Appearance > Theme Options > Configuration
 *
 * @since 1.0.0
 */

function themeblvd_filo_options() {
	
	// First make sure they're using a theme that supports this.
	if( function_exists( 'themeblvd_add_option_section' ) ) {
		
		// Setup params
		$name = __( 'Featured Image Link Override', 'themeblvd_filo' );
		$description = __( 'The Theme Blvd framework has an intricate internal system for displaying posts and their respective featured images. You can configure what link wraps each post\'s featured image. However, this can only be done individually for each post. By default, when you create a new post, this setting will always start at "Featured Image is not a link."<br><br>This is a problem if you\'re creating a site where you want all featured images to do one action because then you\'d have to change the "Featured Image Link" setting for each post you create, one-by-one. Unfortunately, with the logic of the framework the way it is, there\'s really no good way for us to accommodate this without losing other aspects.<br><br>So, this plugin is your solution -- a bit of a "hack" to allow you do to accomplish this. The two options below for this plugin will apply to <strong>ALL</strong> of your posts that currently have the default setting, "Featured Image is not a link."', 'themeblvd_filo' );
		$options = array(
			array(
				'name' 		=> __( 'Link Override', 'themeblvd_filo' ),
				'desc' 		=> __( 'Select how you\'d like all featured image links currently set to "Featured image is not a link" to be overridden.', 'themeblvd_filo' ),
				'id' 		=> 'filo',
				'std' 		=> 'none',
				'type' 		=> 'radio',
				'options'	=> array(
					'none' 	=> __( 'No, do not apply any override.', 'themeblvd_filo' ),
					'post' 	=> __( 'Featured images link to their posts.', 'themeblvd_filo' ),
					'image'	=> __( 'Featured images link to their enlarged versions in a lightbox.', 'themeblvd_filo' )
				)
			),
			array(
				'name' 		=> __( 'Single Posts', 'themeblvd_filo' ),
				'desc' 		=> __( 'Would you like to apply your above selection to single posts, as well?<br><br>For example, if you have your featured images set to link to their posts, you may not want this functionality to repeat on the single post\'s page.', 'themeblvd_filo' ),
				'id' 		=> 'filo_single',
				'std' 		=> 'true',
				'type' 		=> 'radio',
				'options'	=> array(
					'true' 	=> __( 'Yes, apply the above override to single posts, too.', 'themeblvd_filo' ),
					'false' => __( 'No, do not apply the above override to single posts.', 'themeblvd_filo' )
				)
			)
		);
		
		// Add option section
		themeblvd_add_option_section( 'config', 'filo', $name, $description, $options );
	}
}
add_action( 'after_setup_theme', 'themeblvd_filo_options' );

/**
 * Add filter onto Theme Blvd featured images.
 *
 * @since 1.0.0
 */

function themeblvd_filo_post_thumbnail( $output, $location, $size, $link ) {
	
	global $post;
	
	$override = false;
	$link = false;
	$link_url = '';
	$link_target = '';
	$title = '';
	
	// Get original featured image link option from individual post.
	$thumb_link_meta = get_post_meta( $post->ID, '_tb_thumb_link', true );
	
	// The actual override. This is the whole point of this new 
	// function. If the user has set the featured image link to 
	// be inactive, we want to override it with our plugin's settings.
	if( ! $thumb_link_meta || $thumb_link_meta == 'inactive' ) {

		// Get "filo" plugin settings
		$filo = themeblvd_get_option( 'filo' );
		$filo_single = themeblvd_get_option( 'filo_single' );
		
		// Only continue if user set an override option
		if( $filo == 'post' || $filo == 'image' ) {
			
			// Flip on override
			$override = true;
			
			// Check for the single post override to the 
			// plugin override. Confused, yet?
			if( $filo_single === 'false' && is_single() )
				$override = false;

			// No point moving forward if we're on a single 
			// post and the user has setup overrides not 
			// to take effect on single posts.
			if( $override ) {
				
				// Setup attachment ID
				$attachment_id = get_post_thumbnail_id( $post->ID );
				
				// Determine proper link
				switch( $filo ) {
					case 'post' :
						$link = true;
						$thumb_link_meta = 'post';
						$link_url = get_permalink( $post->ID );
						break;
					case 'image' :
						$link = true;
						$thumb_link_meta = 'image';
						$link_url = wp_get_attachment_url( $attachment_id );
						$link_target = ' rel="featured_themeblvd_lightbox[gallery]"';
						break;
				}
				
			}
		}
	}
	
	// Only re-do the post thumbnail if the $override is true.
	if( $override ){
	
		// Additional link setup
		if( is_single() ) $link_target = str_replace('[gallery]', '', $link_target );
		$end_link = '<span class="image-overlay"><span class="image-overlay-bg"></span><span class="image-overlay-icon"></span></span>';
		$end_link = apply_filters( 'themeblvd_image_overlay', $end_link );
		
		// Reset output
		$output = '';
		
		// Image check
		$image = wp_get_attachment_image_src( $attachment_id, $size );
		
		// Attributes
		$size_class = $size;
		if( $size_class == 'tb_small' ) $size_class = 'small';
		$classes = 'attachment-'.$size_class.' wp-post-image';
		if( is_single() ) $title = ' title="'.get_the_title($post->ID).'"';
		
		// Final HTML output
		if( has_post_thumbnail( $post->ID ) ) {
			$output .= '<div class="featured-image-wrapper '.$classes.'">';
			$output .= '<div class="featured-image">';
			$output .= '<div class="featured-image-inner">';
			if( $link ) $output .= '<a href="'.$link_url.'"'.$link_target.' class="'.$thumb_link_meta.'"'.$title.'>';	
			$output .= get_the_post_thumbnail( $post->ID, $size, array( 'class' => '' ) );
			if( $link ) $output .= $end_link.'</a>';
			$output .= '</div><!-- .featured-image-inner (end) -->';
			$output .= '</div><!-- .featured-image (end) -->';
			$output .= '</div><!-- .featured-image-wrapper (end) -->';
		}
	} 
	
	// Return final output. If override was never true, 
	// then nothing has been modified with the output.
	return $output;
}
add_filter( 'themeblvd_post_thumbnail', 'themeblvd_filo_post_thumbnail', 10, 4 );