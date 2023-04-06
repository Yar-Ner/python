export default class CustomListView extends chat.views.list {
    init(view) {
        super.init(view)
    }

    config() {
        const config = super.config();
        return config
    }

    InitSelf (data, filter) {
        let _this = this;
        let list = this.$$("list");
        list.data.attachEvent("onSyncApply", function () {
            _this.ApplySearchValue();
            _this.SyncHandler();
        });
        list.sync(data, filter || null);
        list.attachEvent("onAfterSelect", function (id) {
            list.hide()
            // _this.ShowChat(id);
        });
        this.on(this.app.getState().$changes, "search", function (v) {
            _this.Find(v);
        });
    };
}