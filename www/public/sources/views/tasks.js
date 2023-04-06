import {JetView} from "webix-jet";
import TaskModalsView from "./modals/taskModals";
import {Utils} from "../libs/utils";
import "../libs/customfilters";

export default class TasksView extends JetView {
    config() {
        return {
            view: "datatable", id: "taskGrid", resizeColumn: true,
            select: true, css: "webix_header_border", multiselect:true,
            columns: [
                {id: "id", fillspace: 0.5, header: [{text: "ID"}] },
                {
                    id: "user_id",
                    fillspace: 2,
                    header: [
                        "Водитель",
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
                    options: "/users"
                },
                {
                    id: "vehicles_id",
                    fillspace: 2,
                    header: [
                        "Машина",
                        {
                            content:"serverMultiComboFilter",
                            inputConfig: {
                                keepText: true
                            },
                            suggest: {
                                view: "my_suggest"
                            }
                        },
                    ],
                    options: "/vehicles"
                },
                {id: "number", fillspace: 1.5, header: [{text: "Номер документа"}]},
                {
                    id: "status",
                    fillspace: 1.5,
                    header: [{text: "Статус"}],
                    template: (o) => {
                        return Utils.translateTaskStatus(o.status)
                    }
                },
                {id: "loaded_weight", fillspace: 0.75, header: [{text: "Вес загруженной машины"}]},
                {id: "empty_weight", fillspace: 0.75, header: [{text: "Вес порожней машины"}]},
                {id: "comment", fillspace: 2, header: [{text: "Описание"}]},
                {id: "starttime", fillspace: 2, header: ["Начало", {content: "serverDateRangeFilter"}]},
                {id: "endtime", fillspace: 2, header: [{text: "Окончание"}]},
                {id: "updated", fillspace: 2, header: [{text: "Изменен"}]}
            ],
            on: {
                onItemDblClick: (id) => {
                    this.window.showWindow(id)
                },
                onBeforeContextMenu: (id) => {
                    $$('taskGrid').select(id)
                },
            },
            ready: function () {
                webix.ui({
                    view:"contextmenu", id:"tasksMenu",
                        data: ["Перейти"],
                    on: {
                        "onMenuItemClick": function(o) {
                            const obj = $$('taskGrid').getSelectedItem();
                            document.location.href = `#!/main/map?taskId=${obj.id}&vehicles_id=${obj.vehicles_id}`
                        }
                    },
                }).attachTo(this);
            }
        };
    }

    reloadView() {
        webix.extend($$("taskGrid"), webix.ProgressBar);
        $$("taskGrid").showProgress();
        $$("taskGrid").clearAll();
        $$("taskGrid").loadNext(null, null, null, "/tasks");
    }

    setActionHandlers() {
        this.on(this.app, "reloadAction", () => {
            this.reloadView();
        });
    }

    init() {
        $$("addAction").hide()
        $$("editAction").hide()
        $$("deleteAction").hide()
        this.window = this.ui(TaskModalsView);

        webix.ui({
            view:"contextmenu", id:"tasksMenu",
        }).attachTo(this);

        this.setActionHandlers();
        this.reloadView();
    }
}