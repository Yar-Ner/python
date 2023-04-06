import {JetView} from "webix-jet";
import VehiclesContainersModalsView from "./modals/vehiclesContainersModals";

export default class VehiclesContainersView extends JetView {
    constructor(app, name) {
        super(app, name);
        this.id = "vehiclesContainersGrid"
    }
    config() {
        const config = {
            view: "datatable",
            localId: "grid",
            id: this.id,
            select: true,
            css: "webix_header_border",
            columns: [
                {id: "id", width: 50, header: [{text: "ID"}]},
                {id: "name", fillspace: 1.5, header: [{text: "Название"}]},
                {id: "description", fillspace: 3, header: [{text: "Описание"}]},
                {id: "units", fillspace: 1, header: [{text: "Единицы измерения"}]},
                {id: "volume", fillspace: 1, header: [{text: "Объем"}]},
                {id: "dropped_out", fillspace: 1, header: [{text: "Выбыл"}], template: (o) => {
                        let txt = "<span class=" + (o.dropped_out ? 'redcircle_state' : 'greencircle_state') + "></span>";
                        txt += "<span class = \"state" + (o.dropped_out ? 5 : 1) + "\">";
                        txt += o.dropped_out ? "Бункер выбыл" : "Бункер не выбыл"
                        txt += "</span>";
                        return txt;
                    }},
                {id: "ext_id", fillspace: 2, header: [{text: "Внешний ID"}]}
            ],
            on: {
                onItemDblClick: () => {
                    this.editAction()
                }
            }
        };

        return config;
    }

    reloadView() {
        webix.extend($$(this.id), webix.ProgressBar);
        $$(this.id).showProgress({
            type: "icon",
            hide: true
        });

        $$(this.id).clearAll();
        $$(this.id).load("/vehicles/containers");
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
        });
        this.on(this.app, "deleteAction", () => {
            let cb = () => { this.reloadView(); };
            let selectedRow = $$(this.id).getSelectedId();

            if (selectedRow && selectedRow.row) {
                webix.confirm({
                    text: "Тара будет удалена! <br> Продолжить?",
                    callback: (result) => {
                        if (result) {
                            webix.ajax().del("/vehicles/containers/" + selectedRow.row).then((data) => {
                                webix.message({
                                    type: "warning",
                                    text: 'Тара успешно удалена!'
                                });
                                cb();
                            }).catch(function (e) {
                                webix.message({
                                    type: "error",
                                    text: "Произошла ошибка обращения к серверу"
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
                    text: "Выберите тару для удаления."
                });
            }
        });
    }

    init() {
        $$("addAction").show()
        $$("editAction").show()
        $$("deleteAction").show()

        this.window = this.ui(VehiclesContainersModalsView);

        this.setActionHandlers();
        this.reloadView();
    }
}