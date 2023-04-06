import {JetView} from "webix-jet";
import VehiclesTypesModalsView from "./modals/vehiclesTypesModals";

export default class VehiclesTypesView extends JetView {
    constructor(app, name) {
        super(app, name);
        this.id = "vehiclesTypesGrid"
    }
    config() {
        const config = {
            view: "datatable",
            id: this.id,
            select: true,
            css: "webix_header_border",
            columns: [
                {id: "id", fillspace: 0.5, header: [{text: "ID"}]},
                {id: "ext_id", fillspace: 0.5, header: [{text: "Внешний ID"}]},
                {id: "name", fillspace: 2, header: [{text: "Название"}]},
                {id: "description", fillspace: 3, header: [{text: "Описание"}]},
                {
                    id: "containersId",
                    fillspace: 2,
                    header: [{text: "Тип тары"}],
                    options: "/vehicles/containers",
                    template: (o) => {
                        let options = $$(this.id).getColumns().find(item => item.id === "containersId").collection.serialize();
                        let str = [];
                        for (let containersId of o.containersId) {
                            let opt = options.find(o => o.id == containersId);

                            if (opt) {
                                str.push(opt.name);
                            }
                        }
                        return str.join(", ");
                    }
                },
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
        $$(this.id).load("/vehicles/types");
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
                    text: "Тип ТС будет удален! <br> Продолжить?",
                    callback: (result) => {
                        if (result) {
                            webix.ajax().del("/vehicles/types/" + selectedRow.row).then((data) => {
                                webix.message({
                                    type: "warning",
                                    text: 'Тип ТС успешно удален!'
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
                    text: "Выберите тип ТС для удаления."
                });
            }
        });
    }

    init() {
        $$("addAction").show()
        $$("editAction").show()
        $$("deleteAction").show()

        this.window = this.ui(VehiclesTypesModalsView);

        this.setActionHandlers();
        this.reloadView();
    }
}