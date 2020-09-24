//
// For ck-editor 5
//

class FileUploadAdapter {

    constructor(editor, loader, api) {
        this.editor = editor;
        this.loader = loader;
        this.api = api;
    }

    upload() {
        return this.loader.file.then(file => new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('ck-editor', file);
            data.append('file_flag', 'ck-editor');
            bsw.request(this.api, data, null, true).then(function (res) {
                if (res.error) {
                    reject(res.message);
                } else {
                    resolve({default: res.sets.attachment_url});
                }
            });
        }));
    }

    abort() {
    }
}