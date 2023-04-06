import { JetView } from "webix-jet";
import SetupModalsView from "./setupModals";
import {Utils} from "../../libs/utils";

export default class UserModalsView extends JetView {
	constructor(app, name) {
		super(app, name);
		this.id = "userWindow"
		this.form = "userWindowForm"
	}
	config() {
		const th = this

		return {
			view: "window",
			position: "center",
			id: this.id,
			move: true,
			modal: true,
			minHeight: 400,
			width: 700,
			head: {
				view: "toolbar",
				paddingY: 1,
				height: 40,
				cols: [{ view: "label", label: "Пользователь", align: "left" },
					{
						view: "icon", icon: "wxi-close", click: () => {
							this.closeWindow()
						}
					}
				]
			},
			body: {
				rows: [
					{
						view: "form", id: this.form, autoheight: true, scroll: true, elements: [
							{ view: "text", name: "id", label: "id", value: 0, hidden: true },
							{
								cols: [
									{
										view: "text",
										name: "fullname",
										label: "ФИО",
										required: true,
										readonly: !webix.auth.roles.includes("admin"),
										width: 300
									},
									{
										view: "select", name: "status", label: " Статус", required: true,
										width: 210,
										value: 1,
										disabled: !webix.auth.roles.includes("admin"),
										options: [
											{ id: "1", value: "Активный" },
											{ id: "2", value: "Заблокирован" },
											{ id: "3", value: "Удален" },
											{ id: "4", value: "Скрытый" }
										],
									},
								]
							},
							{
								cols: [
									{
										view: "text",
										name: "username",
										label: "Логин",
										placeholder: "",
										required: true,
										readonly: !webix.auth.roles.includes("admin"),
									},
									{
										view: "text", type: "password", name: "password", label: "Пароль", width: 210, placeholder: "Новый пароль",
										css:"webix_el_search", icon:"_showhide wxi-eye",
										hidden: !webix.auth.roles.includes("admin"),
										on:{
											onItemClick: function(id, e){
												if(e.target.className.indexOf("_showhide") > -1){
													const input = this.getInputNode();
													input.focus();
													webix.html.removeCss(e.target, "wxi-eye-slash");
													webix.html.removeCss(e.target, "wxi-eye");
													if(input.type == "text"){
														webix.html.addCss(e.target, "wxi-eye");
														input.type = "password";
													} else {
														webix.html.addCss(e.target, "wxi-eye-slash");
														input.type = "text";
													}
												}
											}
										}
									},
								]
							},
							{
								view: "textarea", name: "description", label: "Описание", readonly: !webix.auth.roles.includes("admin"),
							},
							{
								cols: [
									{
										view: "multicombo",
										name: "groupsId",
										label: "Группы",
										hidden: !webix.auth.roles.includes("admin"),
										options: {
											body: {
												template: "#name#"
											},
											url: "/users/groups",
										}
									},
									{
										view: "multicombo",
										name: "rulesId",
										label: "Права доступа",
										hidden: !webix.auth.roles.includes("admin"),
										options: {
											body: {
												template: "#name#"
											},
											url: "/users/rules"
										}
									}
								]
							}
						],
						elementsConfig: {
							inputAlign: "left",
							labelPosition: "top"
						}
					},
					{
						margin: 10,
						cols: [
							{},
							{
								view: "button",
								label: "Настройки",
								type: "form",
								align: "center",
								width: 120,
								hidden: !webix.auth.roles.includes("admin"),
								click: function () {
									this.$scope.ui(SetupModalsView).showWindow(th.userId, "user");
								}
							},
							{
								view: "button",
								label: "Сохранить",
								type: "form",
								align: "center",
								width: 120,
								hidden: !webix.auth.roles.includes("admin"),
								click: () => {
									const form = $$(this.form);
									const obj = this;

									if (form.validate()) {
										form.disable();
										const values = form.getValues();
										webix.ajax().post("/users/" + (values.id ? values.id : 0), values).then((data) => {
											form.enable();
											data = data.json();
											if (data && data.res === false) {
												webix.message({
													type: "error",
													text: data.message
												});
											} else {
												webix.message({
													type: "success",
													text: "Сохранение прошло успешно"
												});
												obj.closeWindowWithoutAsk();

												if (obj.callback) {
													obj.callback();
												}
											}
										}).catch(function (e) {
											webix.message({
												type: "error",
												text: "Произошла ошибка обращения к серверу"
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

					if (this.userId) {
						webix.extend($$(this.form), webix.ProgressBar);
						$$(this.form).showProgress();
						form.disable();

						webix.ajax().get("/users/" + this.userId).then((data) => {
							form.enable();
							this.initValues = data.json()
							form.setValues(data.json());
							$$(this.form).hideProgress();
						}).catch(function (e) {
							webix.message({
								type: "error",
								text: "Произошла ошибка обращения к серверу"
							});
							form.enable();
							console.log(e);
							obj.closeWindowWithoutAsk();
						});
					} else {
						form.setValues( { status: 1 } );
					}
				}
			}
		};
	}
	showWindow(userId = undefined, callback = undefined) {
		this.callback = callback;
		this.userId = userId;

		this.ui(this.config()).show();
	}
	closeWindow() {
		const values = $$(this.form).getValues()
		let changed = 0

		if (this.userId !== undefined) {
			for (let key in values) {
				if (this.initValues[key] === undefined) this.initValues[key] = ''
				if (['groupsId', 'rulesId'].includes(key)) {
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
						$$(this.id).close();
					}
				}
			});
		} else {
			$$(this.id).close();
		}
	}
	closeWindowWithoutAsk() {
		$$(this.id).close();
	}
}