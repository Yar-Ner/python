import {JetView} from "webix-jet";

export default class ContractorModalsView extends JetView {
	constructor(app, name) {
		super(app, name);
		this.id = "contractorWindow"
		this.form = "contractorWindowForm"
	}
	config() {
		return {
			view: "window",
			position: "center",
			id: this.id,
			move: true,
			modal: true,
			height: 400,
			width: 700,
			head: {
				view: "toolbar",
				paddingY: 1,
				height: 40,
				cols: [{view: "label", label: "Контрагент", align: "left"},
					{
						view: "icon", icon: "wxi-close", click: () => {
							this.closeWindow()
						}
					}
				]
			},
			body: {
				padding: 17,
				rows: [
					{
						view: "form", id: this.form, autoheight: true, scroll: true, elements: [
							{view: "text", name: "id", label: "id", value: 0, hidden: true},
							{
								view: "text",
								name: "ext_id",
								label: "Внешний ID",
								inputAlign: "left",
								labelWidth: 190,
								placeholder: "Введите внешний ID",
								required: true
							},
							{
								view: "text",
								name: "name",
								label: "Название",
								inputAlign: "left",
								labelWidth: 190,
								placeholder: "Введите название",
								required: true
							},
							{
								view: "text",
								name: "code",
								label: "Код",
								inputAlign: "left",
								labelWidth: 190,
								placeholder: "Введите код"
							},
							{
								view: "text",
								name: "inn",
								label: "ИНН",
								inputAlign: "left",
								labelWidth: 190,
								placeholder: "Введите ИНН",
								required: true
							},
							{
								view: "text",
								name: "comment",
								label: "Комментарий",
								inputAlign: "left",
								labelWidth: 190,
								placeholder: "Введите комментарий"
							},
							{
								view: "multicombo",
								name: "geoobjectsId",
								label: "Геообъекты",
								inputAlign: "left",
								labelWidth: 190,
								labelPosition: "left",
								options: {
									url: "/geoobjects/short"
								},
								placeholder: "Выберите геообъекты"
							}
						],
						rules:{
							inn: function (o) {
								return !isNaN(o) && ['10', '12', '13'].includes(o.length.toString())
							}
						},
						elementsConfig: {
							inputAlign: "left",
							labelPosition: "left"
						}
					},
					{
						margin: 10,
						cols: [
							{},
							{
								view: "button",
								label: "Сохранить",
								type: "form",
								align: "center",
								width: 120,
								click: () => {
									webix.extend($$(this.id), webix.ProgressBar);
									$$(this.id).showProgress({
										type: "icon",
										hide: true
									});
									const form = $$(this.form);
									const obj = this;

									if (form.validate()) {
										form.disable();
										const values = form.getValues();

										webix.ajax().post("/contractors/" + (values.id ? values.id : 0), values).then((data) => {
											form.enable();
											data = data.json();
											webix.message({
												type: "success",
												text: "Сохранение прошло успешно"
											});
											form.enable();
											obj.closeWindowWithoutAsk();

											if (obj.callback) {
												obj.callback();
											}
										}).catch(function (e) {
											let text = "Произошла ошибка обращения к серверу.\n"
											if (e.responseText)  text += JSON.parse(e.responseText).message
											if (e.status === 403) text = "Вам запрещено выполнять данное действие."
											webix.message({
												type: "error",
												text
											});
											form.enable();
											console.log(e);
										});
									} else {
										webix.message({
											type: "error",
											text: "Заполните обязательные поля"
										});
									}
								}
							},
							{
								view: "button", label: "Отмена", align: "center", width: 120, click: () => {
									this.closeWindow();
								}
							}
						]
					},
				]
			},
			on: {
				onShow: () => {
					const form = $$(this.form);
					const obj = this;

					form.clear();

					if (this.contractorId) {
						webix.extend($$(this.form), webix.ProgressBar);
						$$(this.form).showProgress();

						webix.ajax().get("/contractors/" + this.contractorId).then((data) => {
							data = data.json()

							data.geoobjectsId = data.addresses.map(address => {
								return address.id
							})

							form.enable()
							this.initValues = data
							form.setValues(data)

							setTimeout(() => $$(this.form).hideProgress(), 100)
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
			}
		};
	}

	showWindow(contractorId = undefined, callback = undefined) {
		this.callback = callback;
		this.contractorId = contractorId;

		this.ui(this.config()).show();
	}

	closeWindow() {
		const values = $$(this.form).getValues()
		let changed = 0

		if (this.contractorId !== undefined) {
			for (let key in values) {
				if (key === 'addresses') continue
				if (this.initValues[key] === undefined) this.initValues[key] = ''
				if (values[key] != this.initValues[key]) {
					changed = 1
				}
			}
		}

		if (changed === 1) {
			webix.confirm({
				text: "Вы не сохранили изменения! <br> Продолжить?",
				callback: (result) => {
					if (result) {
						$$(this.id).close()
					}
				}
			});
		} else {
			$$(this.id).close()
		}

	}

	closeWindowWithoutAsk() {
		$$(this.id).close()
	}
}
