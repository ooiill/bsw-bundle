'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

//
// For ck-editor 5
//

var FileUploadAdapter = function () {
    function FileUploadAdapter(editor, loader, api) {
        _classCallCheck(this, FileUploadAdapter);

        this.editor = editor;
        this.loader = loader;
        this.api = api;
    }

    _createClass(FileUploadAdapter, [{
        key: 'upload',
        value: function upload() {
            var _this = this;

            return this.loader.file.then(function (file) {
                return new Promise(function (resolve, reject) {
                    var data = new FormData();
                    data.append('ck-editor', file);
                    data.append('file_flag', 'ck-editor');
                    bsw.request(_this.api, data, null, true).then(function (res) {
                        if (res.error) {
                            reject(res.message);
                        } else {
                            resolve({ default: res.sets.attachment_url });
                        }
                    });
                });
            });
        }
    }, {
        key: 'abort',
        value: function abort() {}
    }]);

    return FileUploadAdapter;
}();
