/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2021-05-12 */
"use strict";bsw.configure({data:{menuTheme:"dark",menuWidth:256,menuCollapsed:!1,weak:"no"},method:{themeSwitch:function(a,b){var c=bsw.cnf,d=c.menuTheme||this.menuTheme;this.menuTheme=bsw.cookieMapNext("bsw_theme",c.menuThemeMap,d,!0,bsw.lang.theme)},colorWeakSwitch:function(a,b){var c=bsw.cnf,d=c.weak||this.weak;this.weak=bsw.cookieMapNext("bsw_color_weak",c.opposeMap,d,!0,bsw.lang.color_weak),bsw.switchClass("bsw-weak",this.weak)},thirdMessageSwitch:function(a,b){var c=bsw.cnf,d=c.thirdMessage||this.thirdMessage;c.thirdMessage=bsw.cookieMapNext("bsw_third_message",c.opposeMap,d,!0,bsw.lang.third_message)},menuTrigger:function(a){var b=bsw.cnf;"undefined"==typeof a&&(a="undefined"!=typeof b.menuCollapsed?b.menuCollapsed:this.menuCollapsed),a=bsw.cookieMapNext("bsw_menu_collapsed",b.opposeMap,a?"yes":"no",!0),this.menuCollapsed="yes"===a,setTimeout(function(){$(window).resize()},300)},menuTriggerFooter:function(a){this.menuTrigger(a)},changeLanguageByVue:function(a){var b=$(a.item.$el).find("span").attr("lang");bsw.request(this.init.languageApiUrl,{key:b}).then(function(a){bsw.response(a).then(function(){bsw.responseLogic(a)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)})},scaffoldInit:function(){var a=bsw.cnf,b=a.menuTheme||this.menuTheme;this.menuTheme=bsw.cookieMapCurrent("bsw_theme",a.menuThemeMap,b);var c=a.weak||this.weak;this.weak=bsw.cookieMapCurrent("bsw_color_weak",a.opposeMap,c),bsw.switchClass("bsw-weak",this.weak);var d=a.thirdMessage||this.thirdMessage;a.thirdMessage=bsw.cookieMapCurrent("bsw_third_message",a.opposeMap,d),this.menuWidth=a.menuWidth||this.menuWidth;var e=bsw.cookieMapCurrent("bsw_menu_collapsed",a.opposeMap,"undefined"==typeof a.menuCollapsed?this.menuWidth:a.menuCollapsed),f="yes"===e;return this.$nextTick(function(){this.menuCollapsed=f}),f}},logic:{thirdMessage:function(a){var b=bsw.cnf;"undefined"!=typeof b&&"undefined"!=typeof b.thirdMessageSecond&&(b.thirdMessageSecond<3||a.$nextTick(function(){setInterval(function(){var c=bsw.cookieMapCurrent("bsw_third_message",b.opposeMap,b.thirdMessage);"no"!==c&&(a.noLoadingOnce=!0,bsw.request(a.init.thirdMessageApiUrl).then(function(a){4967!==a.error&&bsw.response(a).then(function(){bsw.responseLogic(a)}).catch(function(a){console.warn(a)})}).catch(function(a){console.warn(a)}))},1e3*b.thirdMessageSecond)}))}}});