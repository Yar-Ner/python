import {JetView} from "webix-jet";
import ChatBackend from "../libs/services/ChatBackend";
import ChatUpload from "../libs/services/ChatUpload";
import CustomSideBar from "../libs/CustomChatViews/CustomSideBar";
import CustomMessageSideBar from "../libs/CustomChatViews/CustomMessageSideBar";
import CustomChatsView from "../libs/CustomChatViews/CustomChatsView";
import CustomListView from "../libs/CustomChatViews/CustomListView";
import CustomChatMembers from "../libs/CustomChatViews/CustomChatMembers";
import CustomChatCommonPeople from "../libs/CustomChatViews/CustomChatCommonPeople";
import CustomChatCommonToolbar from "../libs/CustomChatViews/CustomChatCommonToolbar";
import CustomChatMessages from "../libs/CustomChatViews/CustomChatMessages";

export default class ChatModal extends JetView {
    config() {
        return {
            view: "window",
            position: "center",
            id: "chatWindow",
            move: true,
            resize: true,
            hidden: true,
            height: 600,
            width: 900,
            head: {
                view: "toolbar",
                paddingY: 1,
                height: 40,
                cols: [{view: "label", label: "Чат", align: "left"},
                    {
                        view: "icon", icon: "wxi-close", click: () => {
                            $$("chatWindow").hide();
                        }
                    }
                ]
            },
            body: {
                padding: 0,
                rows: [
                    {
                        view: "chat",
                        id: "chatWindowForm",
                        autoheight: true,
                        scroll: true,
                        calls: false,
                        files : true,
                        compact  : true,
                        url: "/chat",
                        locale: {
                            lang: "ru",
                            webix: {
                                ru: "ru-RU",
                            },
                        },
                        override: new Map([
                            [chat.services.Backend, ChatBackend],
                            [chat.services.Upload, ChatUpload],
                            [chat.views.sidebar, CustomSideBar],
                            [chat.views['messages/toolbar'], CustomMessageSideBar],
                            [chat.views['chats'], CustomChatsView],
                            [chat.views.list, CustomListView],
                            [chat.views["chat/members"], CustomChatMembers],
                            [chat.views["chat/common/people"], CustomChatCommonPeople],
                            [chat.views["chat/common/toolbar"], CustomChatCommonToolbar],
                            [chat.views["messages"], CustomChatMessages],
                        ]),
                        on: {
                            onInit: app => {
                                const state = app.getState();
                                state.$observe("chatId", id => {
                                    if ($$("chatWindow") && $$("chatWindow").config.width <= 650) {
                                        $$('$sidemenu1').hide()
                                    }
                                });
                            }
                        }
                    }
                ]
            },
        };
    }

    showWindow(chatId = undefined, callback = undefined){
        this.deviceId = chatId;
        this.callback = callback;
        this.getRoot().show();
    }
    closeWindow(){
        webix.confirm({
            text: "Вы не сохранили изменения! <br> Продолжить?",
            callback: (result) => {
                if (result) {
                    this.getRoot().hide();
                }
            }
        });
    }
    closeWindowWithoutAsk(){
        this.getRoot().hide();
    }
}
