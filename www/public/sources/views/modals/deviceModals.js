import {JetView} from "webix-jet";
import {Utils} from "../../libs/utils";

export default class DeviceModalsView extends JetView {
    constructor(app, name) {
        super(app, name);
        this.id = "deviceWindow"
        this.form = "deviceWindowForm"
    }
    config() {
        return {
            view: "window",
            position: "center",
            id: this.id,
            move: true,
            modal: true,
            height: 400,
            width: 700,
            head: {
                view: "toolbar",
                paddingY: 1,
                height: 40,
                cols: [{view: "label", label: "Устройство", align: "left"},
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
                        view: "form", id: this.form, autoheight: true, scroll: true, elements: [
                            {view: "text", name: "id", label: "id", value: 0, hidden: true},
                            {
                                view: "text",
                                name: "name",
                                label: "Название",
                                inputAlign: "left",
                                labelWidth: 190,
                                placeholder: "Введите название",
                                required: true
                            },
                            {
                                view: "text",
                                name: "imei",
                                label: "IMEI",
                                inputAlign: "left",
                                labelWidth: 190,
                                placeholder: "Введите IMEI",
                                required: true
                            },
                            {
                                cols: [
                                    {
                                        view: "multicombo",
                                        name: "vehicleId",
                                        label: "Машины",
                                        inputAlign: "left",
                                        labelWidth: 190,
                                        labelPosition: "left",
                                        options: {
                                            body: {
                                                template: "#name#"
                                            },
                                            url: "/vehicles/short"
                                        },
                                        placeholder: "Выберите машины",
                                    },

                                ]
                            }
                        ],
                        elementsConfig: {
                            inputAlign: "left",
                            labelPosition: "left"
                        }
                    },
                    {
                        margin: 10,
                        cols: [
                            {},
                            {
                                view: "button",
                                label: "Сохранить",
                                type: "form",
                                align: "center",
                                width: 120,
                                click: () => {
                                    webix.extend($$(this.id), webix.ProgressBar);
                                    $$(this.id).showProgress({
                                        type: "icon",
                                        hide: true
                                    });

                                    const form = $$(this.form);
                                    const obj = this;

                                    if (form.validate()) {
                                        form.disable();
                                        const values = form.getValues();

                                        webix.ajax().post("/devices/" + (values.id ? values.id : 0), values)
                                            .then((data) => {
                                                form.enable();
                                                data = data.json();
                                                webix.message({
                                                    type: "success",
                                                    text: "Сохранение прошло успешно"
                                                });
                                                form.enable();
                                                obj.closeWindowWithoutAsk();

                                                if (obj.callback) {
                                                    obj.callback();
                                                }
                                            }).catch(function (e) {
                                                let text = "Произошла ошибка обращения к серверу"
                                                if (e.status === 403) text = "Вам запрещено выполнять данное действие."
                                            webix.message({
                                                type: "error",
                                                text
                                            });
                                            form.enable();
                                            console.log(e);
                                        });
                                    } else {
                                        webix.message({
                                            type: "error",
                                            text: "Заполните обязательные поля"
                                        });
                                    }
                                }
                            },
                            {
                                view: "button", label: "Отмена", align: "center", width: 120, click: () => {
                                    this.closeWindow();
                                }
                            }
                        ]
                    },
                ]
            },
            on: {
                onShow: () => {
                    const form = $$(this.form);
                    const obj = this;

                    form.clear();

                    if (this.deviceId) {
                        webix.extend($$(this.form), webix.ProgressBar);
                        $$(this.form).showProgress();

                        webix.ajax().get("/devices/" + this.deviceId).then((data) => {
                            form.enable();
                            this.initValues = data.json()
                            form.setValues(data.json());

                            setTimeout(() => $$(this.form).hideProgress(), 100)
                        }).catch(function (e) {
                            webix.message({
                                type: "error",
                                text: "Произошла ошибка обращения к серверу"
                            });
                            form.enable();
                            console.log(e);
                            obj.closeWindowWithoutAsk();
                        });
                    }
                }
            }
        };
    }

    showWindow(deviceId = undefined, callback = undefined) {
        this.deviceId = deviceId;
        this.callback = callback;

        this.ui(this.config()).show();
    }

    closeWindow() {
        const values = $$(this.form).getValues()
        let changed = 0

        if (this.deviceId !== undefined) {
            for (let key in values) {
                if (this.initValues[key] === undefined) this.initValues[key] = ''

                if (['vehicleId'].includes(key)) {
                    if (Utils.compareArray(key, values, this.initValues)) changed = 1
                    continue
                }

                if (values[key] != this.initValues[key]) changed = 1
            }
        }

        if (changed === 1) {
            webix.confirm({
                text: "Вы не сохранили изменения! <br> Продолжить?",
                callback: (result) => {
                    if (result) {
                        $$(this.id).close();
                    }
                }
            });
        } else {
            $$(this.id).close();
        }
    }

    closeWindowWithoutAsk() {
        $$("deviceWindow").close();
    }
}