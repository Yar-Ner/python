import {JetView} from "webix-jet";
import ContractorModalsView from "./modals/contractorModals";
import "../libs/customfilters";

export default class ContractorsView extends JetView {
    config() {
        return {
            view: "datatable",
            id: "contractorsGrid",
            select: true,
            css: "webix_header_border",
            resizeColumn: true,
            columns: [
                {id: "id", width: 50, header: [{text: "ID"}]},
                {id: "name", width: 300, header: ["Название", {content: "serverFilter"}]},
                {id: "code", fillspace: 1, header: ["Код", {content: "serverFilter"}]},
                {id: "inn", width: 200, header: ["ИНН", {content: "serverFilter"}]},
                {id: "comment", fillspace: 1.5, header: [{text: "Комментарий"}]},
                {
                    id: "geoobjectsId",
                    fillspace: 2,
                    header: [
                        "Геообъекты",
                        {
                            content:"serverMultiComboFilter",
                            inputConfig: {
                                keepText: true
                            },
                            suggest: {
                                view: "my_suggest",
                            }
                        }
                        ],
                    options: "/geoobjects/short",
                    template: function (o) {
                        let options = $$("contractorsGrid").getColumns().find(item => item.id === "geoobjectsId").collection.serialize();
                        let str = [];
                        if (o.addresses.length > 0) {
                            o.addresses.map(address => {
                                let opt = options.find(o => o.id == address.id);

                                if (opt) {
                                    str.push(opt.value);
                                }
                            })
                        }
                        return str.join(", ");
                    }
                },
                {id: "ext_id", fillspace: 2, header: [{text: "Внешний ID"}]}
            ],
            on: {
                onItemDblClick: () => {
                    this.editAction()
                }
            }
        };
    }


    reloadView() {
        webix.extend($$("contractorsGrid"), webix.ProgressBar);
        $$("contractorsGrid").showProgress({
            type: "icon",
            hide: true
        });
        $$("contractorsGrid").clearAll();
        $$("contractorsGrid").loadNext(null, null, null, "/contractors");
    }

    editAction() {
        let selectedRow = $$("contractorsGrid").getSelectedId();

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
            let selectedRow = $$("contractorsGrid").getSelectedId();

            if (selectedRow && selectedRow.row) {
                webix.confirm({
                    text: "Контрагент будет удален! <br> Продолжить?",
                    callback: (result) => {
                        if (result) {
                            webix.ajax().del("/contractors/" + selectedRow.row).then((data) => {
                                webix.message({
                                    type: "warning",
                                    text: 'Контрагент успешно удален!'
                                });
                                cb();
                            }).catch(function (e) {
                                let text = "Произошла ошибка обращения к серверу"
                                if (e.status === 403) text = "Вам запрещено выполнять данное действие."
                                webix.message({
                                    type: "error",
                                    text: text
                                });
                                form.enable();
                                console.log(e);
                                obj.closeWindowWithoutAsk();
                            });
                        }
                    }
                });
            } else {
                webix.message({
                    type: "error",
                    text: "Выберите контрагента для удаления."
                });
            }
        });
    }

    init() {
        $$("addAction").show()
        $$("editAction").show()
        $$("deleteAction").show()

        this.window = this.ui(ContractorModalsView);

        this.setActionHandlers();
        this.reloadView();
    }

}