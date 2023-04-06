import {JetView} from "webix-jet";

export default class SetupModalsView extends JetView {
    config() {
        const obj = this;

        return {
            view: "window",
            position: "center",
            id: "additionalSetupWindow",
            modal: true,
            width: 700,
            maxHeight: 800,
            resize: true,
            move: true,
            head: {
                view: "toolbar",
                paddingY: 1,
                height: 40,
                cols: [{view: "label", label: "Дополнительные настройки", align: "left"},
                    {
                        view: "icon", icon: "wxi-close", click: () => {
                            this.closeWindow()
                        }
                    }
                ]
            },
            body: {
                padding: 17,
                rows: [
                    {
                        width: 600, view: "form", id: "settings-form", minHeight: 500, padding: 17, scroll: true,
                        elements: [{
                            width: 600,
                            view: "datatable",
                            id: "additionalSetupSettings",
                            select: true,
                            tooltip: true,
                            resizeColumn: true,
                            scroll: true,
                            editable: true,
                            columns: [
                                {id: "handle", header: '', fillspace: 1,},
                                {id: "val", header: '', fillspace: 1, editor: "text"}
                            ],
                            on: {
                                onBeforeContextMenu: (id) => {
                                    $$('additionalSetupSettings').select(id)
                                },
                            },
                            ready: function () {
                                this.save = () => {
                                    const table = $$("additionalSetupSettings");
                                    table.disable();

                                    const values = table.serialize();
                                    const settings = []
                                    for (let setting in obj.initValues) {
                                        const compareSetting = values.find(settingNew => settingNew.handle === setting)
                                        if (compareSetting.val != obj.initValues[setting]) {
                                            settings.push(compareSetting)
                                        }
                                    }

                                    let url = `/settings/${obj.type}`
                                    if (obj.id) url += `/${obj.id}`
                                    url += '/save'
                                    webix.ajax().post(url, {settings}).then(function (data) {
                                        table.enable();
                                        data = data.json();
                                        webix.message({
                                            type: "success",
                                            text: "Сохранение прошло успешно"
                                        });
                                        table.enable();
                                        obj.closeWindowWithoutAsk();
                                        if (obj.callback) obj.callback()
                                    }).catch(function (e) {
                                        webix.message({
                                            type: "error",
                                            text: "Произошла ошибка обращения к серверу"
                                        });
                                        table.enable();
                                        console.log(e);
                                    });
                                };

                                webix.ui({
                                    view: "contextmenu", id: "setupMenu",
                                    data: ["Восстановить значение по умолчанию"],
                                    on: {
                                        onMenuItemClick: (o) => {
                                            webix.confirm({
                                                text: "Внимание! Значение будет восстановлено по умолчанию. <br> Продолжить?",
                                                callback: function (result) {
                                                    if (result) {
                                                        const item = $$("additionalSetupSettings").getSelectedItem()
                                                        webix.ajax().post(`/settings/${obj.type}/${obj.id}/default/${item.handle}`,
                                                            function (text) {
                                                                let data = JSON.parse(text);
                                                                if (data.result) {
                                                                    webix.message({
                                                                        type: "success",
                                                                        text: "Восстановление произошло успешно"
                                                                    });
                                                                    $$("additionalSetupWindow").close()
                                                                } else {
                                                                    webix.message({
                                                                        type: "error",
                                                                        text: data.message ? data.message : "Произошла ошибка"
                                                                    });
                                                                }
                                                            });
                                                    }
                                                }
                                            })
                                        },
                                        onBeforeShow: function () {
                                            let item = $$('additionalSetupSettings').getSelectedItem();
                                            if (!item.main) return false;
                                        }
                                    },
                                }).attachTo(this);
                            },
                            scheme: {
                                $init: function (row) {
                                    if (!row.main) {
                                        row.$css = 'good-row';
                                    }
                                }
                            }
                        },
                        {
                            margin: 10,
                            padding: 17,
                            cols: [{},
                                {
                                    view: "button",
                                    label: "Сохранить",
                                    type: "form",
                                    align: "center",
                                    width: 120,
                                    click: () => {
                                        const values = $$("additionalSetupSettings").serialize()
                                        let changed = 0

                                        for (let setting in this.initValues) {
                                            const obj = values.find(settingNew => settingNew.handle === setting)
                                            if (obj.val != this.initValues[setting]) {
                                                changed = 1
                                            }
                                        }

                                        if (changed === 0) {
                                            this.closeWindow()
                                            return
                                        }
                                        webix.confirm({
                                            text: "Все измененные данные будут сохранены. <br> Продолжить?",
                                            callback: function (result) {
                                                if (!result) {
                                                    return;
                                                }
                                                $$("additionalSetupSettings").save();
                                            }
                                        });
                                    }
                                },
                                {
                                    view: "button",
                                    label: "Отмена",
                                    align: "center",
                                    width: 120,
                                    click: () => {
                                        this.closeWindow();
                                    }
                                }
                            ]
                        }]
                    }
                ]
            },
            on: {
                onShow: () => {
                    let url = `/settings/${this.type}`
                    if (this.id) url += `/${this.id}`

                    webix.ajax().get(url).then(data => {
                        data = data.json()
                        const settings = []
                        data.map(setting => {
                            settings[setting.handle] = setting.val
                        })
                        this.initValues = settings
                        $$("additionalSetupSettings").parse(data)
                    })
                }
            }
        };
    }

    showWindow(id = undefined, type = "default", callback = undefined) {
        this.id = id;
        this.type = type;
        this.callback = callback;

        this.ui(this.config()).show();
    }

    closeWindow() {
        const values = $$("additionalSetupSettings").serialize()
        let changed = 0

        for (let setting in this.initValues) {
            const obj = values.find(settingNew => settingNew.handle === setting)
            if (obj.val != this.initValues[setting]) {
                changed = 1
            }
        }

        if (changed === 1) {
            webix.confirm({
                text: "Вы не сохранили изменения! <br> Продолжить?",
                callback: (result) => {
                    if (result) {
                        $$("additionalSetupWindow").close()
                    }
                }
            });
        } else {
            $$("additionalSetupWindow").close()
        }
    }

    closeWindowWithoutAsk() {
        $$("additionalSetupWindow").close()
    }
}