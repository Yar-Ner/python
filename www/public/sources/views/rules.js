import {JetView} from "webix-jet";
import RuleModalsView from "../views/modals/ruleModals";

export default class RulesView extends JetView {
	constructor(app, name) {
		super(app, name);
		this.id = "ruleGrid"
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
				{id: "updated", width: 200, header: [{text: "Изменен"}]},
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
		$$(this.id).load("/users/rules");
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
			let cb = () => { this.reloadView(); };
			let selectedRow = $$(this.id).getSelectedId();

			if (selectedRow && selectedRow.row) {
				webix.confirm({
					text: "Правило будет удалено! <br> Продолжить?",
					callback: (result) => {
						if (result) {
							webix.ajax().del("/users/rules/" + selectedRow.row).then((data) => {
								webix.message({
									type: "warning",
									text: 'Правило успешно удалено!'
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
					text: "Выберите правило для удаления."
				});
			}
		});
	}

	init() {
		$$("addAction").show()
		$$("editAction").show()
		$$("deleteAction").show()

		this.window = this.ui(RuleModalsView);

		this.setActionHandlers();
		this.reloadView();
	}
}