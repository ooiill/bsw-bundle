/*! Anyone */
/*! BackendSteeringWheel4AntD - v0.0.1 - 2021-05-11 */
"use strict";var _slicedToArray=function(){function a(a,b){var c=[],d=!0,e=!1,f=void 0;try{for(var g,h=a[Symbol.iterator]();!(d=(g=h.next()).done)&&(c.push(g.value),!b||c.length!==b);d=!0);}catch(a){e=!0,f=a}finally{try{!d&&h.return&&h.return()}finally{if(e)throw f}}return c}return function(b,c){if(Array.isArray(b))return b;if(Symbol.iterator in Object(b))return a(b,c);throw new TypeError("Invalid attempt to destructure non-iterable instance")}}();window.bsw=new FoundationAntD(jQuery,Vue,antd,window.lang||{}),$(function(){bsw.vue().template(bsw.config.template||null).data(Object.assign({bsw:bsw,locale:bsw.d.locales[bsw.lang.i18n_ant],noLoadingOnce:!1,spinning:!1,ckEditor:{},vConsole:null,init:{configure:{},message:{},modal:{},result:{}}},bsw.config.data)).computed(Object.assign({},bsw.config.computed||{})).method(Object.assign({redirectByVue:function(a){bsw.redirect(bsw.getBswData($(a.item.$el).find("span")))},dispatcherByNative:function(a){bsw.dispatcherByBswData(bsw.getBswData($(a)),a)},dispatcherByVue:function(a){this.dispatcherByNative($(a.target)[0])},showIFrameByNative:function(a){bsw.showIFrame(bsw.getBswData($(a)),a)},showIFrameByVue:function(a){this.showIFrameByNative($(a.target)[0])},tabsLinksSwitch:function(a){bsw.redirect(bsw.getBswData($("#tabs_link_"+a)))}},bsw.config.method||{})).directive(Object.assign({init:{bind:function(a,b,c){var d="";b.arg.startsWith("data-")&&(d="data",b.arg=b.arg.substr(5));var e=bsw.smallHump(b.arg,"-"),f=b.value||b.expression;if("configure"===e)bsw.cnf=Object.assign(bsw.cnf,f);else if("data"===d){var g=null;if(e.indexOf(":")>-1){var h=e.split(":"),i=_slicedToArray(h,2);e=i[0],g=i[1]}e&&e.length>0?c.context[e][bsw.lcFirst(g)]=f:c.context[e]=f}else c.context.init[e]=f}}},bsw.config.directive||{})).watch(Object.assign({},bsw.config.watch||{})).component(Object.assign({"b-icon":bsw.d.Icon.createFromIconfontCN({scriptUrl:$("#var-font-symbol").data("bsw-value")})},bsw.config.component||{})).init(function(a){bsw.initClipboard(".bsw-copy"),bsw.initVConsole(),setTimeout(function(){bsw.messageAutoDiscovery(a.init)},100)})});