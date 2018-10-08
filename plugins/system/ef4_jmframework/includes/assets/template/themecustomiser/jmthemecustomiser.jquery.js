!function($,n){var o=window.JMThemeCustomiser=window.JMThemeCustomiser||{init:function(a){this.tpl_name=a},render:function(){this.wrapper=$('<div/>',{id:'jmthemewrapper',dir:'ltr'}).appendTo(document.body);this.toggler=$('<div/>',{id:'jmthemetoggler'}).appendTo(this.wrapper);this.formwrapper=$('<div/>',{id:'jmthemeform'}).appendTo(this.wrapper);this.toggler.click(function(){o.wrapper.toggleClass('active')});this.overlay=$('#jmthemeoverlay');$.ajax({async:false,url:o.url,data:{jmajax:'themer',jmtask:'display',jmthemerlogin:o.login_form,ts:new Date().getTime()}}).done(function(a){o.loadLayout(a)}).fail(function(a,b,c){alert("error: "+b)}).always(function(){setTimeout(function(){o.overlay.removeClass('visible')},100)});if($('#jm-styleswitcher')!=n){$('#jm-styleswitcher').hide()}return},loadLayout:function(d){this.formwrapper.html(d);this.form=$('#jmtheme');var e=$('#themecustomiser_templateStyle');if(e){e.change(function(){if(changeStyle!='undefined'){o.overlay.addClass('visible');var a={};a[o.getName(this.name)]=this.value;changeStyle(this.options[this.selectedIndex].value);o.setThemerState(JSON.stringify(a));document.location.reload()}})}$(document).find('div.jmtheme-set-inside').accordion({header:"h4",collapsible:true,heightStyle:"content"});$('#jmtheme').accordion({header:"h3",collapsible:true,heightStyle:"content",active: false});var f=o.getThemerState()||'';if(f&&f!=''){form_cache=JSON.parse(f);for(var g in form_cache){if(!form_cache.hasOwnProperty(g))continue;if(form_cache[g]!=''){form_cache[g]=form_cache[g].toString()}o.form.find('[type=text], [type=hidden], textarea, select').each(function(a,b){if(o.getName(b.name)==g&&form_cache[g]!=''){b.value=form_cache[g]}})}}var h=$(document).find('select.jmgooglefontselector');if(h){h.each(function(a,b){var c=b.selectedIndex>0?b.selectedIndex:0;if(b.options[c].value!=''){o.enableGoogleFont(b.options[c].value)}})}var i=$('#jmtheme-reset');if(i){i.click(function(a){a.preventDefault();o.overlay.addClass('visible');setTimeout(function(){o.setThemerState('');less.refresh(true);document.location.reload()},100);return false})}var j=$('#jmtheme-save');if(j){j.click(function(a){a.preventDefault();o.overlay.html('<p>'+o.lang.LANG_PLEASE_WAIT_SAVING+'</p>');o.overlay.addClass('visible');setTimeout(function(){o.saveSettings(false)},100);return})}var k=$('#jmtheme-save_file');if(k){k.click(function(a){a.preventDefault();o.overlay.html('<p>'+o.lang.LANG_PLEASE_WAIT_SAVING+'</p>');o.overlay.addClass('visible');setTimeout(function(){o.saveSettings(true)},100);return})}var l=$('#jmtheme-submit');if(l){var m=this;l.click(function(a){a.preventDefault();m.form.submit()})}this.form.submit(function(a){a.preventDefault();o.overlay.html('<p>'+o.lang.LANG_PLEASE_WAIT_APPLYING+'</p>');o.overlay.addClass('visible');setTimeout(function(){o.applyChanges();o.modifyVars()},100);return})},applyChanges:function(){var e=o.getCookie('JMTH_TIMESTAMP_'+o.tpl_name)||'';if(e==''||e==-1){o.overlay.html('<p>'+o.lang.LANG_PLEASE_WAIT_RELOADING+'</p>');setTimeout(function(){document.location.reload()},100);return false}var f=$(document).find('link');f.each(function(a,b){id=jQuery(b).attr('id');if(id!=null&&id.match(/^style[0-9]{1}$/)){b.destroy()}});o.vars={};o.cached_vars=o.lessVars||{};for(var m in o.lessVars){if(m.substring(0,2)=='JM'&&o.lessVars[m].replace(/[^0-9a-z\#,]/g,'')!=''){o.vars['@'+m]=o.lessVars[m]}}o.form.find('[type=text], [type=hidden], textarea, select').each(function(a,b){if(b.value){var c=b.name;var d=o.getName(c);if(b.value.replace(/[^0-9a-z]/gi,'')!=''){if(d.substring(0,2)=='JM'){o.vars['@'+d]=b.value}o.cached_vars[d]=b.value}}});return true},modifyVars:function(){if(o.vars&&less){o.setThemerState(JSON.stringify(o.cached_vars));setTimeout(function(){less.modifyVars(o.vars);o.overlay.removeClass('visible')},100)}else{o.overlay.removeClass('visible')}},saveSettings:function(d){if(this.applyChanges()==false){return false}var e=(d)?'save_file':'save';$.ajax({type:'POST',async:false,url:o.url,data:{jmajax:'themer',jmtask:e,jmstyleid:o.styleId,jmtemplatename:o.tpl_name,jmvars:o.cached_vars,ts:new Date().getTime()}}).done(function(a){alert(a);return}).fail(function(a,b,c){if(a.status==403){alert(o.lang.LANG_ERROR_FORBIDDEN);document.location.reload();return false}else if(a.status==401){alert(o.lang.LANG_ERROR_UNAUTHORISED);return false}else if(a.status==400){alert(o.lang.LANG_ERROR_BAD_REQUEST);return false}}).always(function(){o.overlay.removeClass('visible')})},getName:function(a){var b=a.match('themecustomiser\\[([^\\]]*)\\]');if(b){return b[1]}return''},setCookie:function(a,b,c){var d=new Date();d.setDate(d.getDate()+c);var e=escape(b)+((c==null)?"":"; expires="+d.toUTCString());e+="; path="+this.cookie.path;document.cookie=a+"="+e},getCookie:function(a){var b=document.cookie;var c=b.indexOf(" "+a+"=");if(c==-1){c=b.indexOf(a+"=")}if(c==-1){b=null}else{c=b.indexOf("=",c)+1;var d=b.indexOf(";",c);if(d==-1){d=b.length}b=unescape(b.substring(c,d))}return b},setThemerState:function(d){var e=false;$.ajax({type:'POST',async:false,url:o.url,data:{jmajax:'themer',jmtask:'set_state',jmstyleid:o.styleId,jmtemplatename:o.tpl_name,jmvars:d,ts:new Date().getTime()}}).done(function(a){e=true}).fail(function(a,b,c){e=false});return e},getThemerState:function(d){var e='';$.ajax({type:'POST',async:false,url:o.url,data:{jmajax:'themer',jmtask:'get_state',jmstyleid:o.styleId,jmtemplatename:o.tpl_name,ts:new Date().getTime()}}).done(function(a){e=a}).fail(function(a,b,c){e=''});return e},enableGoogleFont:function(c){if(!c||c==''){return false}var d=encodeURIComponent(c).replace(/%20/g,'+');var e='http://fonts.googleapis.com/css?family='+d;var f=false;jQuery(document).find('link').each(function(a,b){if(jQuery(b).getProperty('href')==e){f=true}});if(f){return true}var g=$('<link/>',{href:e,rel:'stylesheet',type:'text/css'}).appendTo(document.head);return true}}}(jQuery);