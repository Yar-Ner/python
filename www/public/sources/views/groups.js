import {JetView} from "webix-jet";
import GroupModalsView from "../views/modals/groupModals";

export default class GroupsView extends JetView {
	constructor(app, name) {
		super(app, name);
		this.id = "groupsGrid"
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
				{id: "name", width: 300, header: [{text: "Название"}]},
				{id: "description", fillspace: 1, header: [{text: "Описание"}]},
				{
					id: "rulesId",
					fillspace: 1,
					header: [{text: "Список прав доступа"}],
					options: "/users/rules",
					template: (o) => {
						let options = $$(this.id).getColumns().find(item => item.id === "rulesId").collection.serialize();
						let str = [];

						for (let ruleId of o.rulesId) {
							let opt = options.find(o => o.id == ruleId);

							if (opt) {
								str.push(opt.name);
							}
						}

						return str.join(",");
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
		$$(this.id).load("/users/groups");
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
					text: "Группа будет удалена! <br> Продолжить?",
					callback: (result) => {
						if (result) {
							webix.ajax().del("/users/groups/" + selectedRow.row).then((data) => {
								webix.message({
									type: "warning",
									text: 'Группа успешно удалена!'
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
					text: "Выберите группу для удаления."
				});
			}
		});
	}

	init() {
		$$("addAction").show()
		$$("editAction").show()
		$$("deleteAction").show()

		this.window = this.ui(GroupModalsView);

		this.setActionHandlers();
		this.reloadView();
	}
}