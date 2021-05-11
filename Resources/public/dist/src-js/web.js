'use strict';

var _slicedToArray = function () { function sliceIterator(arr, i) { var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"]) _i["return"](); } finally { if (_d) throw _e; } } return _arr; } return function (arr, i) { if (Array.isArray(arr)) { return arr; } else if (Symbol.iterator in Object(arr)) { return sliceIterator(arr, i); } else { throw new TypeError("Invalid attempt to destructure non-iterable instance"); } }; }();

//
// Copyright 2019
//

//
// Register global
//

window.bsw = new FoundationAntD(jQuery, Vue, antd, window.lang || {});

//
// Init
//

$(function () {
    // vue
    bsw.vue().template(bsw.config.template || null).data(Object.assign({

        bsw: bsw,
        locale: bsw.d.locales[bsw.lang.i18n_ant],
        noLoadingOnce: false,
        spinning: false,
        ckEditor: {},
        vConsole: null,
        init: { // from v-init
            configure: {},
            message: {},
            modal: {},
            result: {}
        }

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({
        redirectByVue: function redirectByVue(event) {
            bsw.redirect(bsw.getBswData($(event.item.$el).find('span')));
        },
        dispatcherByNative: function dispatcherByNative(element) {
            bsw.dispatcherByBswData(bsw.getBswData($(element)), element);
        },
        dispatcherByVue: function dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0]);
        },
        showIFrameByNative: function showIFrameByNative(element) {
            bsw.showIFrame(bsw.getBswData($(element)), element);
        },
        showIFrameByVue: function showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0]);
        },
        tabsLinksSwitch: function tabsLinksSwitch(key) {
            bsw.redirect(bsw.getBswData($('#tabs_link_' + key)));
        }
    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function bind(el, binding, vnode) {
                var keyFlag = '';
                if (binding.arg.startsWith('data-')) {
                    keyFlag = 'data';
                    binding.arg = binding.arg.substr(5);
                }

                var key = bsw.smallHump(binding.arg, '-');
                var value = binding.value || binding.expression;
                if (key === 'configure') {
                    bsw.cnf = Object.assign(bsw.cnf, value);
                } else if (keyFlag === 'data') {
                    var children = null;
                    if (key.indexOf(':') > -1) {
                        var _key$split = key.split(':');

                        var _key$split2 = _slicedToArray(_key$split, 2);

                        key = _key$split2[0];
                        children = _key$split2[1];
                    }
                    if (key && key.length > 0) {
                        vnode.context[key][bsw.lcFirst(children)] = value;
                    } else {
                        vnode.context[key] = value;
                    }
                } else {
                    vnode.context.init[key] = value;
                }
            }
        }

    }, bsw.config.directive || {})).watch(Object.assign({}, bsw.config.watch || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
        })

    }, bsw.config.component || {})).init(function (v) {

        bsw.initClipboard('.bsw-copy');
        bsw.initVConsole();

        setTimeout(function () {
            bsw.messageAutoDiscovery(v.init);
        }, 100);
    });
});

// -- eof --
