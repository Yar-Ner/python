import {JetView} from "webix-jet";
import SetupModalsWindow from "../views/modals/setupModals"

export default class SetupView extends JetView {

    config() {
        const config = {
            id: "setupForm",
            view: "form",
            tooltip: true,
            borderless: true,
            elements: [
                {
                    view: "counter",
                    name: "updateTime",
                    label: "Периодичность синхронизации в секундах",
                    labelWidth: 350
                },
                {
                    view: "text",
                    name: "logistPhone",
                    label: "Номер телефона для связи с логистом",
					width: 500,
                    labelWidth: 350,
                    pattern: {mask: "###########", allow: /[0-9]/g}
                },
                {
                    view: "checkbox",
                    name: "arbitraryExecutionTasks",
                    label: "Выполнять задачи в произвольном порядке",
                    labelWidth: 350
                },
                {
                    view: "checkbox",
                    name: "AllowPhotoOutsideGeo",
                    label: "Разрешить фотофиксацию вне геообъекта",
                    labelWidth: 350
                },
                {
                    view: "checkbox",
                    name: "allOrdersComplete",
                    label: "Завершение МЛ без закрытия всех задач",
                    labelWidth: 350
                },
                {
                    view: "counter",
                    name: "countOfAttempt",
                    label: "Количество попыток для закрытия МЗ",
                    labelWidth: 350
                },
                {
                    view: "counter",
                    name: "countOfAttemptForCheckingInRadius",
                    label: "Количество попыток для проверки, вне радиуса",
                    labelWidth: 350
                },
                {
                    view: "counter",
                    name: "geoRadius",
                    label: "Значение радиуса по умолчанию",
                    labelWidth: 350
                },
                {
                    cols: [
                        {
                            view: "button", label: "Доп. настройки", click: () => {
                                this.ui(SetupModalsWindow).showWindow(undefined, 'default', () => this.reloadView())
                            }
                        },
                        {
                            view: "button", label: "Сохранить", click: () => {
                                const values = $$("setupForm").getValues()
								let changed = 0

								const settings = []
								for (let key in values) {
									if (values[key] != this.initValues[key]) changed = 1
									settings.push({
										val: `${values[key]}`,
										handle: key
									})
								}

								if (changed === 0) {
									webix.message({
										type: "info",
										text: "Нечего сохранять"
									});
									return
								}
								this.initValues = values
								webix.ajax().post("/settings/default/save", {
                                    settings
                                }).then(function (data) {
                                    webix.message({
                                        type: "success",
                                        text: "Сохранение прошло успешно"
                                    });
                                })
                            }
                        }
                    ]
                }
            ]
        };

        return config;
    }

	reloadView() {
		webix.ajax().get("/settings/default").then((data) => {
			data = data .json()

			const settings = {}
			data.map(setting => {
				settings[setting.handle] = setting.val
			})
			this.initValues = settings
			$$("setupForm").parse(settings)
		});
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

		this.setActionHandlers();
		this.reloadView();
    }
}