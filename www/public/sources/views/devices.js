import {JetView} from "webix-jet";
import DeviceModalsView from "./modals/deviceModals";

export default class DevicesView extends JetView {
	config() {
		const config = {
			view: "datatable",
			localId: "grid",
			id: "devicesGrid",
			select: true,
			css: "webix_header_border",
			resizeColumn: true,
			columns: [
				{id: "id", width: 50, header: [{text: "ID"}]},
				{id: "name", width: 300, header: [{text: "Название"}]},
				{id: "imei", width: 100, header: [{text: "IMEI"}]},
				{
					id: "vehicleId", header: ["Машины"], options: "/vehicles/short", fillspace: 1,
					template: function (o) {
						let options = $$("devicesGrid").getColumns().find(item => item.id === "vehicleId").collection.serialize();

						let str = [];
						for (let vehicleId of o.vehicleId) {
							let opt = options.find(o => o.id == vehicleId);
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
		webix.extend($$("devicesGrid"), webix.ProgressBar);
		$$("devicesGrid").showProgress({
			type: "icon",
			hide: true
		});
		$$("devicesGrid").clearAll();
		$$("devicesGrid").load("/devices");
	}

	editAction() {
		let selectedRow = $$("devicesGrid").getSelectedId();

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
			let selectedRow = $$("devicesGrid").getSelectedId();

			if (selectedRow && selectedRow.row) {
				webix.confirm({
					text: "Устройство будет удалено! <br> Продолжить?",
					callback: (result) => {
						if (result) {
							webix.ajax().del("/devices/" + selectedRow.row).then((data) => {
								webix.message({
									type: "warning",
									text: 'Устройство успешно удалено!'
								});
								cb();
							}).catch(function (e) {
								let text = "Произошла ошибка обращения к серверу"
								if (e.status === 403) text = "Вам запрещено выполнять данное действие."
								webix.message({
									type: "error",
									text
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
					text: "Выберите устройство для удаления."
				});
			}
		});
	}

	init() {
		$$("addAction").show()
		$$("editAction").show()
		$$("deleteAction").show()

		this.window = this.ui(DeviceModalsView);

		this.setActionHandlers();
		this.reloadView();
	}
}