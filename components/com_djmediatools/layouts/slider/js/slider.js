/**
 * @version $Id$
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools slider layout
 * @copyright Copyright (C) 2017 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */
!function(t){window.DJImageSlider=window.DJImageSlider||function(i,s){this.init=function(i,s){var n=t("#djslider-loader"+i.id),e=t("#djslider"+i.id).css("opacity",0),o=t("#slider"+i.id),a=function(t){var i=(document.body||document.documentElement).style;if(void 0===i)return!1;if("string"==typeof i[t])return t;v=["Moz","Webkit","Khtml","O","ms","Icab"],pu=t.charAt(0).toUpperCase()+t.substr(1);for(var s=0;s<v.length;s++)if("string"==typeof i[v[s]+pu])return"-"+v[s].toLowerCase()+"-"+t;return!1}("transition");if(n.length&&e.length&&o.length){var c=t("#slider"+i.id).children("li"),r=i.slide_size,d=i.visible_slides,h=r*c.length,u=c.length-d,l=0,p=s.auto,f=0,g=!1,y=[];switch(i.slider_type){case"up":o.css("position","relative"),o.css("top",0),o.css("height",h),a&&o.css(a,"top "+s.duration+"ms "+s.css3transition);break;case"down":o.css("position","absolute"),o.css("bottom",0),o.css("height",h),a&&o.css(a,"bottom "+s.duration+"ms "+s.css3transition);break;case"left":o.css("position","relative"),o.css("left",0),o.css("width",h),a&&o.css(a,"left "+s.duration+"ms "+s.css3transition);break;case"right":o.css("position","relative"),o.css("right",0),o.css("float","right"),o.css("width",h),a&&o.css(a,"right "+s.duration+"ms "+s.css3transition);break;case"fade":case"ifade":default:c.css("position","absolute"),c.css("top",0),c.css("left",0),o.css("width",r),c.css("opacity",0),c.css("visibility","hidden"),t(c[0]).css("opacity",1),t(c[0]).css("visibility","visible"),a&&c.css(a,"opacity "+s.duration+"ms "+s.css3transition)}if(i.show_arrows>0&&(t("#next"+i.id).on("click",function(t){t.preventDefault(),"right"==i.direction?x():_(),f=1}).on("keydown",function(t){var s=t.which;13!=s&&32!=s||("right"==i.direction?x():_(),f=1,t.preventDefault(),t.stopPropagation())}),t("#prev"+i.id).on("click",function(t){t.preventDefault(),"right"==i.direction?_():x(),f=1}).on("keydown",function(t){var s=t.which;13!=s&&32!=s||("right"==i.direction?_():x(),f=1,t.preventDefault(),t.stopPropagation())})),i.show_buttons>0&&(t("#play"+i.id).on("click",function(t){t.preventDefault(),I(),p=1,f=0}).on("keydown",function(t){var i=t.which;13!=i&&32!=i||(I(),p=1,f=0,t.preventDefault(),t.stopPropagation())}),t("#pause"+i.id).on("click",function(t){t.preventDefault(),I(),p=0}).on("keydown",function(t){var i=t.which;13!=i&&32!=i||(I(),p=0,t.preventDefault(),t.stopPropagation())})),n.on("mouseenter",function(){f=s.pause_autoplay}).on("mouseleave",function(){n.removeClass("focused"),f=0}).on("focus",function(){n.addClass("focused"),n.trigger("mouseenter"),f=1}).on("keydown",function(t){var s=t.which;37!=s&&39!=s||(39==s?"right"==i.direction?x():_():"right"==i.direction?_():x(),t.preventDefault(),t.stopPropagation())}),t(".djslider-end").on("focus",function(){n.trigger("mouseleave")}),n.djswipe(function(t,s){s.x<100||s.y>30||("left"==t.x?"right"==i.direction?x():_():"right"==t.x&&("right"==i.direction?_():x()))}),t("#cust-navigation"+i.id).length){var w=t("#cust-navigation"+i.id).find(".load-button");w.each(function(i){var s=t(this);s.on("click",function(t){t.preventDefault(),g||s.hasClass("load-button-active")||k(i)}).on("keydown",function(t){var n=t.which;13!=n&&32!=n||(g||s.hasClass("load-button-active")||k(i),t.preventDefault(),t.stopPropagation())}),i>u&&s.css("display","none")})}i.preload?setTimeout(D,i.preload):t(window).load(D),m(),t(window).on("resize",m),t(window).on("load",m)}function b(i){var s={x:i.width(),y:i.height()};if((0==s.x||0==s.y)&&i.is(":hidden")){for(var n,e,o=i.parent();o.is(":hidden");)n=o,o=o.parent();e=o.width(),n&&(e-=parseInt(n.css("margin-left")),e-=parseInt(n.css("margin-right")),e-=parseInt(n.css("border-left-width")),e-=parseInt(n.css("border-right-width")),e-=parseInt(n.css("padding-left")),e-=parseInt(n.css("padding-right")));var a=i.clone();a.css({position:"absolute",visibility:"hidden","max-width":e}),t(document.body).append(a),s={x:a.width(),y:a.height()},a.remove()}return s}function m(){var s=b(n.parent()).x,a=parseInt(e.css("max-width")),p=b(e),f=p.x;f>s?f=s:f<=s&&(!a||f<a)&&(f=s>a?a:s),y[d]||(y[d]=p.x/p.y);var g=y[d],v=f/g;e.css("width",f),e.css("height",v);var w,m,_,x=i.slider_type;switch(x){case"up":case"down":var I=parseInt(t(c[0]).css("up"==x?"margin-bottom":"margin-top"));r=(v+I)/d,h=c.length*r+c.length,o.css("height",h),c.css("width",f),c.css("height",r-I),o.css("up"==x?"top":"bottom",-r*l);break;case"left":case"right":I=parseInt(t(c[0]).css("left"==x?"margin-right":"margin-left"));var D=Math.ceil(f/(i.slide_size+I));if(D!=d){if(d=D>i.visible_slides?i.visible_slides:D,u=c.length-d,t("#cust-navigation"+i.id).length)t("#cust-navigation"+i.id).find(".load-button").each(function(i){var s=t(this);i>u?s.css("display","none"):s.css("display","")});y[d]||(y[d]=(d*r-I)/p.y),v=f/(g=y[d]),e.css("height",v)}r=(f+I)/d,h=c.length*r+c.length,o.css("width",h),c.css("width",r-I),c.css("height",v),o.css(x,-r*l),l>u&&k(u);break;case"fade":case"ifade":default:o.css("width",f),c.css("width",f),c.css("height",v)}(i.show_buttons||i.show_arrows)&&((w=t("#navigation"+i.id).position().top)<0?(n.css("padding-top",-w),n.css("padding-bottom",0)):(buttons_height=0,i.show_arrows>0&&(buttons_height=b(t("#next"+i.id)).y,buttons_height=Math.max(buttons_height,b(t("#prev"+i.id)).y)),i.show_buttons>0&&(buttons_height=Math.max(buttons_height,b(t("#play"+i.id)).y),buttons_height=Math.max(buttons_height,b(t("#pause"+i.id)).y)),(m=w+buttons_height-v)>0?(n.css("padding-top",0),n.css("padding-bottom",m)):(n.css("padding-top",0),n.css("padding-bottom",0))),(_=parseInt(t("#navigation"+i.id).css("margin-left"))+parseInt(t("#navigation"+i.id).css("margin-right")))<0&&b(t(window)).x<b(t("#navigation"+i.id)).x-_&&(t("#navigation"+i.id).css("margin-left",0),t("#navigation"+i.id).css("margin-right",0)));C()}function _(){k(l<u?l+1:0)}function x(){k(l>0?l-1:u)}function k(n){if(l!=n){if("fade"==i.slider_type||"ifade"==i.slider_type){if(g)return;g=!0;var e=l;l=n,function(i){t(c[l]).css("visibility","visible"),a?(t(c[l]).css("opacity",1),t(c[i]).css("opacity",0)):(t(c[l]).animate({opacity:1},s.duration,s.transition),t(c[i]).animate({opacity:0},s.duration,s.transition));setTimeout(function(){t(c[i]).css("visibility","hidden"),g=!1},s.duration)}(e)}else switch(l=n,i.slider_type){case"up":a?o.css("top",-r*l):o.animate({top:-r*l},s.duration,s.transition);break;case"down":a?o.css("bottom",-r*l):o.animate({bottom:-r*l},s.duration,s.transition);break;case"left":a?o.css("left",-r*l):o.animate({left:-r*l},s.duration,s.transition);break;case"right":a?o.css("right",-r*l):o.animate({right:-r*l},s.duration,s.transition)}var d;C(),d=l,t("#cust-navigation"+i.id).length&&w.each(function(i){var s=t(this);s.removeClass("load-button-active"),i==d&&s.addClass("load-button-active")})}}function I(){p?(t("#pause"+i.id).css("display","none"),t("#play"+i.id).css("display","block").focus()):(t("#play"+i.id).css("display","none"),t("#pause"+i.id).css("display","block").focus())}function D(){n.css("background","none"),e.css("opacity",1),i.show_buttons>0&&(play_width=b(t("#play"+i.id)).x,t("#play"+i.id).css("margin-left",-play_width/2),pause_width=b(t("#pause"+i.id)).x,t("#pause"+i.id).css("margin-left",-pause_width/2),p?t("#play"+i.id).css("display","none"):t("#pause"+i.id).css("display","none")),function t(){setTimeout(function(){p&&!f&&_(),t()},s.delay)}()}function C(){c.each(function(s){"down"==i.slider_type&&(s=c.length-s-1);var n=t(this).find("a[href], input, select, textarea, button");s>=l&&s<l+parseInt(d)?n.each(function(){t(this).removeProp("tabindex")}):n.each(function(){t(this).prop("tabindex","-1")})})}},this.init(i,s)};t.fn.djswipe=t.fn.djswipe||function(i){var s=!1,n=null,e=null;return $el=t(this),$el.on("touchstart",function(t){s=!0,n={x:t.originalEvent.touches[0].pageX,y:t.originalEvent.touches[0].pageY}}),$el.on("touchend",function(){s=!1,e&&i(e.direction,e.offset),n=null,e=null}),$el.on("touchmove",function(t){var i,o,a;s&&(o=(i=t).originalEvent.touches[0].pageX,a=i.originalEvent.touches[0].pageY,e={direction:{x:o>n.x?"right":"left",y:a>n.y?"down":"up"},offset:{x:Math.abs(o-n.x),y:Math.abs(n.y-a)}})}),!0}}(jQuery);