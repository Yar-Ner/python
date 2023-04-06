import {JetView} from "webix-jet";
import {Utils} from "../../libs/utils";

export default class VehicleModalsView extends JetView{
	constructor(app, name) {
		super(app, name)
		this.id = "vehicleWindow"
		this.form = "vehicleWindowForm"
	}

	config(){
		return {
			view:"window",
			position:"center",
			id: this.id,
			move:true,
			modal: true,
			resize: true,
			height: 600,
			width: 700,
			head: {
				view: "toolbar",
				paddingY: 1,
				height: 40,
				cols: [{view: "label", label: "Машина", align: "left"},
					{
						view: "icon", icon: "wxi-close", click: () => {
							this.closeWindow();
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
							{ view: "text", name: "ext_id", label: "Внешний ID", labelWidth: 190, value: 0, placeholder: "Введите внешний ID", required: true},
							{ view: "text", name: "name", label: "Название", inputAlign: "left", labelWidth: 190, placeholder: "Введите название", required: true },
							{ view: "text", name: "number", label: "Номер", inputAlign: "left", labelWidth: 190, placeholder: "Введите номер", required: true },
							{
								view: "richselect",
								name: "type",
								label: "Тип",
								inputAlign: "left",
								labelWidth: 190,
								placeholder: "Введите тип машины",
								options: {
									body: {
										template: "#name#"
									},
									url: "/vehicles/types"
								}
							},
							{ view: "textarea", name: "description", label: "Описание", inputAlign: "left", labelWidth: 190, placeholder: "Введите описание", },
							{
								cols: [
									{
										view: "multicombo",
										name: "usersId",
										label: "Пользователи",
										inputAlign: "left",
										labelWidth: 190,
										labelPosition: "left",
										options: {
											body: {
												template: "#fullname#"
											},
											url: "/users/short"
										},
										placeholder: "Выберите пользователей",
									},

								]
							},
							{
								cols: [
									{
										view: "multicombo",
										name: "devicesId",
										label: "Устройства",
										inputAlign: "left",
										labelWidth: 190,
										labelPosition: "left",
										options: {
											body: {
												template: "#name#"
											},
											url: "/devices/short"
										},
										placeholder: "Выберите устройства",
									},

								]
							},
							{
								view:"colorpicker", id: "color", name: "color", label:"Цвет", labelWidth:190,
								suggest:{
									padding:0,
									type:"colorselect", body: {
										button:true
									}
								}
							},
							{ view: "text", id: "weight", name: "weight", label:"Вес машины", labelWidth:190, placeholder: "Введите вес машины" },
							{ view: "switch", id: "active", name: "active", label:"Активна", labelWidth: 190 },
							{ view: "text", id: "odometer", name: "odometer", label:"Показание одометра", labelWidth: 190 }
						],
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
										webix.ajax().post("/vehicles/" + (values.id ? values.id : 0), values).then((data) => {
											form.enable();
											data = data.json();
											webix.message({
												type: "success",
												text: 'Сохранение прошло успешно'
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
										})
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

					if (this.vehicleId) {
						webix.extend($$(this.form), webix.ProgressBar);
						$$(this.form).showProgress();

						webix.ajax().get("/vehicles/" + this.vehicleId).then((data) => {
							form.enable();
							this.initValues = data.json()
							form.setValues(data.json())

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
	showWindow(vehicleId = undefined, callback = undefined){
		this.callback = callback;
		this.vehicleId = vehicleId;

		this.ui(this.config()).show();
	}
	closeWindow(){
		const values = $$(this.form).getValues()
		let changed = 0

		if (this.vehicleId !== undefined) {
			for (let key in values) {
				if (['ext_id', 'color', 'weight'].includes(key) && this.initValues[key] === null) this.initValues[key] = ''
				if (this.initValues[key] === undefined) this.initValues[key] = ''

				if (['devicesId', 'usersId'].includes(key)) {
					if (Utils.compareArray(key, values, this.initValues)) changed = 1
					continue
				}

				if (values[key] != this.initValues[key]) changed = 1
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
	closeWindowWithoutAsk(){
		$$(this.id).close()
	}
}