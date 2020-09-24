'use strict';

bsw.configure({
    data: {
        menuTheme: 'dark',
        menuWidth: 256,
        menuCollapsed: false,
        weak: 'no'
    },
    method: {
        themeSwitch: function themeSwitch(data, element) {
            var cnf = bsw.cnf;
            var menuTheme = cnf.menuTheme || this.menuTheme;
            this.menuTheme = bsw.cookieMapNext('bsw_theme', cnf.menuThemeMap, menuTheme, true, bsw.lang.theme);
        },
        colorWeakSwitch: function colorWeakSwitch(data, element) {
            var cnf = bsw.cnf;
            var weak = cnf.weak || this.weak;
            this.weak = bsw.cookieMapNext('bsw_color_weak', cnf.opposeMap, weak, true, bsw.lang.color_weak);
            bsw.switchClass('bsw-weak', this.weak);
        },
        thirdMessageSwitch: function thirdMessageSwitch(data, element) {
            var cnf = bsw.cnf;
            var thirdMessage = cnf.thirdMessage || this.thirdMessage;
            cnf.thirdMessage = bsw.cookieMapNext('bsw_third_message', cnf.opposeMap, thirdMessage, true, bsw.lang.third_message);
        },
        menuTrigger: function menuTrigger(collapsed) {
            var cnf = bsw.cnf;
            if (typeof collapsed === 'undefined') {
                collapsed = typeof cnf.menuCollapsed !== 'undefined' ? cnf.menuCollapsed : this.menuCollapsed;
            }
            collapsed = bsw.cookieMapNext('bsw_menu_collapsed', cnf.opposeMap, collapsed ? 'yes' : 'no', true);
            this.menuCollapsed = collapsed === 'yes';
            setTimeout(function () {
                $(window).resize();
            }, 300);
        },
        menuTriggerFooter: function menuTriggerFooter(collapsed) {
            this.menuTrigger(collapsed);
        },
        changeLanguageByVue: function changeLanguageByVue(event) {
            var key = $(event.item.$el).find('span').attr('lang');
            bsw.request(this.init.languageApiUrl, { key: key }).then(function (res) {
                bsw.response(res).then(function () {
                    bsw.responseLogic(res);
                }).catch(function (reason) {
                    console.warn(reason);
                });
            }).catch(function (reason) {
                console.warn(reason);
            });
        },
        scaffoldInit: function scaffoldInit() {
            var cnf = bsw.cnf;

            // theme
            var menuTheme = cnf.menuTheme || this.menuTheme;
            this.menuTheme = bsw.cookieMapCurrent('bsw_theme', cnf.menuThemeMap, menuTheme);

            // color weak
            var weak = cnf.weak || this.weak;
            this.weak = bsw.cookieMapCurrent('bsw_color_weak', cnf.opposeMap, weak);
            bsw.switchClass('bsw-weak', this.weak);

            // third message
            var thirdMessage = cnf.thirdMessage || this.thirdMessage;
            cnf.thirdMessage = bsw.cookieMapCurrent('bsw_third_message', cnf.opposeMap, thirdMessage);

            // menu
            this.menuWidth = cnf.menuWidth || this.menuWidth;
            var collapsed = bsw.cookieMapCurrent('bsw_menu_collapsed', cnf.opposeMap, typeof cnf.menuCollapsed === 'undefined' ? this.menuWidth : cnf.menuCollapsed);

            var menuCollapsed = collapsed === 'yes';
            this.$nextTick(function () {
                this.menuCollapsed = menuCollapsed;
            });
            return menuCollapsed;
        }
    },
    logic: {
        thirdMessage: function thirdMessage(v) {
            var cnf = bsw.cnf;
            if (typeof cnf === 'undefined' || typeof cnf.thirdMessageSecond === 'undefined') {
                return;
            }
            if (cnf.thirdMessageSecond < 3) {
                return;
            }
            v.$nextTick(function () {
                setInterval(function () {
                    var tm = bsw.cookieMapCurrent('bsw_third_message', cnf.opposeMap, cnf.thirdMessage);
                    if (tm === 'no') {
                        return;
                    }
                    v.noLoadingOnce = true;
                    bsw.request(v.init.thirdMessageApiUrl).then(function (res) {
                        if (res.error === 4967) {
                            return;
                        }
                        bsw.response(res).then(function () {
                            bsw.responseLogic(res);
                        }).catch(function (reason) {
                            console.warn(reason);
                        });
                    }).catch(function (reason) {
                        console.warn(reason);
                    });
                }, cnf.thirdMessageSecond * 1000);
            });
        }
    }
});
