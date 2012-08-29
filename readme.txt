=== Theme Blvd Featured Image Link Override ===
Contributors: themeblvd
Tags: themeblvd, featured images, thumbnails, links
Requires at least: Theme Blvd Framework 2.1+
Stable tag: 1.0.1

When using a theme with Theme Blvd framework version 2.1+, this plugin allows you to set featured image link options globally throughout your site.

== Description ==

When using a theme with Theme Blvd framework version 2.1+, this plugin allows you to set featured image link options globally throughout your site.

= The Problem =

The Theme Blvd framework has an intricate internal system for displaying posts and their respective featured images. You can choose from many different options as far as what link wraps each post's featured image. However, this can only be done individually for each post. By default, when you create a new post, this setting will always start at "Featured Image is not a link."

This is a problem if you're creating a site where you want all featured images to do one action because then you'd have to change the "Featured Image Link" setting for each post you create, one-by-one. Unfortunately, with the logic of the framework the way it is, there's really no good way for us to accommodate this without losing other aspects.

= The Solution =

So, this plugin is your solution -- a bit of a "hack" to allow you do to accomplish this. When you install the plugin, two new options will be added to your Theme Options at *Appearance > Theme Options > Configuration*.

The two options will apply to **ALL** of your posts that currently have the default setting, "Featured Image is not a link." -- See screenshots tab for a quick peak.

== Installation ==

1. Upload `theme-blvd-featured-image-link-override` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to *Appearance > Theme Options > Configuration* to use.

== Screenshots ==

1. Options added to your current theme at *Appearance > Theme Options > Configuration*.

== Changelog ==

= 1.0.1 =

* Fixed bug with override being applied when it shouldn't be.

= 1.0.0 =

* This is the first release.
