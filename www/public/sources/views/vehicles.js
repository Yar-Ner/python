import {JetView} from "webix-jet";
import VehicleModalsView from "./modals/vehicleModals";

export default class VehiclesView extends JetView{
	config(){
        return {
            view: "datatable",
            id: "vehicleGrid",
            select: true,
            css: "webix_header_border",
			resizeColumn: true,
            columns: [
                {id: "id", width: 50, header: [{text: "ID"}]},
				{
					id: "active", header: [{text: "Активен"}], fillspace: 1, template: (o) => {
						let txt = "<span class=" + (o.active ? 'greencircle_state' : 'redcircle_state') + "></span>";
						txt += "<span class = \"state" + (o.active ? 1 : 5) + "\">";
						txt += o.active ? "Активна" : "Не активна"
						txt += "</span>";
						return txt;
					}
				},
                {id: "name", width: 150, header: [{text: "Название"}]},
                {id: "number", width: 100, header: [{text: "Номер"}]},
				{
					id: "type",
					width: 100,
					header: [{text: "Тип ТС"}],
					options: "/vehicles/types",
					template(o) {
						let options = $$("vehicleGrid").getColumns().find(item => item.id === "type").collection.serialize();
						const option = options.find(option => o.type === option.id)
						return option ? option.name : ''
					}
				},
                {id: "description", width: 200, header: [{text: "Описание"}]},
                {
                    id: "usersId",
                    width: 150,
                    header: ["Пользователи"],
                    options: "/users/short",
                    template: function (o) {
                        let options = $$("vehicleGrid").getColumns().find(item => item.id === "usersId").collection.serialize();
                        let str = [];

                        for (let usersId of o.usersId) {
                            let opt = options.find(o => o.id == usersId);

                            if (opt) {
                                str.push(opt.username);
                            }
                        }
                        return str.join(", ");
                    }
                },
                {
                    id: "devicesId", width: 150, header: ["Устройства"], options: "/devices/short", fillspace: 1,
                    template: function (o) {
                        let options = $$("vehicleGrid").getColumns().find(item => item.id === "devicesId").collection.serialize();

                        let str = [];
                        for (let devicesId of o.devicesId) {
                            let opt = options.find(o => o.id == devicesId);
                            if (opt) {
                                str.push(opt.name);
                            }
                        }
                        return str.join(", ");
                    }
                },
				{
					header: [{text: "Цвет"}], id:"color", name:"color"
				},
				{
					header: [{text: "Вес машины"}], id:"weight", name:"weight"
				},
				{
					header: [{text: "Одометр"}], id:"odometer", name:"odometer"
				}
            ],
			on: {
				onItemDblClick: () => {
					this.editAction()
				}
			},
            data: []
        };
	}

	reloadView() {
		webix.extend($$("vehicleGrid"), webix.ProgressBar);
		$$("vehicleGrid").showProgress({
			type: "icon",
			hide: true
		});
		$$("vehicleGrid").clearAll();
		$$("vehicleGrid").load("/vehicles");
	}

	editAction() {
		let selectedRow = $$("vehicleGrid").getSelectedId();

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
			let cb = () => { this.reloadView(); };
			let selectedRow = $$("vehicleGrid").getSelectedId();

			if (selectedRow && selectedRow.row) {
				webix.confirm({
					text: "Машина будет удалена! <br> Продолжить?",
					callback: (result) => {
						if (result) {
							webix.ajax().del("/vehicles/" + selectedRow.row).then((data) => {
								webix.message({
									type: "warning",
									text: 'Машина успешно удалена!'
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
					text: "Выберите машину для удаления."
				});
			}
		});
	}

	init(){
		$$("addAction").show()
		$$("editAction").show()
		$$("deleteAction").show()

		this.window = this.ui(VehicleModalsView);

		this.setActionHandlers();
		this.reloadView();
	}
}