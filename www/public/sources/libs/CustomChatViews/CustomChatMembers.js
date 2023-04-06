export default class CustomChatMembers extends chat.views["chat/members"] {
    stages() {
        var _ = this.app.getService("locale")._;
        return [
            null,
            [
                "./chat.common.people",
                {
                    label: "<span class=\"webix_chat_wizard_title2\">" + _("Distribution") + "</span>",
                    cols: ["close", "allMembers", "label", "save"],
                },
            ],
        ];
    }

    Save() {
        let app = this.app;
        let state = this.getParam("state");
        let message = $$("distributionMessage").getValue()
        if (!message || message === '') {
            webix.message({
                type: "error",
                text: "Введите текст сообщения"
            })
            throw "Отсутсвует текст сообщения"
        }
        if (state.users.length) {
            state.users.map((id)=> {
                app.getService("backend").addMessage(id, message);
            })
            webix.message({
                type: "success",
                text: "Сообщения успешно отправлены"
            })
        }
    };
}
