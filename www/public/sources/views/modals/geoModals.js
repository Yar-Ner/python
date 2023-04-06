import {JetView} from "webix-jet";

export default class GeoModalsView extends JetView {
    constructor(app, name) {
        super(app, name);
        this.id = "geoWindow"
        this.form = "geoWindowForm"
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
                cols: [{view: "label", label: "Геообъект", align: "left"},
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
                            {view: "text", name: "ext_id", label: "Внешний ID", value: 0, labelWidth: 100, placeholder: "Введите внешний ID", required: true},
                            {
                                view: "text",
                                name: "name",
                                id: "name",
                                label: "Название",
                                inputAlign: "left",
                                labelWidth: 100,
                                placeholder: "Введите название",
                                required: true
                            },
                            {
                                view: "text",
                                name: "type",
                                label: "Тип",
                                inputAlign: "left",
                                labelWidth: 100,
                                placeholder: "Введите тип",
                                required: true
                            },
                            {
                                view: "text",
                                name: "address",
                                label: "Адрес",
                                inputAlign: "left",
                                labelWidth: 100,
                                placeholder: "Введите адрес",
                                required: true
                            },
                            {
                                cols: [
                                    {
                                        view: "text",
                                        name: "lat",
                                        id: "latitude",
                                        label: "Широта",
                                        labelWidth: 100,
                                        inputAlign: "left",
                                        css: {"margin-right": "10px"},
                                        placeholder: "Введите широту",
                                        required: true
                                    },
                                    {
                                        view: "text",
                                        name: "long",
                                        id: "longitude",
                                        label: "Долгота",
                                        inputAlign: "left",
                                        placeholder: "Введите долготу",
                                        required: true
                                    },
                                    {
                                        view: "button",
                                        id: "point",
                                        label: "Указать",
                                        width: 130,
                                        click: (id, event) => {
                                            if ($$("mapModals").isVisible()) {
                                                $$("mapModals").hide();
                                                $$(this.id).define("height", 400);
                                                $$(this.id).resize();
                                            } else {
                                                $$("mapModals").show();
                                                $$(this.id).define("height", 800);
                                                $$(this.id).resize();

                                                let map = $$("mapModals").getMap();
                                                map.geoObjects.removeAll();
                                                map.setCenter([$$("latitude").getValue(), $$("longitude").getValue()]);

                                                let placemark = new ymaps.GeoObject({
                                                        geometry: {
                                                            type: "Point",
                                                            coordinates: [$$("latitude").getValue(), $$("longitude").getValue()]
                                                        },
                                                        properties: {
                                                            iconContent: $$("name").getValue()
                                                        }
                                                    },
                                                    {
                                                        preset: "islands#redStretchyIcon",
                                                        hasBalloon: false
                                                    }
                                                );
                                                let circle = new ymaps.Circle([placemark.geometry.getCoordinates(), $$("radius").getValue()]);
                                                map.events.add("click", (e) => {
                                                    let coords = e.get("coords");
                                                    placemark.geometry.setCoordinates(coords);
                                                    circle.geometry.setCoordinates(coords);
                                                    circle.geometry.setRadius($$("radius").getValue());

                                                    $$("latitude").setValue(coords[0]);
                                                    $$("longitude").setValue(coords[1]);
                                                });
                                                map.geoObjects.add(placemark);
                                                map.geoObjects.add(circle);
                                                map.container.fitToViewport();
                                            }
                                        }
                                    }
                                ]
                            },
                            {
                                apikey: "1c58e6ea-09bc-4f90-a0c5-e7e3a2b1ba88",
                                view: "yandex-map",
                                id: "mapModals",
                                minWidth: 400,
                                minHeight: 200,
                                zoom: 10,
                                center: [57.627398, 39.891040],
                                lang: "ru-RU"
                            },
                            {
                                view: "text",
                                name: "radius",
                                id: "radius",
                                label: "Радиус",
                                inputAlign: "left",
                                labelWidth: 100,
                                placeholder: "Введите радиус",
                                required: true
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
                                    $$("mapModals").getMap().geoObjects.removeAll();
                                    const form = $$(this.form);
                                    const obj = this;

                                    if (form.validate()) {
                                        form.disable();
                                        const values = form.getValues();
                                        values.lat = parseFloat(values.lat);
                                        values.long = parseFloat(values.long);
                                        values.radius = parseFloat(values.radius);
                                        webix.ajax().post("/geoobjects/" + (values.id ? values.id : 0), values).then((data) => {
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
                                            let text = "Произошла ошибка обращения к серверу.\n"
                                            if (e.responseText)  text += JSON.parse(e.responseText).message
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
                                    $$("mapModals").getMap().geoObjects.removeAll();
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

                    $$("mapModals").hide();        
                    $$(this.id).define("height", 400);
                    $$(this.id).resize()

                    if (this.geoId) {
                        webix.extend($$(this.form), webix.ProgressBar);
                        $$(this.form).showProgress();

                        webix.ajax().get("/geoobjects/" + this.geoId).then((data) => {
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

    showWindow(geoId = undefined, callback = undefined) {
        this.geoId = geoId;
        this.callback = callback;

        this.ui(this.config()).show();
    }

    closeWindow() {
        const values = $$(this.form).getValues();
        values.lat = parseFloat(values.lat);
        values.long = parseFloat(values.long);
        values.radius = parseFloat(values.radius);

        let changed = 0

        if (this.geoId !== undefined) {
            for (let key in values) {
                if (this.initValues[key] === null) this.initValues[key] = ''
                if (values[key] != this.initValues[key]) changed = 1
            }
        }

        if (changed === 1) {
            webix.confirm({
                text: "Вы не сохранили изменения! <br> Продолжить?",
                callback: (result) => {
                    if (result) {
                        $$("mapModals").getMap().geoObjects.removeAll();
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