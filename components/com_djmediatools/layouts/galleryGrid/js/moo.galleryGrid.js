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
this.DJImageGalleryGrid=new Class({Implements:Options,options:{transition:Fx.Transitions.Expo.easeInOut,duration:250,delay:50},initialize:function(a,b){if(!(this.grid=document.id(a)))return;this.setOptions(b);this.slides=this.grid.getElements('.dj-slide');this.loaded=0;this.touch=(Browser.Platform.ios||Browser.Platform.android||Browser.Platform.webos);this.transition=this.support('transition');this.transform=this.options.effect=='ifade'?this.support('transform'):false;if(this.slides.length){this.responsive();this.setEffectsOptions();this.setSlidesEffects();this.loadSlides();this.setGridEvents();window.addEvent('resize',this.responsive.bind(this))}},responsive:function(){var a=this.getSize(this.grid).x;a-=this.grid.getStyle('padding-left').toInt();a-=this.grid.getStyle('padding-right').toInt();a-=this.grid.getStyle('border-left-width').toInt();a-=this.grid.getStyle('border-right-width').toInt();var b=Math.ceil(a/(this.options.width+this.options.spacing));var c=Math.floor((a-1)/b-this.options.spacing);this.slides.setStyle('width',c);this.slides.setStyle('height',(this.options.height*c/this.options.width)-1)},getSize:function(a){return a.measure(function(){return this.getSize()})},setEffectsOptions:function(){switch(this.options.effect){case'up':var a=Math.ceil(this.options.spacing*100/this.options.height);this.property="top";this.startEffect=(100+a)+'%';this.endEffect='0';break;case'down':var a=Math.ceil(this.options.spacing*100/this.options.height);this.property="top";this.startEffect=-1*(100+a)+'%';this.endEffect='0';break;case'left':var a=Math.ceil(this.options.spacing*100/this.options.width);this.property="left";this.startEffect=(100+a)+'%';this.endEffect='0';break;case'right':var a=Math.ceil(this.options.spacing*100/this.options.width);this.property="left";this.startEffect=-1*(100+a)+'%';this.endEffect='0';break;case'fade':default:this.property="opacity";this.startEffect=0;this.endEffect=1;break}if(this.options.desc_effect){switch(this.options.desc_effect){case'up':var a=Math.ceil(this.options.spacing*100/this.options.height);this.desc_property="margin-bottom";this.desc_startEffect=(100+a)+'%';this.desc_endEffect=0;break;case'down':var a=Math.ceil(this.options.spacing*100/this.options.height);this.desc_property="margin-bottom";this.desc_startEffect=-1*(100+a)+'%';this.desc_endEffect=0;break;case'left':var a=Math.ceil(this.options.spacing*100/this.options.width);this.desc_property="margin-left";this.desc_startEffect=-1*(100+a)+'%';this.desc_endEffect=0;break;case'right':var a=Math.ceil(this.options.spacing*100/this.options.width);this.desc_property="margin-left";this.desc_startEffect=(100+a)+'%';this.desc_endEffect=0;break;case'fade':default:this.desc_property="opacity";this.desc_startEffect=0;this.desc_endEffect=1;break}}},setSlidesEffects:function(){this.slides.each(function(d){d.fx=d.getElement('.dj-slide-in');if(this.transition){d.fx.setStyle(this.property,this.startEffect);var f=this.property+' '+this.options.duration+'ms '+'ease-out';if(this.transform){f+=', '+this.transform+' '+this.options.duration+'ms '+'ease-out';d.fx.setStyle(this.transform,'scale(0.3)')}d.fx.setStyle(this.transition,f);f='opacity '+this.options.duration+'ms '+'ease-out '+this.options.delay+'ms';if(this.transform)f+=', '+this.transform+' '+this.options.duration+'ms '+'ease-out '+this.options.delay+'ms';d.setStyle(this.transition,f)}else{d.set('tween',{property:'opacity',link:'cancel',transition:this.options.transition,duration:this.options.duration});d.fx.set('tween',{property:this.property,link:'cancel',transition:this.options.transition,duration:this.options.duration});d.fx.get('tween').set(this.startEffect)}d.desc=d.getElement('.dj-slide-desc');if(this.options.desc_effect&&d.desc){if(this.transition){d.desc.setStyle(this.desc_property,this.desc_startEffect);d.desc.setStyle(this.transition,this.desc_property+' '+this.options.duration+'ms '+'ease-in-out '+this.options.delay+'ms')}else{if(this.options.desc_effect=='fade')d.desc.set('tween',{property:this.desc_property,link:'cancel',transition:Fx.Transitions.Expo.easeInOut,duration:this.options.duration});else d.desc.set('tween',{property:this.desc_property,link:'cancel',transition:Fx.Transitions.Expo.easeInOut,duration:this.options.duration,unit:'%'});d.desc.get('tween').set(this.desc_startEffect)}}if(this.touch){var g=d.fx.getElement('img.dj-image');g.addEvent('click',function(e){if(this.options.desc_effect&&d.desc&&!d.hasClass('active')){this.slides.each(function(a){if(a.hasClass('active')&&a!=d)this.hideItem(a)}.bind(this));this.showItem(d);e.preventDefault();e.stopPropagation();this.grid.fireEvent('mouseenter')}}.bind(this))}else{d.addEvent('mouseenter',function(e){this.slides.each(function(a){if(a.hasClass('active')&&a!=d)this.hideItem(a)}.bind(this));this.showItem(d);this.grid.fireEvent('mouseenter')}.bind(this));d.addEvent('focus',function(e){var b=d.hasClass('active');this.slides.each(function(a){if(a.hasClass('active')&&a!=d)this.hideItem(a)}.bind(this));this.showItem(d);this.grid.fireEvent('mouseenter');d.getElements('.showOnMouseOver').fireEvent('mouseenter');var c=d.getElement('a[href]');if(!b&&c)c.focus()}.bind(this))}}.bind(this))},showItem:function(a){a.addClass('active');if(this.transition){a.setStyle('opacity',1);if(this.transform)a.setStyle(this.transform,'scale(1.1)')}else a.tween(1);if(this.options.desc_effect&&a.desc){if(this.transition)a.desc.setStyle(this.desc_property,this.desc_endEffect);else a.desc.get('tween').start(this.desc_endEffect)}},hideItem:function(a){a.removeClass('active');if(this.transition){a.setStyle('opacity',0.3);if(this.transform)a.setStyle(this.transform,'scale(1.0)')}else a.tween(0.3);if(this.options.desc_effect&&a.desc){if(this.transition)a.desc.setStyle(this.desc_property,this.desc_startEffect);else a.desc.get('tween').start(this.desc_startEffect)}},loadSlide:function(e,f){if(this.slides[e].loaded)return;this.slides[e].loaded=true;this.loaded++;var g=this.slides[e].getElement('img.dj-image');var h=0;if(f)h=e*this.options.delay;var j=function(b,c,d){if(b.length>1){c=b[1];d=b[2];b=b[0]}(function(i,a){if(i.length>1){a=i[1];i=i[0]}var s=this.slides[i];s.setStyle('background-image','none');if(this.property!='opacity')s.fx.setStyle('opacity',1);if(this.transition){s.fx.setStyle(this.property,this.endEffect);if(this.transform)s.fx.setStyle(this.transform,'scale(1.0)')}else{a.set('tween',{property:'max-width',link:'cancel',transition:this.options.transition,duration:this.options.duration,unit:'%'});a.get('tween').set(30);a.tween(100);s.fx.tween(this.endEffect)}}).delay(d,this,[c,b]);b.removeEvent('load',j)}.bind(this,[g,e,h]);g.removeProperty('src');g.addEvent('load',j);var k=g.getProperty('data-sizes'),srcset=g.getProperty('data-srcset'),src=g.getProperty('data-src');if(k){g.setProperty('sizes',k);g.removeProperty('data-sizes')}if(srcset){g.setProperty('srcset',srcset);g.removeProperty('data-srcset')}if(src){g.setProperty('src',src);g.removeProperty('data-src')}picturefill({elements:[g]})},loadSlides:function(){var d=window.getScroll().y+window.getSize().y;this.slides.each(function(a,b){if(a.getPosition().y<d){this.loadSlide(b,true)}}.bind(this));var e=function(){var c=window.getScroll().y+window.getSize().y;this.slides.each(function(a,b){if(a.getPosition().y<c){this.loadSlide(b,false)}}.bind(this));if(this.loaded==this.slides.length){window.removeEvent('scroll',e);window.removeEvent('resize',e)}}.bind(this);window.addEvent('scroll',e);window.addEvent('resize',e);window.addEvent('load',e)},setGridEvents:function(){this.elementsToShow=this.grid.getElements('.showOnMouseOver');this.elementsToShow.each(function(a){a.set('tween',{property:'opacity',duration:200,link:'cancel'});a.get('tween').set(0);a.addEvent('mouseenter',function(){this.tween(1)}.bind(a));a.addEvent('mouseleave',function(){this.tween(0.5)}.bind(a))}.bind(this));this.grid.addEvent('mouseenter',function(){this.slides.each(function(a,b){if(!a.hasClass('active')){if(this.transition)a.setStyle('opacity',0.3);else a.tween(0.3)}}.bind(this));this.elementsToShow.each(function(a){a.tween(0.5)}.bind(this))}.bind(this));this.grid.addEvent('mouseleave',function(){this.slides.each(function(a){if(a.hasClass('active'))this.hideItem(a)}.bind(this));if(this.transition)this.slides.setStyle('opacity',1);else this.slides.tween(1);this.elementsToShow.each(function(a){a.tween(0)}.bind(this))}.bind(this));this.grid.getElement('.dj-gallery-end').addEvent('focus',function(){this.grid.fireEvent('mouseleave')}.bind(this))},support:function(p){var b=document.body||document.documentElement,s=b.style;if(typeof s=='undefined')return false;if(typeof s[p]=='string')return p;v=['Moz','Webkit','Khtml','O','ms','Icab'],pu=p.charAt(0).toUpperCase()+p.substr(1);for(var i=0;i<v.length;i++){if(typeof s[v[i]+pu]=='string')return('-'+v[i].toLowerCase()+'-'+p)}}});