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

        bsw,
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
        },

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

        redirectByVue(event) {
            bsw.redirect(bsw.getBswData($(event.item.$el).find('span')));
        },

        dispatcherByNative(element) {
            bsw.dispatcherByBswData(bsw.getBswData($(element)), element);
        },

        dispatcherByVue(event) {
            this.dispatcherByNative($(event.target)[0])
        },

        showIFrameByNative(element) {
            bsw.showIFrame(bsw.getBswData($(element)), element);
        },

        showIFrameByVue(event) {
            this.showIFrameByNative($(event.target)[0])
        },

        tabsLinksSwitch(key) {
            bsw.redirect(bsw.getBswData($(`#tabs_link_${key}`)));
        },

    }, bsw.config.method || {})).directive(Object.assign({

        // directive
        init: {
            bind: function (el, binding, vnode) {
                let keyFlag = '';
                if (binding.arg.startsWith('data-')) {
                    keyFlag = 'data';
                    binding.arg = binding.arg.substr(5);
                }

                let key = bsw.smallHump(binding.arg, '-');
                let value = (binding.value || binding.expression);
                if (key === 'configure') {
                    bsw.cnf = Object.assign(bsw.cnf, value);
                } else if (keyFlag === 'data') {
                    let children = null;
                    if (key.indexOf(':') > -1) {
                        [key, children] = key.split(':');
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
        },

    }, bsw.config.directive || {})).watch(Object.assign({}, bsw.config.watch || {})).component(Object.assign({

        // component
        'b-icon': bsw.d.Icon.createFromIconfontCN({
            // /bundles/leonbsw/dist/js/iconfont.js
            scriptUrl: $('#var-font-symbol').data('bsw-value')
        }),

    }, bsw.config.component || {})).init(function (v) {

        bsw.initClipboard('.bsw-copy');
        bsw.initVConsole();

        setTimeout(function () {
            bsw.messageAutoDiscovery(v.init);
        }, 100);

    });
});

// -- eof --
