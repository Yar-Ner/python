export default class ChatBackend extends chat.services.Backend {

    constructor(config) {
        setInterval(() => {
            const currenChat = $$('chatWindowForm').getState().chatId
            this.chats()
            this.messages(currenChat)
        },10000);
        super(config);
    }
    messages(chatId) {
        return webix.ajax().get("/chat/users/"+chatId+"/messages").then(data => {
            data = data.json()
            const view = $$('chatWindowForm').queryView('chat-comments').queryView('list')
            $$(view).clearAll(data)
            $$(view).parse(data)
            if (data.length > 0) $$(view).showItem(data[data.length - 1].id)
            return Promise.resolve(data)
        });
    }

    users() {
        return webix.ajax().get("/chat/users");
    }

    resetCounter(cid) {
        this.updateSunUnreadCount(
            this.sumUnread($$('chatWindowForm').getService("local").chats().serialize())
        );

        webix.ajax().post("/chat/users/" + cid + "/counter").then( r => super.resetCounter(cid));
    }

    addMessage(cid, text, origin) {
        this.resetCounter(cid);
        return webix.ajax().post("/chat/users/"+cid+"/messages", {content: text, recipient_id:cid}).then(data => {
            this.messages(cid)
            return Promise.resolve(data.json())
        });
    }

    addDirect() {
    }

    addChat(cid) {
        return Promise.resolve(
            {
                date: new Date(),
                id: cid,
                message: "Hi there",
                name: "Today's meeting",
                unread_count: 1,
                users: [cid, 12]
            }
        )

        return super.addChat(cid)
        return webix.ajax().post("/chat/users/"+cid+"/messages", {text: text, origin:origin});
    }

    fileUploadUrl(id) {
        return "/api/photo?chatId=" + id;
    }

    chats() {
        return webix.ajax().get("/chat/chats").then((data) => {
            const chats = data.json();
            this.updateSunUnreadCount(
                this.sumUnread(chats)
            );

            $$('chatWindowForm').queryView("list").refresh()

            return Promise.resolve(data.json())
        });
    }

    updateChat(chatId, name, avatar) {
        return super.updateChat(chatId, name, avatar);
    }

    sumUnread(chats) {
        let unreadCount = 0;

        chats && chats.forEach((chat) => {
            let chatList = $$('chatWindowForm').getService("local").chats()
            if (chat.unread_count > 0) {
                unreadCount += chat.unread_count;
                chatList.moveTop(chat.id)
            }
            let item = chatList.getItem(chat.id)
            if (item) {
                if ($$('chatWindowForm').getService("local").chats().getItem(chat.id)) {
                    $$('chatWindowForm').getService("local").chats().updateItem(chat.id, {
                        message: chat.message,
                        unread_count: chat.unread_count
                    })
                }
            }
        });

        return unreadCount;
    }

    updateSunUnreadCount(count) {
        if ($$('chatBadge')) {
            $$('chatBadge').config.badge = count
            $$('chatBadge').refresh()
        }
    }
}