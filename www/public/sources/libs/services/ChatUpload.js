export default class ChatUpload extends chat.services.Upload {
    configUpload(file) {
        var back = this.app.getService("backend");
        this.uploader.config.upload =
            this._mode == "file"
                ? back.fileUploadUrl(this._chat)
                : back.avatarUploadUrl(this._chat);
        if (file.size > this.maxFileSize) {
            this.app.callEvent("onSizeExceed", [file]);
            return false;
        }
    };
}