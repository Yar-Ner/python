import {JetView} from "webix-jet";

export default class VehiclesContainersModalsView extends JetView {
    constructor(app, name) {
        super(app, name);
        this.id = "vehiclesContainersWindow"
        this.form = "vehiclesContainersWindowForm"
    }
    config() {
        return {
            view: "window",
            position: "center",
            id: this.id,
            move: true,
            modal: true,
            height: 500,
            width: 700,
            head: {
                view: "toolbar",
                paddingY: 1,
                height: 40,
                cols: [{view: "label", label: "Типы тары", align: "left"},
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
                            {view: "text", name: "ext_id", label: "Внешний ID", required: true},
                            {
                                view: "text",
                                name: "name",
                                label: "Название",
                                inputAlign: "left",
                                placeholder: "Введите название",
                                required: true
                            },
                            {
                                view: "textarea",
                                name: "description",
                                label: "Описание",
                                inputAlign: "left",
                                placeholder: "Введите описание",
                            },
                            {view: "text", name: "units", label: "Единицы измерения"},
                            {view: "text", name: "volume", label: "Объем"},
                            {view: "switch", name: "dropped_out", label: "Выбыл"}
                        ],
                        elementsConfig: {
                            inputAlign: "left",
                            labelPosition: "left",
                            labelWidth: 190,
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
                                    const form = $$(this.form);
                                    const obj = this;

                                    if (form.validate()) {
                                        form.disable();
                                        const values = form.getValues();
                                        webix.ajax().post("/vehicles/containers/" + (values.id ? values.id : 0), values)
                                            .then((data) => {
                                                form.enable();
                                                webix.message({
                                                    type: "success",
                                                    text: 'Сохранение прошло успешно'
                                                });
                                                form.enable();
                                                obj.closeWindowWithoutAsk();

                                                if (obj.callback) {
                                                    obj.callback();
                                                }
                                            }).catch(function (e) {
                                            webix.message({
                                                type: "error",
                                                text: "Произошла ошибка обращения к серверу"
                                            });
                                            form.enable();
                                            console.log(e);
                                        });
                                    } else {
                                        webix.message({
                                            type: "error",
                                            text: "Заполните обязательные поля"
                                        })
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

                    if (this.containerId) {
                        webix.extend($$(this.form), webix.ProgressBar);
                        $$(this.form).showProgress();

                        webix.ajax().get("/vehicles/containers/" + this.containerId).then((data) => {
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

    showWindow(containerId = undefined, callback = undefined) {
        this.callback = callback;
        this.containerId = containerId;

        this.ui(this.config()).show();
    }

    closeWindow() {
        const values = $$(this.form).getValues()
        let changed = 0

        if (this.containerId !== undefined) {
            for (let key in values) {
                if (this.initValues[key] === undefined) this.initValues[key] = ''
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
        $$(this.id).close();
    }
}