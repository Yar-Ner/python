export default class CustomChatsView extends chat.views['chats'] {
    init(view) {
        super.init(view);
        view.queryView("button").config.click = () => {}
        view.queryView("button").define("label", "Рассылка");
        view.queryView("button").attachEvent('onItemClick', () => {
            document.getElementsByClassName('chi-account-plus')[0].click()
        })

        view.queryView("button").refresh();
    }

    config() {
        const config = super.config()
        webix.config = config
        return config;
    }
}
