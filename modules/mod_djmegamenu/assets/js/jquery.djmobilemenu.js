/**
 * @version $Id$
 * @package DJ-MegaMenu
 * @copyright Copyright (C) 2017 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 */
!function(n){var e=function(e,o){var i=n('<select id="'+e.attr("id")+'select" class="inputbox dj-select" />').on("change",function(){n(this).val&&(window.location=n(this).val())}),t=e.find("li.dj-up");a(t,i,0),i.appendTo(o),o.find(".dj-mobile-open-btn").on("click",function(n){n.stopPropagation(),n.preventDefault();var e=i[0];if(document.createEvent){var a=document.createEvent("MouseEvents");a.initMouseEvent("mousedown",!0,!0,window,0,0,0,0,0,!1,!1,!1,!1,0,null),e.dispatchEvent(a)}else e.fireEvent&&e.fireEvent("onmousedown")})},a=function(e,o,i){for(var t="",d=!1,c=0;c<i;c++)t+="- ";e.each(function(){var e=n(this),c=e.find("> a").first(),s=e.find("> .dj-subwrap > .dj-subwrap-in > .dj-subcol > .dj-submenu > li, > .dj-subtree > li");if(c.length){var l="",f=c.find("img").first();f.length?l=t+f.attr("alt"):(l=c.html().replace(/(<small[^<]+<\/small>)/gi,""),l=t+l.replace(/(<([^>]+)>)/gi,""));var r=n('<option value="'+c.prop("href")+'">'+l+"</option>").appendTo(o);c.prop("href")||r.prop("disabled",!0),e.hasClass("active")&&(o.val(r.val()),d=!0)}s&&a(s,o,i+1)}),i||d||(n('<option value="">- MENU -</option>').prependTo(o),o.val(""))},o=function(e){var a=null;e.find("ul.dj-mobile-nav > li, ul.dj-mobile-nav-child > li").each(function(){var e=n(this),o=e.find("> a").first();o.length&&(e.find("> ul.dj-mobile-nav-child > li:not(:empty)").length||(e.removeClass("parent"),e.find("ul.dj-mobile-nav-child").remove()),e.hasClass("parent")&&(e.hasClass("active")?o.attr("aria-expanded",!0):o.attr("aria-expanded",!1),o.append('<span class="toggler"></span>'),o.on("click",function(i){e.hasClass("active")?n(i.target).hasClass("toggler")&&(i.preventDefault(),i.stopPropagation(),clearTimeout(a),e.removeClass("active"),o.attr("aria-expanded",!1)):i.preventDefault()})),o.on("focus",function(){a=setTimeout(function(){e.click()},250)})),e.on("click",function(){e.siblings().removeClass("active"),e.siblings().find("> a").attr("aria-expanded",!1),e.addClass("active"),o.length&&o.attr("aria-expanded",!0)})})},i=function(e){var a=null,i=jQuery(".dj-offcanvas-wrapper").first(),t=jQuery(".dj-offcanvas-pusher").first(),d=jQuery(".dj-offcanvas-pusher-in").first();i.length||(a=n(document.body).children(),i=n('<div class="dj-offcanvas-wrapper" />'),t=n('<div class="dj-offcanvas-pusher" />'),d=n('<div class="dj-offcanvas-pusher-in" />'));var c=e.find(".dj-offcanvas").first(),s=c.data("effect");n(document.body).addClass("dj-offcanvas-effect-"+s);var l=null;e.find(".dj-mobile-open-btn").on("click",function(e){e.stopPropagation(),e.preventDefault(),clearTimeout(l),c.data("scroll",n(window).scrollTop()),n(document.body).addClass("dj-offcanvas-anim"),setTimeout(function(){n(document.body).addClass("dj-offcanvas-open")},50),d.css("top",-c.data("scroll")),c.find(".dj-offcanvas-close-btn").focus()}),a&&n(document.body).prepend(i),3==s||6==s||7==s||8==s||14==s?t.append(c):i.append(c),a&&(i.append(t),t.append(d),d.append(a)),c.find(".dj-offcanvas-close-btn").on("click",function(a){a.stopPropagation(),a.preventDefault(),n(document.body).hasClass("dj-offcanvas-open")&&(n(document.body).removeClass("dj-offcanvas-open"),l=setTimeout(function(){e.find(".dj-mobile-open-btn").focus(),d.css("top",0),n(document.body).removeClass("dj-offcanvas-anim"),n(window).scrollTop(c.data("scroll"))},500))}),n(".dj-offcanvas-pusher").on("click",function(e){n(e.target).hasClass("dj-offcanvas-pusher")&&c.find(".dj-offcanvas-close-btn").click()}),c.find(".dj-offcanvas-close-btn").on("keydown",function(n){9==n.which&&setTimeout(function(){c.find(":focus").length||c.find(".dj-offcanvas-close-btn").click()},50)}),c.find(".dj-offcanvas-end").on("focus",function(){c.find(".dj-offcanvas-close-btn").click()}),o(c),c.find("a").click(function(e){var a=n(this).attr("href");if(a&&"#"==a.charAt(0)){var o=n(a);if(o.length){e.preventDefault();var i=o.position().top;c.data("scroll",i),d.css("top",-i)}}})},t=function(e){e.find(".dj-mobile-open-btn").on("click",function(n){n.stopPropagation(),n.preventDefault(),e.find(".dj-accordion-in").slideToggle("fast")}),n(document).on("click",function(a){n(a.target).closest(".dj-accordion-in").length||e.find(".dj-accordion-in").is(":visible")&&e.find(".dj-accordion-in").slideUp("fast")}),o(e)},d=[],c=null,s=function(){window.clearTimeout(c),c=window.setTimeout(function(){for(var e=0;e<d.length;e++)d[e].mobile&&(window.matchMedia("(max-width: "+d[e].trigger+"px)").matches?(n(document.body).addClass("dj-megamenu-mobile"),n(document.body).addClass(d[e].id+"-mobile"),n.contains(document,d[e].menu[0])&&(d[e].menu.after(d[e].menuHandler),d[e].menu.detach()),n.contains(document,d[e].mobileHandler[0])&&d[e].mobileHandler.replaceWith(d[e].mobile),n.contains(document,d[e].offcanvasHandler[0])&&d[e].offcanvasHandler.replaceWith(d[e].offcanvas)):(n(document.body).removeClass("dj-megamenu-mobile"),n(document.body).removeClass(d[e].id+"-mobile"),n.contains(document,d[e].mobile[0])&&(d[e].mobile.after(d[e].mobileHandler),d[e].mobile.detach()),d[e].offcanvas&&n.contains(document,d[e].offcanvas[0])&&(d[e].offcanvas.after(d[e].offcanvasHandler),d[e].offcanvas.detach()),n.contains(document,d[e].menuHandler[0])&&d[e].menuHandler.replaceWith(d[e].menu)))},100)};n(document).ready(function(){n(".dj-megamenu:not(.dj-megamenu-sticky)").each(function(){var e=n(this),a=n("#"+e.prop("id")+"mobile"),o=n("#"+e.prop("id")+"offcanvas"),c=d.length;d[c]={},d[c].id=e.prop("id"),d[c].trigger=e.data("trigger"),d[c].menu=e,d[c].menuHandler=n("<div />"),d[c].mobile=a.length?a:null,d[c].mobileHandler=n("<div />"),d[c].offcanvas=o.length?o:null,d[c].offcanvasHandler=n("<div />");var s=n("#"+e.prop("id")+"mobileWrap");s.length&&s.empty().append(d[c].mobile),d[c].mobile&&(d[c].mobile.find(".dj-hideitem").remove(),d[c].mobile.hasClass("dj-megamenu-offcanvas")?i(d[c].mobile):d[c].mobile.hasClass("dj-megamenu-accordion")&&t(d[c].mobile))}),n(window).resize(s),s()}),n(window).one("load",function(){for(var a=0;a<d.length;a++)d[a].mobile&&d[a].mobile.hasClass("dj-megamenu-select")&&e(d[a].menu,d[a].mobile);n(".dj-offcanvas-close-btn").click()})}(jQuery);
