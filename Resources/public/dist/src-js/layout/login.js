'use strict';

bsw.configure({
    data: {
        loginForm: null,
        btnLoading: false,
        formItems: {},
        submitFormMethod: 'doLogin',
        rsaPublicKey: '-----BEGIN PUBLIC KEY-----\nMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCyhl+6jZ/ENQvs24VpT4+o7Ltc\nB4nFBZ9zYSeVbqYHaXMVpFSZTpAKkgqoy2R9kg7lM6QWnpDcVIPlbE6iqzzJ4Zm5\nIZ18C43C4jhtcNncjY6HRDTykkgul8OX2t6eJrRhRcWFYI7ygoYMZZ7vEfHImsXH\nNydhxUEs0y8aMzWbGwIDAQAB\n-----END PUBLIC KEY-----'
    },
    method: {
        userLogin: function userLogin(e) {
            this.submitFormAction(e, 'loginForm');
        },
        doLoginPersistenceForm: function doLoginPersistenceForm(login) {
            var _this = this;

            if (!login.account.length) {
                this.btnLoading = true;
                return bsw.error(bsw.lang.username_required, 3).then(function () {
                    return _this.btnLoading = false;
                });
            }

            if (this.formItems.needPassword) {
                if (!login.password.length) {
                    this.btnLoading = true;
                    return bsw.error(bsw.lang.password_required, 3).then(function () {
                        return _this.btnLoading = false;
                    });
                }
                if (login.password.length < 8 || login.password.length > 20) {
                    this.btnLoading = true;
                    return bsw.warning(bsw.lang.password_length_error, 3).then(function () {
                        return _this.btnLoading = false;
                    });
                }
            }

            if (!login.captcha.length) {
                this.btnLoading = true;
                return bsw.error(bsw.lang.captcha_required, 3).then(function () {
                    return _this.btnLoading = false;
                });
            }

            if (this.formItems.needGoogleCaptcha && !login.google_captcha.length) {
                this.btnLoading = true;
                return bsw.error(bsw.lang.google_captcha_required, 3).then(function () {
                    return _this.btnLoading = false;
                });
            }

            login.password = login.password ? bsw.rsaEncrypt(login.password) : login.password;
            bsw.request(this.init.loginApiUrl, login).then(function (res) {
                _this.btnLoading = true;
                if (res.error === 4901) {
                    $("img.bsw-captcha").click();
                }
                bsw.response(res, 2).then(function () {
                    bsw.responseLogic(res);
                }).catch(function () {
                    _this.btnLoading = false;
                });
            });
        }
    },
    logic: {
        createLoginForm: function createLoginForm(v) {
            v.loginForm = v.$form.createForm(v);
        }
    }
});
