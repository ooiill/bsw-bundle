bsw.configure({
    data: {
        menuTheme: 'dark',
        menuWidth: 256,
        menuCollapsed: false,
        weak: 'no',
    },
    method: {
        themeSwitch(data, element) {
            let cnf = bsw.cnf;
            let menuTheme = cnf.menuTheme || this.menuTheme;
            this.menuTheme = bsw.cookieMapNext('bsw_theme', cnf.menuThemeMap, menuTheme, true, bsw.lang.theme);
        },

        colorWeakSwitch(data, element) {
            let cnf = bsw.cnf;
            let weak = cnf.weak || this.weak;
            this.weak = bsw.cookieMapNext('bsw_color_weak', cnf.opposeMap, weak, true, bsw.lang.color_weak);
            bsw.switchClass('bsw-weak', this.weak);
        },

        thirdMessageSwitch(data, element) {
            let cnf = bsw.cnf;
            let thirdMessage = cnf.thirdMessage || this.thirdMessage;
            cnf.thirdMessage = bsw.cookieMapNext('bsw_third_message', cnf.opposeMap, thirdMessage, true, bsw.lang.third_message);
        },

        menuTrigger(collapsed) {
            let cnf = bsw.cnf;
            if (typeof collapsed === 'undefined') {
                collapsed = typeof cnf.menuCollapsed !== 'undefined' ? cnf.menuCollapsed : this.menuCollapsed;
            }
            collapsed = bsw.cookieMapNext('bsw_menu_collapsed', cnf.opposeMap, collapsed ? 'yes' : 'no', true);
            this.menuCollapsed = (collapsed === 'yes');
            setTimeout(() => {
                $(window).resize();
            }, 300);
        },

        menuTriggerFooter(collapsed) {
            this.menuTrigger(collapsed);
        },

        changeLanguageByVue(event) {
            let key = $(event.item.$el).find('span').attr('lang');
            bsw.request(this.init.languageApiUrl, {key}).then((res) => {
                bsw.response(res).then(() => {
                    bsw.responseLogic(res);
                }).catch((reason => {
                    console.warn(reason);
                }));
            }).catch((reason => {
                console.warn(reason);
            }));
        },

        scaffoldInit() {
            let cnf = bsw.cnf;

            // theme
            let menuTheme = cnf.menuTheme || this.menuTheme;
            this.menuTheme = bsw.cookieMapCurrent('bsw_theme', cnf.menuThemeMap, menuTheme);

            // color weak
            let weak = cnf.weak || this.weak;
            this.weak = bsw.cookieMapCurrent('bsw_color_weak', cnf.opposeMap, weak);
            bsw.switchClass('bsw-weak', this.weak);

            // third message
            let thirdMessage = cnf.thirdMessage || this.thirdMessage;
            cnf.thirdMessage = bsw.cookieMapCurrent('bsw_third_message', cnf.opposeMap, thirdMessage);

            // menu
            this.menuWidth = cnf.menuWidth || this.menuWidth;
            let collapsed = bsw.cookieMapCurrent(
                'bsw_menu_collapsed',
                cnf.opposeMap,
                (typeof cnf.menuCollapsed === 'undefined') ? this.menuWidth : cnf.menuCollapsed
            );

            let menuCollapsed = (collapsed === 'yes');
            this.$nextTick(function () {
                this.menuCollapsed = menuCollapsed;
            });
            return menuCollapsed;
        }
    },
    logic: {
        thirdMessage(v) {
            let cnf = bsw.cnf;
            if (typeof cnf === 'undefined' || typeof cnf.thirdMessageSecond === 'undefined') {
                return;
            }
            if (cnf.thirdMessageSecond < 3) {
                return;
            }
            v.$nextTick(function () {
                setInterval(function () {
                    let tm = bsw.cookieMapCurrent('bsw_third_message', cnf.opposeMap, cnf.thirdMessage);
                    if (tm === 'no') {
                        return;
                    }
                    v.noLoadingOnce = true;
                    bsw.request(v.init.thirdMessageApiUrl).then((res) => {
                        if (res.error === 4967) {
                            return;
                        }
                        bsw.response(res).then(() => {
                            bsw.responseLogic(res);
                        }).catch((reason => {
                            console.warn(reason);
                        }));
                    }).catch((reason => {
                        console.warn(reason);
                    }));
                }, cnf.thirdMessageSecond * 1000);
            });
        }
    }
});