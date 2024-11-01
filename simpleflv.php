<?php
/*
 Plugin Name: Simple FLV
 Version: 1.8
 Author: CyberSEO.net
 Author URI: http://www.cyberseo.net/
 Plugin URI: http://www.cyberseo.net/simple-flv-plugin/
 Description: The Simple FLV plugin allows one to easily embed Flash videos (FLV files) into WordPress blogs using the extended universal FLV tag style: <strong>[flv:url image width height link player]</strong>. Where <strong>url</strong> - URL of the FLV video file you want to embed; <strong>image</strong> - URL of a preview image (shown in display and playlist); <strong>width</strong> - width of an FLV video (optional parameter, default: 450); <strong>height</strong> - height of an FLV video (optional parameter, default: 317); <strong>link</strong> - URL to an external page the display, controlbar and playlist can link to (optional parameter, default: #); <strong>player</strong> - URL to FLV player (optional parameter, default: /wp-content/plugins/simple-flv/flwplayer.swf).
 */

if (!function_exists("get_option") || !function_exists("add_filter")) {
	die();
}

define('SIMPLE_FLV_AUTOPLAY', 'true'); // change this to 'false' if you want to disable smart autoplay
define('SIMPLE_FLV_DEFAULT_WIDTH', '450');
define('SIMPLE_FLV_DEFAULT_HEIGHT', '317');
define('SIMPLE_FLV_DEFAULT_PLAYER', get_option('siteurl') . "/wp-content/plugins/" . dirname(plugin_basename(__FILE__)) . '/flvplayer.swf');

function simpleFlvUrlFix($url) {
	if (stripos($url, 'http://') === false && !file_exists($url)) {
		$url = 'http://' . $url;
	}
	return $url;
}

function simpleFlvInsert($string) {
	@list($url, $thumbnail, $width, $height, $link, $player) = explode(" ", $string);
	$url = simpleFlvUrlFix($url);
	$thumbnail = simpleFlvUrlFix($thumbnail);
	if (!isset($width) || $width == "0") {
		$width = SIMPLE_FLV_DEFAULT_WIDTH;
	}
	if (!isset($height) || $height == "0") {
		$height = SIMPLE_FLV_DEFAULT_HEIGHT;
	}
	if (!isset($link)) {
		$linkfromdisplay = 'false';
		$displayclick = 'play';
		$link = "#";
	} else {
		$linkfromdisplay = 'true';
		$displayclick = 'link';
		$link = urlencode(html_entity_decode($link));
	}
	if (!isset($player)) {
		$player = SIMPLE_FLV_DEFAULT_PLAYER;
	}
	if (is_single() || is_page()) {
		$autostart = SIMPLE_FLV_AUTOPLAY;
	} else {
		$autostart = 'false';
	}
	return "\n" . '<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="' . $width . '" height="' . $height . '">
        <param name="movie" value="' . $player . '" />
        <param name="allowfullscreen" value="true" />
        <param name="allowscriptaccess" value="always" />
        <param name="flashvars" value="image=' . $thumbnail . '&file=' . $url . '&autostart=' . $autostart . '&linkfromdisplay=' . $linkfromdisplay . '&displayclick=' . $displayclick . '&link=' . $link . '" />
        <!--[if !IE]>-->
        <object type="application/x-shockwave-flash" data="' . $player . '" width="' . $width . '" height="' . $height . '">
                <param name="movie" value="' . $url . '" />
                <param name="link" value="' . $link . '" />
                <param name="allowfullscreen" value="true" />
                <param name="linkfromdisplay" value="' . $linkfromdisplay . '" />
                <param name="displayclick" value="' . $displayclick . '" />
                <param name="allowscriptaccess" value="always" />
                <param name="flashvars" value="image=' . $thumbnail . '&file=' . $url . '&autostart=' . $autostart . '&linkfromdisplay=' . $linkfromdisplay . '&displayclick=' . $displayclick . '&link=' . $link . '" />
                <p><a href="http://get.adobe.com/flashplayer">Get Flash</a> to see this player.</p>
        </object>
        <!--<![endif]-->
</object>' . "\n";
}

function simpleFlvContent($content) {
	$content = preg_replace("'\[flv:(.*?)\]'ie", "stripslashes(simpleFlvInsert('\\1'))", $content);
	return $content;
}

add_filter('the_content', 'simpleFlvContent');
add_filter('the_excerpt', 'simpleFlvContent');
?>