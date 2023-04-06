import {JetView} from "webix-jet";
import {Utils} from "../../libs/utils";

export default class TaskModalsView extends JetView {
    config() {
        return {
            view: "window",
            position: "center",
            id: "taskWindow",
            move: true,
            modal: true,
            resize: true,
            height: 600,
			width: 1100,
            head: {
                view: "toolbar",
                paddingY: 1,
                height: 40,
                cols: [{view: "label", label: "Маршрутный лист", align: "left"},
                    {
                        view: "icon", icon: "wxi-close", click: () => {
                            this.closeWindowWithoutAsk()
                        }
                    }
                ]
            },
            body: {
                padding: 17,
                rows: [
                    {
                        view: "datatable", id: "taskWindowGrid", select: true, css: "webix_header_border webix_row_task_window",
                        scroll: true, resizeColumn: true,
                        columns: [
                            { id: "id", header: "ID", width: 50, template:"{common.subrow()} #id#"},
                            { id: "contractor", header: "Контрагент", fillspace: 1.5 },
                            { id: "ext_id", header: "Внешний ID", fillspace: 2 },
                            { id: "address", header: "Адрес", fillspace: 1 },
                            { id: "lat", header: "Широта", fillspace: 1 },
                            { id: "long", header: "Долгота", fillspace: 1 },
                            { id: "radius", header: "Радиус", fillspace: 1 },
                            { id: "order", header: "Order", fillspace: 1 },
                            { id: "type", header: "Тип", fillspace: 1 },
                        ],
                        subview: {
                            borderless:true,
                            select: true,
                            view:"datatable",
                            headerRowHeight:28,
                            resizeColumn: true,
                            columns:[
                                {id: "id", width: 50, header: [{text: "ID"}]},
                                {id: "ext_id", width: 110, header: [{text: "Внешний ID"}]},
                                {
                                    id: "action",
                                    width: 100,
                                    header: [{text: "Действие"}],
                                    template: (o) => {
                                        return Utils.translateOrderAction(o.action)
                                    }
                                },
                                {id: "volume", width: 70, header: [{text: "Объём"}], hidden: true},
                                {id: "weight", width: 50, header: [{text: "Вес"}], hidden: true},
                                {id: "gross_weight", width: 125, header: [{text: "Общая масса"}], hidden: true},
                                {id: "package_weight", width: 135, header: [{text: "Масса упаковки"}], hidden: true},
                                {
                                    id: "status",
                                    width: 75,
                                    header: [{text: "Статус"}],
                                    template: (o) => {
                                        return Utils.translateOrderStatus(o.status)
                                    }
                                },
                                {id: "failed_reason", width: 150, header: [{text: "Причина отказа"}], hidden: true},
                                {id: "plan_arrival", width: 150, header: [{text: "Прибытие план"}]},
                                {id: "plan_departure", width: 125, header: [{text: "Убытие план"}]},
                                {id: "fact_arrival", width: 125, header: [{text: "Прибытие факт"}]},
                                {id: "fact_departure", width: 125, header: [{text: "Убытие факт"}]},
                                {id: "comment", width: 125, header: [{text: "Комментарий"}], hidden: true},
                                {id: "hopper", width: 125, header: [{text: "Контейнер"}], hidden: true},
                                {id: "replacement_hopper", width: 200, header: [{text: "Контейнер для замены"}], hidden: true},
                            ],
                            autoheight:true,
                        },
                        on: {
                            onSubViewCreate:function (view, item) {
                                webix.ajax().get("/vehicles/containers").then(data => {
                                    data = data.json()
                                    item.orders.map(order => {
                                        let container = data.find(container => container.id === order.payload.hopper)
                                        if (container) order.hopper = container.name
                                        container = data.find(container => container.id === order.payload.replacement_hopper)
                                        if (container) order.replacement_hopper = container.name
                                    })
                                    view.parse(item.orders);
                                    if (item.orders) {
                                        item.orders.map((order) => {
                                            if (order.volume) $$(`$datatable${webix.subviewIndex}`).showColumn('volume')
                                            if (order.weight) $$(`$datatable${webix.subviewIndex}`).showColumn('weight')
                                            if (order.gross_weight) $$(`$datatable${webix.subviewIndex}`).showColumn('gross_weight')
                                            if (order.package_weight) $$(`$datatable${webix.subviewIndex}`).showColumn('package_weight')
                                            if (order.failed_reason) $$(`$datatable${webix.subviewIndex}`).showColumn('failed_reason')
                                            if (order.comment) $$(`$datatable${webix.subviewIndex}`).showColumn('comment')
                                            if (order.payload.hopper) $$(`$datatable${webix.subviewIndex}`).showColumn('hopper')
                                            if (order.payload.replacement_hopper) $$(`$datatable${webix.subviewIndex}`).showColumn('replacement_hopper')
                                        })
                                    }
                                    webix.subviewIndex++
                                })
                            },
                        }
                    },
                ]
            },
            on: {
                onShow: () => {
                    const form = $$("taskWindowGrid");
                    const obj = this;

                    webix.extend($$("taskWindowGrid"), webix.ProgressBar);
                    $$("taskWindowGrid").showProgress();
                    form.disable();

                    form.clearAll();
                    webix.ajax().get("/tasks/" + this.taskId).then(data => {
                        data = data.json()
                        let addresses = data[0].addresses
                        addresses.map(address => {
                            if (address.contractor) {
                                address.contractor = address.contractor.name
                            }
                        })
                        $$("taskWindowGrid").parse(addresses)

                        form.enable();
                        $$("userWindowForm").hideProgress();
                    })
                }
            }
        };
    }

    showWindow(taskId = undefined, callback = undefined) {
        this.taskId = taskId;
        this.callback = callback;

        this.ui(this.config()).show();
    }

    closeWindowWithoutAsk() {
        $$("taskWindow").close();
    }
}
