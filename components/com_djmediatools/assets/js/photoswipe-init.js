// initialization of photoswipe for all album instances
!function($){

$(document).ready(function(){
	
	var layout = $('<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true"><div class="pswp__bg"></div><div class="pswp__scroll-wrap"><div class="pswp__container"><div class="pswp__item"></div><div class="pswp__item"></div><div class="pswp__item"></div></div><div class="pswp__ui pswp__ui--hidden"><div class="pswp__top-bar"><div class="pswp__counter"></div><button class="pswp__button pswp__button--close" title="Close (Esc)"></button><button class="pswp__button pswp__button--share" title="Share"></button><button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button><button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button><div class="pswp__preloader"><div class="pswp__preloader__icn"><div class="pswp__preloader__cut"><div class="pswp__preloader__donut"></div></div></div></div></div><div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"><div class="pswp__share-tooltip"></div></div><button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button><button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button><div class="pswp__caption"><div class="pswp__caption__center"></div></div></div></div></div>');
	
	$(document.body).append(layout);
	
	var initPhotoSwipeFromDOM = function(gallerySelector) {

	    // parse slide data (url, title, size ...) from DOM elements 
	    // (children of album)
	    var parseThumbnailElements = function(el) {
	        var thumbElements = $(el).find('a.dj-slide-pswp'),
	            numNodes = thumbElements.length,
	            items = [],
	            figureEl,
	            linkEl,
	            size,
	            item;

	        for(var i = 0; i < numNodes; i++) {

	            linkEl = $(thumbElements[i]); // <a> element

	            size = linkEl.attr('data-size').split('x');

	            // create slide object
	            item = {
	                src: linkEl.attr('href'),
	                w: parseInt(size[0], 10),
	                h: parseInt(size[1], 10),
	                msrc: linkEl.attr('data-msrc'),
	                title: linkEl.attr('data-caption')
	            };

	            item.el = linkEl; // save link to element for getThumbBoundsFn
	            items.push(item);
	        }

	        return items;
	    };
	    
	    // triggers when user clicks on thumbnail
	    var onThumbnailsClick = function(e) {
	        e = e || window.event;
	        e.preventDefault ? e.preventDefault() : e.returnValue = false;

	        var eTarget = e.target || e.srcElement;

	        // find root element of slide
	        var clickedListItem = closest(eTarget, function(el) {
	            return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
	        });

	        if(!clickedListItem) {
	            return;
	        }

	        // find index of clicked item by looping through all child nodes
	        // alternatively, you may define index via data- attribute
	        var clickedGallery = clickedListItem.parentNode,
	            childNodes = clickedListItem.parentNode.childNodes,
	            numChildNodes = childNodes.length,
	            nodeIndex = 0,
	            index;

	        for (var i = 0; i < numChildNodes; i++) {
	            if(childNodes[i].nodeType !== 1) { 
	                continue; 
	            }

	            if(childNodes[i] === clickedListItem) {
	                index = nodeIndex;
	                break;
	            }
	            nodeIndex++;
	        }



	        if(index >= 0) {
	            // open PhotoSwipe if valid index found
	            openPhotoSwipe( index, clickedGallery );
	        }
	        return false;
	    };
	    
	    // parse picture index and gallery index from URL (#&pid=1&gid=2)
	    var photoswipeParseHash = function() {
	        var hash = window.location.hash.substring(1),
	        params = {};

	        if(hash.length < 5) {
	            return params;
	        }

	        var vars = hash.split('&');
	        for (var i = 0; i < vars.length; i++) {
	            if(!vars[i]) {
	                continue;
	            }
	            var pair = vars[i].split('=');  
	            if(pair.length < 2) {
	                continue;
	            }           
	            params[pair[0]] = pair[1];
	        }

	        if(params.gid) {
	            params.gid = parseInt(params.gid, 10);
	        }

	        return params;
	    };

	    var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
	        var pswpElement = document.querySelectorAll('.pswp')[0],
	            gallery,
	            options,
	            items;

	        items = parseThumbnailElements(galleryElement);

	        // define options (if needed)
	        options = {

	            // define gallery index (for URL)
	            galleryUID: galleryElement.getAttribute('data-pswp-uid'),

	            getThumbBoundsFn: function(index) {
	                // See Options -> getThumbBoundsFn section of documentation for more info
	                var thumbnail = items[index].el.find('img')[0], // find thumbnail
	                    pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
	                    rect = thumbnail.getBoundingClientRect(); 

	                return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
	            },
	            
	            showHideOpacity: true,
	            showAnimationDuration: false,
	            bgOpacity: 0.75,
	            mainClass: 'dj-pswp',
	            shareEl: false
	        };

	        // PhotoSwipe opened from URL
	        if(fromURL) {
	            if(options.galleryPIDs) {
	                // parse real index when custom PIDs are used 
	                // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
	                for(var j = 0; j < items.length; j++) {
	                    if(items[j].pid == index) {
	                        options.index = j;
	                        break;
	                    }
	                }
	            } else {
	                // in URL indexes start from 1
	                options.index = parseInt(index, 10) - 1;
	            }
	        } else {
	            options.index = parseInt(index, 10);
	        }

	        // exit if index not found
	        if( isNaN(options.index) ) {
	            return;
	        }

	        if(disableAnimation) {
	            options.showAnimationDuration = 0;
	        }

	        // Pass data to PhotoSwipe and initialize it
	        gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
	        gallery.init();
	    };

	    // loop through all gallery elements and bind events
	    var galleryElements = $(gallerySelector);
	    
	    for(var i = 0, l = galleryElements.length; i < l; i++) {
	    	$(galleryElements[i]).attr('data-pswp-uid', i+1);
	    	$(galleryElements[i]).on('click', 'a.dj-slide-pswp', function(e){
	    		e.preventDefault();
	    		var slide = $(this),
	    			gallery = slide.closest('[data-pswp-uid]'),
	    			slides = gallery.find('a.dj-slide-pswp'),
	    			index = 0;
	    		for(var j = 0; j < slides.length; j++) {
	    			if(slides[j] === this) {
	    				index = j;
	    				break;
	    			}
	    		}
	    		openPhotoSwipe(index, gallery[0]);
	    	});
	    }
	    
	    // Parse URL and open gallery if it contains #&pid=3&gid=1
	    var hashData = photoswipeParseHash();
	    if(hashData.pid && hashData.gid) {
	        openPhotoSwipe( hashData.pid ,  slides[ hashData.gid - 1 ], true, true );
	    }
	};

	// execute above function
	initPhotoSwipeFromDOM('.dj-slides');
});

}(jQuery);