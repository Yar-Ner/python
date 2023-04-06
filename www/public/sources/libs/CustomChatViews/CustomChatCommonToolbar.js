export default class CustomChatCommonToolbar extends chat.views['chat/common/toolbar'] {
    ButtonFactory(id, state) {
        var _this = this;
        var _ = this.app.getService("locale")._;
        switch (id) {
            case "close":
                return {
                    view: "icon",
                    icon: "wxi-close",
                    hotkey: "esc",
                    width: Math.max(webix.skin.$active.inputHeight, 38),
                    click: function () {
                        state.cursor = 0;
                    },
                };
            case "back":
                return {
                    view: "icon",
                    icon: "chi-back",
                    width: Math.max(webix.skin.$active.inputHeight, 38),
                    hotkey: "esc",
                    click: function () {
                        state.cursor = state.cursor - 1;
                    },
                };
            case "start":
                return {
                    view: "icon",
                    icon: "chi-back",
                    width: Math.max(webix.skin.$active.inputHeight, 38),
                    hotkey: "esc",
                    click: function () {
                        _this.app.callEvent("wizardStart", []);
                    },
                };
            case "label":
                return {
                    view: "label",
                    css: "webix_chat_wizard_title",
                    labelAlign: "center",
                    label: typeof state.toolbar.label === "function"
                        ? state.toolbar.label()
                        : state.toolbar.label,
                };
            case "edit":
                return {
                    view: "icon",
                    icon: "wxi-pencil",
                    width: Math.max(webix.skin.$active.inputHeight, 38),
                    batch: "m1",
                    click: function () {
                        state.cursor = state.cursor + 1;
                    },
                };
            case "save":
                return {
                    batch: "m2",
                    view: "button",
                    label: _("Send"),
                    hotkey: "enter",
                    width: 130,
                    css: "webix_primary",
                    click: function () {
                        _this.app.callEvent("wizardSave", []);
                    },
                };
            case "allMembers":
                return {
                    view: "button",
                    label: _("Select all"),
                    hotkey: "enter",
                    width: 130,
                    css: "webix_primary",
                    click: function () {
                        _this.app.callEvent("checkAll", []);
                    },
                };
        }
    }
}
