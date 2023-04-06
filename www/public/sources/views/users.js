import {JetView} from "webix-jet";
import UserModalsView from "../views/modals/userModals";

export default class GroupsView extends JetView {
	config() {
		const config = {
			view: "datatable",
			localId: "grid",
			id: "usersGrid",
			select: true,
			css: "webix_header_border",
			columns: [
				{id: "id", header: [{text: "№"}], width: 50},
				{id: "fullname", header: [{text: "ФИО"}], fillspace: 1},
				{
					id: "state", header: [{text: "статус"}], fillspace: 1, template: (o) => {
						var txt = "<span class=" + this.getColorCircle(o.status) + "></span>";
						txt += "<span class = \"state" + o.status + "\">";
						txt += this.stateToTxt(o.status);
						txt += "</span>";
						return txt;
					}
				},
				{id: "username", header: [{text: "логин"}], fillspace: 1},
				{
					id: "groupsId", header: ["группы"], options: "/users/groups", fillspace: 1, template: function (o) {
						let options = $$("usersGrid").getColumns().find(item => item.id === "groupsId").collection.serialize();
						let str = [];

						for (let groupId of o.groupsId) {
							let opt = options.find(o => o.id == groupId);

							if (opt) {
								str.push(opt.name);
							}
						}

						return str.join(",");
					}
				},
				{id: "description", header: [{text: "описание"}], fillspace: 1},
			],
			on: {
				onItemDblClick: () => {
					this.editAction()
				}
			},
			data: []
		};

		return config;
	}

	reloadView() {
		webix.extend($$("usersGrid"), webix.ProgressBar);
		$$("usersGrid").showProgress({
			type: "icon",
			hide: true
		});

		$$("usersGrid").clearAll();
		$$("usersGrid").load("/users");
	}

	editAction() {
		let selectedRow = $$("usersGrid").getSelectedId();

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
		})
		this.on(this.app, "addAction", () => {
			this.window.showWindow(undefined,() => {
				this.reloadView()
			});
		})
		this.on(this.app, "editAction", () => {
			this.editAction()
		})
		this.on(this.app, "deleteAction", () => {
			let cb = () => { this.reloadView(); };
			let selectedRow = $$("usersGrid").getSelectedId();

			if (selectedRow && selectedRow.row) {
				webix.confirm({
					text: "Пользователь будет удален! <br> Продолжить?",
					callback: (result) => {
						if (result) {
							webix.ajax().del("/users/" + selectedRow.row).then((data) => {
								webix.message({
									type: "warning",
									text: 'Пользователь успешно удален!'
								});
								cb();
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
				});
			} else {
				webix.message({
					type: "error",
					text: "Выберите пользователя для удаления."
				});
			}
		})
	}

	init() {
		$$("addAction").show()
		$$("editAction").show()
		$$("deleteAction").show()

		this.window = this.ui(UserModalsView);

		this.setActionHandlers();
		this.reloadView();
	}

	stateToTxt(state) {
		let txt = "";
		switch (state) {
			case "1":
				txt = "активный";
				break;
			case "2":
				txt = "заблокирован";
				break;
			case "3":
				txt = "удален";
				break;
			case "4":
				txt = "скрытый";
				break;
			default:
				txt = state;
				break;
		}
		return txt;
	}

	getColorCircle(state) {
		let txt = "";
		switch (state) {
			case "1":
				txt = "greencircle_state";
				break;
			case "2":
				txt = "redcircle_state";
				break;
			case "3":
				txt = "redcircle_state";
				break;
			case "4":
				txt = "disablecircle_state";
				break;
			default:
				txt = state;
				break;
		}
		return txt;
	}
}