/**
 * @version $Id$
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools galleryGrid layout
 * @copyright Copyright (C) 2017 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

!(function($){

	var pauseVideo = function(video) {
		
		var provider = video.first().data('provider');
		
		switch(provider) {
		
			case 'YouTube':
				video[0].contentWindow.postMessage('{"event":"command","func":"pauseVideo"}', '*');
				break;
			case 'Vimeo':
				video[0].contentWindow.postMessage('{"event":"command","method":"pause"}', '*');
				break;
			default:
				video[0].pause();
				break;
		}
	};

	var playVideo = function(video) {
		
		var provider = video.first().data('provider');
		
		switch(provider) {
		
			case 'YouTube':
				video[0].contentWindow.postMessage('{"event":"command","func":"playVideo"}', '*');
				break;
			case 'Vimeo':
				video[0].contentWindow.postMessage('{"event":"command","method":"play"}', '*');
				break;
			default:
				video[0].play();
				break;
		}
		
		if(!video.hasClass('play')) {
			switch(provider) {
			
				case 'html5':
					if(video[0].readyState == 4) {
						video.addClass('play');
					} else {
						video[0].addEventListener('canplay', function(){
							video.addClass('play');
						});
					}
					break;
				default:
					video.addClass('play');
					break;
			}
		}
	};
	
	$(document).on('ready',function(){
	

		$('.dj-galleryGrid .play-video, .dj-masonry .play-video').click(function(e){
			e.preventDefault();
			var video = $(this).prev('.full_video');
			if(video.length) {
				playVideo(video);
			}
		});
	});

})(jQuery);