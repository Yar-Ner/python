import {JetView, plugins} from "webix-jet";
import ChatModal from "./chatModal";
import UserModalsView from "../views/modals/userModals";

export default class TopView extends JetView {
    config() {
        const header = {
            cols: [
                {
                    view: "icon",
                    height: 58,
                    css: "menu",
                    width: 44,
                    icon: "mdi mdi-menu",
                    click: () => {
                        $$('menu').toggle()
                        if ($$('logobox').isVisible()) {
                            $$('logobox').hide()
                        } else {
                            $$('logobox').show()
                        }
                    }
                },
                {
                    type: "header",
                    borderless: true,
                    height: 58,
                    width: 170,
                    css: "logobox",
                    id: "logobox",
                    template: "<img src='/sources/styles/logo.svg' class='sidelogo'>"
                }
            ]
        }

        const sidebar = {
            id: "menu",
            view: "sidebar", css: "webix_dark", width: 200,
            data: this.getMenuData(),
            on: {
                onBeforeSelect(id) {
                    const item = this.getItem(id)
                    if (item.$parent !== 0) {
                        this.open(item.$parent)
                    } else if (item.$count > 0) {
                        this.open(id)
                    } else {
                        this.closeAll()
                    }

                    const node_item = this.getNode().querySelectorAll(`[webix_tm_id="${id}"]`)
                    if (node_item[0]) node_item[0].classList.add('webix_selected')
                },
                onItemClick(id) {
                    const HTMLCollection = this.getNode().getElementsByClassName('webix_selected')
                    for (let item of HTMLCollection) {
                        item.classList.remove('webix_selected')
                    }
                    if (isNaN(id)) {
                        this.$scope.show("./" + id)
                    }
                },
                'onSelectChange': (arr) => {
                    (arr && arr[0] === 'tasks') ? $$('showOnMap').show() : $$('showOnMap').hide()
                }
            }
        };

        const toolbar = {
            view: "toolbar",
            id: "appToolbar",
            padding: 9, height: 58,
            cols: [
                {
                    id: "action_buttons",
                    cols: [
                        {
                            view: "button",
                            id: "reloadAction",
                            label: "Обновить",
                            width: 130,
                            click: (id, event) => {
                                this.app.callEvent(id)
                            }
                        },
                        {
                            view: "button",
                            id: "addAction",
                            label: "Добавить",
                            width: 130,
                            click: (id, event) => {
                                this.app.callEvent(id)
                            }
                        },
                        {
                            view: "button",
                            id: "editAction",
                            label: "Редактировать",
                            width: 130,
                            click: (id, event) => {
                                this.app.callEvent(id)
                            }
                        },
                        {
                            view: "button", id: "deleteAction", label: "Удалить", width: 130, click: (id,event) => {
                                this.app.callEvent(id)
                            }
                        },
                        {
                            view: "button",
                            id: "showOnMap",
                            label: "Показать на карте",
                            width: 180,
                            hidden: !location.href.includes('tasks'),
                            click: () => {
                                const selection = $$('taskGrid').getSelectedItem();
                                if (Array.isArray(selection) || selection === undefined) {
                                    if (selection === undefined) {
                                        webix.message({
                                            type: "error",
                                            text: "Не выбрано ни одного маршрутного листа. Выберите хотя бы 1!"
                                        })
                                        return
                                    } else if (selection.length > 10) {
                                        webix.message({
                                            type: "error",
                                            text: "Выбрано слишком много маршрутных листов. Выберите меньше 10!"
                                        })
                                        return
                                    } else {
                                        const taskToParse = []
                                        selection.map(task => {
                                            taskToParse.push({
                                                "taskId": task.id,
                                                "vehicleId": task.vehicles_id
                                            })
                                        })
                                        webix.storage.local.put('tasksForShow', taskToParse)
                                        document.location.href = `#!/main/map`
                                    }
                                } else {
                                    document.location.href = `#!/main/map?taskId=${selection.id}&vehicles_id=${selection.vehicles_id}`
                                }
                            }
                        },
                    ]
                },
                {},
                {
                    view: "icon", id: "chatBadge", icon: "mdi mdi-chat", badge: 0, click: () => {
                        this.chatWindow.showWindow()
                    }
                },
                {view: "icon", id: "alarms-badge-button", icon: "mdi mdi-bell", popup: "toolbarAlarmsPopup"},
                {
                    template: `<image class="mainphoto" onclick="$$('toolbarSettingsPopup').show()" src="/img/nophoto.png">
					<span class="webix_icon mdi mdi-circle status green"></span>`,
                    width: 60, css: "avatar", borderless: true
                }
            ]
        };

        return {
            type: "clean", cols: [
                {rows: [header, sidebar]},
                {rows: [toolbar, {$subview: true}]}
            ]
        };
    }
    updateAlarmsCount() {
        webix.ajax().get('api/alarms/vehicles').then(data => {
            data = data.json()
            // $$('alarms-badge-button').define('badge', data.length);
            $$('alarms-badge-button').config.badge = data.length;
            $$("alarms-badge-button").refresh();
            if (data.length) {
                $$('toolbarAlarmsPopup').clearAll()
                data.map(alarm => {
                $$('toolbarAlarmsPopup').add({
                        id: alarm.id,
                        color: alarm.alarm_type == "error" ? "red" : alarm.alarm_type == "warning" ? "yellow" : "blue",
                        number: alarm.vehicle_number
                    })
                })
                webix.message({
                    text: "Вниманиe есть не рассмотреные тревоги!!!",
                    type: "error",
                    expire: 3000,
                });
            }
        })
    }

    init() {
        this.use(plugins.Menu, "menu");

		this.chatWindow = this.ui(ChatModal);
        const userProfile = this.ui(UserModalsView)

        webix.ui({
            view: "popup",
            id: "toolbarSettingsPopup",
            width: 150,
            css: "toolbarSettingsPopup",
            body: {
                view: "list",
                data: [
                    {id: "1", value: "profile", name: "Профиль"},
                    {id: "2", value: "exit", name: "Выход"},
                ],
                template: "#name#",
                autoheight: true,
                select: true,
                on: {
                    onItemClick: function (id, e, node) {
                        var item = this.getItem(id);
                        switch (item.value) {
                            case 'exit':
                                webix.auth.logout()
                                break
                            case 'profile':
                                userProfile.showWindow(webix.auth.userId)
                                break
                        }
                    }
                }
            }
        });

        webix.ui({
            view: "submenu",
            id: "toolbarAlarmsPopup",
            width: 150,
            type: {
                template: function (obj) {
                    return `${obj.number} <span class="webix_icon mdi mdi-circle alarm ${obj.color}"></span> <span class='webix_icon wxi-close' onclick="webix.closeClicked = true"></span>`
                }
            },
            autoheight: true,
            select: true,
            on: {
                onItemClick: (id, e, node) => {
                    if (webix.closeClicked) {
                        webix.ajax().post(`/api/alarm/read/${id}`)
                        $$('toolbarAlarmsPopup').remove(id)
                        $$('alarms-badge-button').config.badge -= 1;
                        $$("alarms-badge-button").refresh();
                        webix.closeClicked = false
                    } else {
                        this.alarmId = id
                        alarmPopup.show()
                    }
                }
            }
        });

        const alarmPopup = webix.ui({
            view: "window",
            top: 50,
            left: 50,
            id: "alarmWindow",
            move: true,
            height: 350,
            width: 400,
            resize: true,
            head: {
                view: "toolbar",
                paddingY: 1,
                height: 40,
                cols: [{ view: "label", label: "Тревога", align: "left" },
                    {
                        view: "icon", icon: "wxi-close", click: function () {
                            $$("alarmWindow").hide();
                        }
                    }
                ]
            },
            body: {
                rows: [
                    {
                        view: "form", id: "alarmWindowForm", autoheight: true, scroll: true, elements: [
                            { view: "text", name: "vehicle_number", label: "Номер машины", labelWidth: 200, readonly: true},
                            { view: "text", id: "fullname", name: "fullname", label: "ФИО водителя", labelWidth: 200, readonly: true},
                            { view: "text", name: "created", label: "Дата/время", labelWidth: 200, readonly: true},
                            { view: "text", name: "id", hidden: true, label: "id", value: 0, labelWidth: 200, readonly: true},
                            { view: "text", name: "alarm_name", label: "Событие", labelWidth: 200, readonly: true },
                            { view: "text", name: "alarm_text", label: "Сообщение от водителя", labelWidth: 200, readonly: true},
                            { view: "label", id: "location_id", name: "location_id", hidden: true },
                            { view: "label", id: "vehicles_id", name: "vehicles_id", hidden: true },
                            {
                                view: "button", label: "Посмотреть на карте", labelWidth: 200,
                                click: function () {
                                    const location_id = $$('location_id').getValue()
                                    const vehicle_id = $$('vehicles_id').getValue()
                                    let map = $$("map");
                                    if (map) {
                                        map = map.getMap()
                                        map.geoObjects.removeAll();
                                        webix.ajax().get(`/api/monitoring/locations/${vehicle_id}`, {location: location_id}).then((data) => {
                                            data = data.json()[0]
                                            map.geoObjects.add(new ymaps.GeoObject(
                                                {
                                                    geometry: {
                                                        type: "Point",
                                                        coordinates: [data.latitude, data.longitude]
                                                    },
                                                    properties: {
                                                        iconContent: $$('fullname').getValue()
                                                    }
                                                },
                                                {
                                                    preset: 'islands#redStretchyIcon',
                                                    draggable: false,
                                                    hasBalloon: false
                                                }
                                            ))
                                            map.setCenter([data.latitude, data.longitude])
                                            map.setZoom(14)
                                        })
                                    } else {
                                        document.location.href = `#!/main/map?location=${location_id}&vehicleId=${vehicle_id}`
                                    }
                                }
                            }
                        ]
                    },
                ]
            },
            on: {
                onShow: () => {
                    const form = $$("alarmWindowForm");
                    form.clear();

                    if (this.alarmId) {
                        webix.extend($$("alarmWindowForm"), webix.ProgressBar);
                        $$("alarmWindowForm").showProgress();
                        form.disable();

                        webix.ajax().get("api/alarms/vehicles", {id: this.alarmId}).then((data) => {
                            form.enable();
                            form.setValues(data.json()[0]);
                            $$("alarmWindowForm").hideProgress();
                        }).catch(function (e) {
                            webix.message({
                                type: "error",
                                text: "Произошла ошибка обращения к серверу"
                            });
                            form.enable();
                            console.log(e);
                            this.getRoot().hide();
                        });
                    } else {
                        form.setValues( { status: 1 } );
                    }
                }
            }
        })

        this.updateAlarmsCount()
        setInterval(() => {
            this.updateAlarmsCount()
        }, 10000)
    }

    getMenuData() {
        let data = [
            {id: "map", value: "Карта", icon: "mdi mdi-view-dashboard"},
            {id: "geoobjects", value: "Геообъекты", icon: "mdi mdi-chart-areaspline"},
            {id: "tasks", value: "Маршрутные листы", icon: "mdi mdi-table"},
            {id: "contractors", value: "Контрагенты", icon: "mdi mdi-layers"},
            {id: "vehicles", value: "Машины", icon: "mdi mdi-table-large",
            data: [
                {id: "vehiclesTypes", value: "Типы ТС", rules: ['admin', 'logist']},
                {id: "vehiclesContainers", value: "Типы тары", rules: ['admin', 'logist']},
            ]},
            {id: "devices", value: "Устройства", icon: "mdi mdi-view-column"},
            {
                value: "Управление", icon: "mdi mdi-settings", rules: ['admin'],
                data: [
                    {id: "users", value: "Пользователи"},
                    {id: "rules", value: "Права доступа"},
                    {id: "groups", value: "Группы"},
                    {id: "setup", value: "Настройки"},
                ]
            }
        ];
        let result = [];
        data.forEach(function (menuItem) {
            if (menuItem.hasOwnProperty('rules') && menuItem.rules) {
                if (webix.auth.isAllowedRoles(menuItem.rules)) {
                    if (menuItem.data && menuItem.data.length > 0) {
                        menuItem.data = menuItem.data.filter(item => {
                            if (item.hasOwnProperty('rules') && item.rules) {
                                return webix.auth.isAllowedRoles(item.rules)
                            } else return 1
                        })
                    }
                    result.push(menuItem);
                }
            } else {
                if (menuItem.data && menuItem.data.length > 0) {
                    menuItem.data = menuItem.data.filter(item => webix.auth.isAllowedRoles(item.rules))
                }
                result.push(menuItem);
            }
        })
        return result;
    }
}