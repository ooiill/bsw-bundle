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
        timeFormat: 'YYYY-MM-DD HH:mm:ss',
        submitFormUrl: null,
        submitFormMethod: null,
        ckEditor: {},
        loadTimes: 1,
        noLoadingOnce: false,
        spinning: false,
        vConsole: null,
        constTrue: true,
        constFalse: false,
        init: { // from v-init
            configure: {},
            message: {},
            modal: {},
            result: {}
        },
        footer: 'footer',
        modal: {},
        modalMeta: {
            visible: true,
            centered: true,
        },
        drawer: {},
        drawerMeta: {
            visible: true,
        },
        result: {},
        resultMeta: {
            visible: true,
        },

        WH: bsw.getWH(),

    }, bsw.config.data)).computed(Object.assign({}, bsw.config.computed || {})).method(Object.assign({

        moment,

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

        selectedRowHandler(field) {
            let rows = [];
            for (let i = 0; i < this.previewSelectedRow.length; i++) {
                if (bsw.isString(this.previewSelectedRow[i])) {
                    rows[i] = bsw.evalExpr(this.previewSelectedRow[i]);
                    if (field) {
                        rows[i] = rows[i][field] || null;
                    }
                }
            }
            return rows;
        },

        multipleAction(data, element) {
            let that = this;
            let ids = this.selectedRowHandler();
            if (ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            bsw.request(data.location, {ids: ids}).then(res => {
                bsw.response(res).then(() => {
                    bsw.responseLogic(res);
                }).catch(reason => {
                    console.warn(reason);
                });
                if (typeof data.refresh !== 'undefined' && data.refresh) {
                    that.previewPaginationRefresh(false);
                }
            }).catch(reason => {
                console.warn(reason);
            });
        },

        showIFrameWithChecked(data, element) {
            let ids = this.selectedRowHandler(data.selector).join(',');
            let args = {ids};
            if (typeof data.form !== 'undefined') {
                let key = `fill[${data.form}]`;
                args = {[key]: ids};
            }
            data.location = bsw.setParams(args, data.location);
            bsw.showIFrame(data, element);
        },

        fillParentForm(data, element) {
            data.ids = this.selectedRowHandler(data.selector).join(',');
            if (data.ids.length === 0) {
                return bsw.warning(bsw.lang.select_item_first);
            }
            parent.postMessage(data, '*');
        },

        verifyJsonFormat(data, element, form = 'persistenceForm') {
            let json = this[form].getFieldValue(data.field);
            let url = bsw.setParams({[data.key]: json}, data.url);
            window.open(url);
        },

        setUrlToForm(data, element) {
            this.submitFormUrl = data.location;
            this.submitFormMethod = $(element).attr('bsw-method');
        },

        previewGetUrl(url, params = {}) {
            url = url || this.previewUrl;
            return bsw.setParams(Object.assign({page: this.previewPageNumber}, params), url);
        },

        previewPaginationRefresh(jump) {
            this.noLoadingOnce = true;
            this.pagination(this.previewGetUrl(), null, jump);
        },

        previewImageChange() {
            let that = this;
            if (that.previewColumns.length === 0) {
                return;
            }
            let doChecker = setInterval(() => checker(), 50);
            let checker = function () {
                let img = $('img');
                let done = 0;
                img.each(function () {
                    done += (this.complete ? 1 : 0);
                });
                let tmp = that.previewColumns[0].fixed;
                that.previewColumns[0].fixed = !tmp;
                that.previewColumns[0].fixed = tmp;
                if ((done >= img.length) || img.length === 0) {
                    clearInterval(doChecker);
                }
            }
        },

        pagination(url, page, jump = false) {
            let that = this;
            if (page) {
                url = bsw.setParams({page}, url);
            }
            if (jump || typeof that.previewList === 'undefined' || that.previewList.length === 0) {
                return location.href = url;
            }
            bsw.request(url).then(res => {
                bsw.response(res).then(() => {
                    that.previewList = res.sets.preview.list;
                    that.previewPageNumber = page;
                    that.previewUrl = url;
                    that.previewPaginationData = res.sets.preview.page;
                    that.previewImageChange();
                    bsw.responseLogic(res);
                    bsw.cnf.v.$nextTick(function () {
                        bsw.initUpwardInfect();
                        that.doubleClickForEdit();
                    })
                    history.replaceState({}, '', url);
                }).catch(reason => {
                    console.warn(reason);
                });
            }).catch(reason => {
                console.warn(reason);
            });
        },

        filterFormAction(event, jump = false, form, dateFormat) {
            let that = this;
            event.preventDefault();
            that[form].validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        let format = values[field]._f || that[dateFormat][field];
                        values[field] = values[field].format(format);
                        jump = true; // fix bug for ant-d
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                let format = values[field][i]._f || that[dateFormat][field];
                                values[field][i] = values[field][i].format(format);
                                jump = true; // fix bug for ant-d
                            }
                        }
                    }
                }
                let _values = {};
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (typeof values[field] === 'undefined') {
                        continue;
                    }
                    if (values[field] == null) {
                        continue;
                    }
                    if (values[field].length === 0) {
                        continue;
                    }
                    _values[field] = values[field];
                }
                return that[`${that.submitFormMethod}FilterForm`](_values, jump);
            });
        },

        searchFilterForm(values, jump = false) {
            let effect = {};
            let url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url, false, effect);
            url = bsw.setParams({filter: values}, url);
            if (typeof effect.page && effect.page > 1) {
                jump = true;
            }
            this.pagination(url, null, jump);
        },

        exportFilterForm(values) {
            let that = this;
            let url = bsw.unsetParamsBeginWith(['filter']);
            url = bsw.unsetParams(['page'], url);
            url = bsw.setParams({filter: values, scene: 'export'}, url);

            bsw.request(url).then(res => {
                bsw.response(res).then(() => {
                    let data = {
                        title: bsw.lang.export_mission,
                        width: 500,
                        height: 385,
                    };
                    data.location = bsw.setParams(res.sets, that.init.exportApiUrl, true);
                    bsw.showIFrame(data, $('body')[0]);
                    bsw.responseLogic(res);
                }).catch(reason => {
                    console.warn(reason);
                });
            }).catch(reason => {
                console.warn(reason);
            });
        },

        submitFormAction(event, form, dateFormat) {
            let that = this;
            event.preventDefault();
            that[form].validateFields((err, values) => {
                if (err) {
                    return false;
                }
                // logic
                for (let field in values) {
                    if (!values.hasOwnProperty(field)) {
                        continue;
                    }
                    if (moment.isMoment(values[field])) {
                        let format = values[field]._f || that[dateFormat][field];
                        values[field] = values[field].format(format);
                    }
                    if (bsw.isArray(values[field])) {
                        for (let i = 0; i < values[field].length; i++) {
                            if (moment.isMoment(values[field][i])) {
                                let format = values[field][i]._f || that[dateFormat][field];
                                values[field][i] = values[field][i].format(format);
                            }
                        }
                    }
                    if (bsw.checkJsonDeep(values, `${field}.fileList`)) {
                        delete values[field];
                    }
                }
                let extraValuesKey = `${form}ExtraValues`;
                if (typeof that[extraValuesKey] !== 'undefined') {
                    values = Object.assign(values, that[extraValuesKey]);
                }
                return that[`${that.submitFormMethod}PersistenceForm`](values);
            });
        },

        submitPersistenceForm(values) {
            bsw.request(this.submitFormUrl, {submit: values}).then(res => {
                let params = bsw.parseQueryString();
                if (params.iframe) {
                    res.sets.arguments = bsw.parseQueryString();
                    let fn = res.sets.function || 'handleResponse';
                    parent.postMessage({response: res, function: fn}, '*');
                } else {
                    bsw.response(res).then(() => {
                        bsw.responseLogic(res);
                    }).catch(reason => {
                        console.warn(reason);
                    });
                }
            }).catch(reason => {
                console.warn(reason);
            });
        },

        uploaderChange({file, fileList}, field, form = 'persistenceForm') {
            if (file.status === 'done') {
                this.spinning = false;
            } else if (file.status === 'uploading') {
                this.spinning = true;
            }

            let collect = this.persistenceFileListKeyCollect[field];
            if (!file.response) {
                collect.list = fileList;
                return;
            }
            if (file.response.error) {
                collect.list = fileList.slice(0, -1);
            }

            let files = collect.list.slice(-1);
            if (files.length) {
                let sets = files[0].response.sets;
                let map = {
                    [collect.id]: 'attachment_id',
                    [collect.md5]: 'attachment_md5',
                    [collect.sha1]: 'attachment_sha1',
                    [collect.url]: 'attachment_url',
                };
                for (let key in map) {
                    if (!map.hasOwnProperty(key)) {
                        continue;
                    }
                    if (key && map[key]) {
                        if ($(`#${key}`).length === 0) {
                            continue;
                        }
                        if (this[form]) {
                            this[form].setFieldsValue({[key]: sets[map[key]]});
                        }
                    }
                }
            }

            if (typeof file.response.code === 'undefined' || file.response.code === 500) {
                this.spinning = false;
            }

            if (file.response.sets.href) {
                let fn = file.response.sets.function || 'handleResponse';
                parent.postMessage({response: file.response, function: fn}, '*');
            } else {
                bsw.response(file.response).then(() => {
                    bsw.responseLogic(file.response);
                }).catch(reason => {
                    console.warn(reason);
                });
            }
        },

        changeTriggerHideForInput(event, field) {
            this.changeTriggerHide(event.target.value, {}, field)
        },

        changeTriggerHideForSelect(value, option, field) {
            this.changeTriggerHide(value, field)
        },

        changeTriggerHide(value, field) {
            let now = this.persistenceFieldHideNow;
            let collect = this.persistenceFieldHideCollect[field];
            for (let f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = (collect[f].includes(value.toString()));
            }
        },

        changeTriggerDisabledForInput(event, field) {
            this.changeTriggerDisabled(event.target.value, {}, field)
        },

        changeTriggerDisabledForSelect(value, option, field) {
            this.changeTriggerDisabled(value, field)
        },

        changeTriggerDisabled(value, field) {
            let now = this.persistenceFieldDisabledNow;
            let collect = this.persistenceFieldDisabledCollect[field];
            for (let f in collect) {
                if (!collect.hasOwnProperty(f)) {
                    continue;
                }
                now[f] = (collect[f].includes(value.toString()));
            }
        },

        requestByAjax(data, element) {
            let that = this;
            if (typeof data.confirmIsCheck !== 'undefined') {
                data.location = bsw.setParams({checkbox: data.confirmIsCheck ? 1 : 0}, data.location);
            }
            bsw.request(data.location, data).then(res => {
                bsw.response(res).then(() => {
                    bsw.responseLogic(res);
                }).catch(reason => {
                    console.warn(reason);
                });
                if (typeof data.refresh !== 'undefined' && data.refresh) {
                    that.previewPaginationRefresh(false);
                }
            }).catch(reason => {
                console.warn(reason);
            });
        },

        urlParamsTrigger(data) {
            location.href = bsw.triggerParams(data.params, null, true);
        },

        doubleClickForEdit(selector = '.bsw-inline-edit-input') {
            let that = this;
            let save = function (ele, inp) {
                let args = bsw.getBswData(ele);
                args.postValue = inp.val();
                that.noLoadingOnce = true;
                bsw.request(ele.data('api'), args).then(res => {
                    bsw.response(res).then(() => {
                        bsw.responseLogic(res);
                    }).catch(reason => {
                        console.warn(reason);
                    });
                    ele.removeClass('active').attr('title', bsw.lang.double_click_for_edit);
                    inp.attr('disabled', true);
                    if (typeof args.refresh !== 'undefined' && args.refresh) {
                        that.previewPaginationRefresh(false);
                    }
                    if (typeof res.sets.args !== 'undefined') {
                        ele.data('bsw', res.sets.args);
                    }
                }).catch(reason => {
                    console.warn(reason);
                });
            };
            $(selector).each(function () {
                let element = $(this);
                element.off('dblclick').on('dblclick', () => {
                    element.addClass('active').attr('title', bsw.lang.enter_for_save);
                    let input = element.find('input')
                    input.attr('disabled', false).focus();
                    input.off('keypress').on('keypress', (event) => {
                        if (event.keyCode === 13) {
                            save(element, input)
                        }
                    })
                });
            });
        },

        dynamicDataSourceBySearch(value, field) {
            let that = this;
            if (typeof this.ddsCore === 'undefined') {
                this.ddsCore = bsw.debounce(200, function (args) {
                    let item = $(`#${args.field}`);
                    let ddsApi = item.data('do-logic-api');
                    let ddsField = item.data('do-dds-field');
                    that.noLoadingOnce = true;
                    bsw.request(ddsApi, args).then(res => {
                        bsw.response(res).then(() => {
                            if (ddsField && res.sets) {
                                bsw.setJsonDeep(that, ddsField, res.sets);
                                let changeField = ddsField.split('.');
                                changeField = changeField[changeField.length - 1];
                                that.persistenceForm.setFieldsValue({[changeField]: undefined})
                            }
                            bsw.responseLogic(res);
                        }).catch(reason => {
                            console.warn(reason);
                        });
                    }).catch(reason => {
                        console.warn(reason);
                    });
                });
            }
            this.ddsCore({value, field});
        },

        dynamicDataSourceByChange(value, options, field) {
            let that = this;
            let item = $(`#${field}`);
            let ddsApi = item.data('do-logic-api');
            let ddsField = item.data('do-dds-field');
            that.noLoadingOnce = true;
            bsw.request(ddsApi, {value, field}).then(res => {
                bsw.response(res).then(() => {
                    if (ddsField && res.sets) {
                        bsw.setJsonDeep(that, ddsField, res.sets);
                        let changeField = ddsField.split('.');
                        changeField = changeField[changeField.length - 1];
                        that.persistenceForm.setFieldsValue({[changeField]: undefined})
                    }
                    bsw.responseLogic(res);
                }).catch(reason => {
                    console.warn(reason);
                });
            }).catch(reason => {
                console.warn(reason);
            });
        },

        dynamicValueByChange(value, options, field) {
            let that = this;
            let item = $(`#${field}`);
            let ddsApi = item.data('do-logic-api');
            let changeField = item.data('do-change-field');
            that.noLoadingOnce = true;
            bsw.request(ddsApi, {value, field}).then(res => {
                bsw.response(res).then(() => {
                    if (changeField && res.sets) {
                        that.persistenceForm.setFieldsValue({[changeField]: res.sets.change});
                    }
                    bsw.responseLogic(res);
                }).catch(reason => {
                    console.warn(reason);
                });
            }).catch(reason => {
                console.warn(reason);
            });
        },

        copyFileLink(data, element) {
            this.copy = data.link;
        },

        getFormDataByEvent(event) {
            let field = event.target.id;
            let value = event.target.value;
            let data = bsw.getBswData($(event.target));
            let form = data.form || 'persistenceForm';

            return {field, value, data, form};
        },

        refreshPreviewForParent() {
            parent.postMessage({function: 'refreshPreview'}, '*');
        },

        refreshPreviewInParent(data, element) {
            bsw.handleResponseInParent(data, element);
            this.previewPaginationRefresh(false);
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
                    if (children && children.length > 0) {
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

        let change = false;
        if (v.scaffoldInit) {
            change = v.scaffoldInit();
        }

        bsw.initClipboard();
        bsw.initUpwardInfect();
        v.doubleClickForEdit();
        bsw.initHighlight();
        bsw.initVConsole();

        let timeout = change ? 1200 : 600;
        setTimeout(function () {
            bsw.initScrollX();
            $(window).resize();
            let operates = function () {
                bsw.messageAutoDiscovery(v.init);
                bsw.autoIFrameHeight();
                v.loadTimes += 1;
            };
            let loadingDiv = $('.bsw-page-loading');
            if (loadingDiv.length) {
                loadingDiv.fadeOut(300, () => operates());
            } else {
                operates();
            }
            v.WH = bsw.getWH()
        }, timeout);
    });

    window.addEventListener('message', function (event) {
        event.data.function += 'InParent';
        bsw.dispatcherByBswData(event.data, $('body')[0]);
    }, false)
});

// -- eof --
