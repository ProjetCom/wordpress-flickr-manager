<?php
ini_set('display_errors', 0);
header('Content-Type: text/javascript');
header('Cache-Control: no-cache');
header('Pragma: no-cache');
require_once("../../../../wp-config.php");
global $flickr_manager;
?>

function updateFlickrHref(anchor) {
	var image = anchor.getElementsByTagName('img');
	image = image[0];
	
	var chkClass = image.getAttribute("class");
	if (chkClass === null) {
		chkClass = image.getAttribute("className");
	}
	
	if(chkClass && chkClass.match("flickr-original")) {
		anchor.setAttribute("href", image.getAttribute("longdesc"));
	} else {
		var image_link = image.getAttribute("src");
		var imageSize = "";
		
		if(chkClass) {
			var testResult = chkClass.match(/flickr\-small|flickr\-medium|flickr\-large/);
			switch(testResult.toString()) {
				case "flickr-large":
					imageSize = "_b";
					break;
				case "flickr-medium":
					imageSize = "";
					break;
				case "flickr-small":
					imageSize = "_m";
					break;
			}
		}
		
		if(image_link.match(/[s,t,m]\.jpg/)) {
			image_link = image_link.split("_");
			image_link.pop();
			image_link[image_link.length - 1] = image_link[image_link.length - 1] + imageSize + ".jpg";
			image_link = image_link.join("_");
		} else if(!image_link.match(/b\.jpg/)) {
			image_link = image_link.split(".");
			image_link.pop();
			image_link[image_link.length - 1] = image_link[image_link.length - 1] + imageSize + ".jpg";
			image_link = image_link.join(".");
		}
		anchor.setAttribute("href", image_link);
	}
}



function prepareWFMImages() {
	
	jQuery('a[@rel*=flickr-mgr]').click(function() {
		
		if(jQuery(this).attr("rel") == "flickr-mgr") {	// Individual Photo
		
			var origUrl = jQuery(this).attr("href");
			updateFlickrHref(this);
			
			jQuery(this).lightbox({
				fixedNavigation:	true,
				fileLoadingImage:	"<?php echo $flickr_manager->getAbsoluteUrl(); ?>/images/loading-3.gif",
				fileBottomNavCloseImage:	"<?php echo $flickr_manager->getAbsoluteUrl(); ?>/images/closelabel.gif"
			});
			
			jQuery(this).attr("rel","");
			jQuery(this).lightbox.start(this);
			
			var anchor = this;
			
			setTimeout(function() {
				jQuery(anchor).attr("rel","flickr-mgr");
				jQuery(anchor).attr("href",origUrl);
			}, 100);
			
		} else {	// Member of photoset
			var origUrls = [];
			var setRel = jQuery(this).attr("rel");
			
			jQuery("a").each(function(){
				if(this.href && (this.rel == setRel)){
					origUrls.push([jQuery(this).attr("href"), jQuery(this).attr("title")]);
					updateFlickrHref(this);
				}
			});
			origUrls.reverse();
			
			jQuery(this).lightbox({
				fixedNavigation:	true,
				fileLoadingImage:	"<?php echo $flickr_manager->getAbsoluteUrl(); ?>/images/loading-3.gif",
				fileBottomNavCloseImage:	"<?php echo $flickr_manager->getAbsoluteUrl(); ?>/images/closelabel.gif"
			});
			
			jQuery(this).lightbox.start(this);
			
			// Delay changing the URL's back because Internet Explorer doesn't wait for execution to finish
			setTimeout(function() {
				jQuery("a").each(function(){
					if(this.href && (this.rel == setRel)){
						var url = origUrls.pop();
						jQuery(this).attr("href",url[0]);
					}
				});
			}, 100);
			
		}
			
		
		return false;
	});
	
}

// Thanks go to Michael Wender for the jQuery no conflict update
jQuery.noConflict();
jQuery(document).ready(function() {
	prepareWFMImages();
});