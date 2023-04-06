export default class CustomChatCommonPeople extends chat.views["chat/common/people"] {
    config() {
        const config = super.config()

        config.rows.push({
            id: "distributionMessage",
            view: "textarea",
            height: 100,
            label: "Сообщение",
            labelPosition: "top"
        })

        return config
    }

    init() {
        var _this = this;
        var users = this.app.getService("local").users();
        var table = this.$$("table");
        this.Users = [].concat(this.getParam("state", true).users);
        this.UsersStore = new webix.DataCollection({
            data: [],
        });
        this.UsersStore.data.attachEvent("onSyncApply", function () {
            table.clearAll();
            table.parse(_this.UsersStore.data
                .serialize()
                .map(function (_a) {
                    var id = _a.id, name = _a.name, avatar = _a.avatar, status = _a.status;
                    return { id: id, name: name, avatar: avatar, status: status };
                })
                .filter(function (a) { return a.id !== _this.app.config.user; }));
            table.sort(function (a, b) { return _this.SortUsers(a, b); });
            _this.LoadPeopleList();
        });
        this.UsersStore.sync(users);
        this.on(this.app, "checkAll", function () {
            _this.CheckAll();
        });
    }

    CheckAll() {
        let table = this.$$("table");
        let users = table.serialize()
        users.map(user => {
            table.updateItem(user.id, { selected: 1 });
        })
        this.UpdatePeopleList();
    };
}
