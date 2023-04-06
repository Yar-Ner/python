import {JetView} from "webix-jet";
import GeoModalsView from "../modals/geoModals";

export default class GeoobjectsTableView extends JetView {
    constructor(app, name) {
        super(app, name);
        this.id = "geoGrid"
    }

    config() {
        return {
            view: "datatable",
            localId: "grid",
            id: this.id,
            select: true,
            css: "webix_header_border",
            resizeColumn: true,
            width: 900,
            minWidth: 400,
            columns: [
                {id: "id", fillspace: 0.5, header: [{text: "ID"}]},
                {id: "name", fillspace: 2, header: ["Название", {content: "serverFilter"}]},
                {id: "type", fillspace: 1, header: [{text: "Тип"}]},
                {id: "address", fillspace: 2, header: ["Адрес", {content: "serverFilter"}]},
                {id: "coordinates", width: 200, header: [{text: "Координаты"}], template: "#lat#, #long#" },
                {id: "radius", fillspace: 1, header: [{text: "Радиус"}]},
                {id: "ext_id", fillspace: 1, header: [{text: "Внешний ID"}]}
            ],
            on: {
                onresize: () => {
                    setTimeout(() => {
                        if ($$("map").getMap()) {
                            $$("map").getMap().container.fitToViewport()
                        }
                    }, 1)
                },
                onItemClick: () => {
                    let map = $$("map").getMap();
                    let selectedRow = $$(this.id).getSelectedItem();
                    map.setZoom(14);
                    map.setCenter([selectedRow.lat, selectedRow.long]);

                    map.geoObjects.remove(window.obj[0]);
                    let circle = new ymaps.Circle([[selectedRow.lat, selectedRow.long], selectedRow.radius]);
                    window.obj[0] = circle;
                    map.geoObjects.add(circle);
                },
                onItemDblClick: () => {
                    this.editAction()
                },
                onAfterLoad: () => {
                    window.obj = [];
                    let map = $$("map").getMap();
                    map.geoObjects.removeAll();
                    $$(this.id).eachRow(index => {
                        try {
                            const row = $$(this.id).getItem(index);

                            let placemark = new ymaps.GeoObject({
                                    geometry: {
                                        type: "Point",
                                        coordinates: [row.lat, row.long]
                                    },
                                    properties: {
                                        balloonContent: row.radius,
                                        iconContent: row.name,
                                        index: index
                                    }
                                },
                                {
                                    preset: 'islands#redStretchyIcon',
                                    draggable: false,
                                    hasBalloon: false
                                }
                            )
                            placemark.events.add(['click'], (e) => {
                                map.geoObjects.remove(window.obj[0]);
                                const radius = placemark.properties._data.balloonContent;
                                const coordinates = placemark.geometry.getCoordinates();
                                let circle = new ymaps.Circle([coordinates, radius]);
                                window.obj[0] = circle;
                                map.geoObjects.add(circle);
                                $$(this.id).select(placemark.properties._data.index)
                                $$(this.id).showItem(placemark.properties._data.index)
                            })
                            map.geoObjects.add(placemark);
                        } catch (e) {
                            console.log(e);
                        }
                    });
                    map.container.fitToViewport();
                }
            }
        }
    }

    reloadView() {
        webix.extend($$(this.id), webix.ProgressBar);
        $$(this.id).showProgress({
            type: "icon",
            hide: true
        });
        $$(this.id).clearAll();
        $$(this.id).load("/geoobjects").then(() => {

        })
    }
    editAction() {
        let selectedRow = $$(this.id).getSelectedId();

        if (selectedRow && selectedRow.row) {
            this.window.showWindow(selectedRow.row, () => {
                this.reloadView();
            });
        } else {
            webix.message({
                type: "error",
                text: "Выберите строку для редактирования."
            });
        }
    }
    setActionHandlers() {
        this.on(this.app, "reloadAction", () => {
            this.reloadView();
        });
        this.on(this.app, "addAction", () => {
            this.window.showWindow(undefined, () => {
                this.reloadView();
            });
        });
        this.on(this.app, "editAction", () => {
            this.editAction()
        })
        this.on(this.app, "deleteAction", () => {
            let cb = () => {
                this.reloadView();
            };
            let selectedRow = $$(this.id).getSelectedId();

            if (selectedRow && selectedRow.row) {
                webix.confirm({
                    text: "Геообъект будет удален! <br> Продолжить?",
                    callback: (result) => {
                        if (result) {
                            webix.ajax().del("/geoobjects/" + selectedRow.row).then((data) => {
                                webix.message({
                                    type: "warning",
                                    text: 'Геообъект успешно удален!'
                                });
                                cb();
                            }).catch(function (e) {
                                let text = "Произошла ошибка обращения к серверу"
                                if (e.status === 403) text = "Вам запрещено выполнять данное действие."
                                webix.message({
                                    type: "error",
                                    text
                                });
                                console.log(e);
                                obj.closeWindowWithoutAsk();
                            });
                        }
                    }
                });
            } else {
                webix.message({
                    type: "error",
                    text: "Выберите геообъект для удаления."
                });
            }
        });
    }

    init() {
        $$("addAction").show()
        $$("editAction").show()
        $$("deleteAction").show()

        this.window = this.ui(GeoModalsView);

        this.setActionHandlers();
        setTimeout(() => {
            this.reloadView();
        }, 2000)
    }
}
