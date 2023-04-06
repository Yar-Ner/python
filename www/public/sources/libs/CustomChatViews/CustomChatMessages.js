export default class CustomChatMessages extends chat.views["messages"] {
     init(view) {
         super.init(view);
     }

    config() {
        const config = super.config();
        config.rows[1].css = "custom_comments"
        return config;
    }

    FormConfig(config) {
        var _this = this;
        var elements = config.rows[1].elements;
        var textarea = elements[0];
        var skin = webix.skin.$active;
        textarea.view = "chat-comments-text";
        config.rows[1].minHeight = textarea.height = textarea.inactiveHeight =
            webix.skin.$active.inputHeight;
        var button = elements[1].cols[1];
        button.autowidth = true;
        config.rows[1].rows = [
            {
                view: "list",
                css: "webix_chat_ulist",
                localId: "fileList",
                autoheight: true,
                borderless: true,
                template: function (obj) { return _this.ListUploadTemplate(obj); },
                type: {
                    // height: skin.listItemHeight > 30 ? 30 : skin.listItemHeight,
                },
                onClick: {
                    webix_icon: function (ev, id) {
                        _this.StopUpload(id);
                        return false;
                    },
                },
            },
            {
                cols: [
                    {
                        padding: {
                            right: 5,
                        },
                        rows: [
                            {},
                            {
                                view: "icon",
                                icon: "chi-paperclip",
                                click: function () {
                                    _this.Uploader.fileDialog();
                                },
                            },
                        ],
                    },
                    textarea,
                    {
                        padding: { left: webix.skin.$active.layoutMargin.form },
                        view: "chat-comments-layout",
                        rows: [{}, button],
                    },
                ],
            },
        ];
        delete config.rows[1].elements;
    }

    Preview(url, preview, name, size) {
        url = webix.template.escape(url);
        var html = "", css = "";
        if (url.match(/.(jpg|jpeg|png|gif)$/)) {
            if (name) {
                return (this.FileLinkTemplate(url, name, size) +
                    "<br/><a target='_blank' class='webix_chat_preview_lnk' href='" +
                    url +
                    "'>" +
                    "<img class='webix_comments_image' src='" +
                    preview +
                    "' />" +
                    "</a>");
            }
            else {
                html +=
                    "<img class='webix_comments_image webix_comments_image_link' src='" +
                    preview +
                    "'/>" +
                    "<div class='webix_chat_file_name_link'>" +
                    url +
                    "</div>";
            }
        }
        else return (this.FileLinkTemplate(url, name, size));
    };
}