import {JetView} from "webix-jet";
import {Utils} from "../libs/utils";

export default class MapView extends JetView {
	config() {
		let me = this
		const config = {
			type: "space", id: "mainMap", rows: [
				{
					scrollY: false,
					rows: [
						{
							cols: [
								{
									id: "starttime",
									view: "datepicker",
									timepicker: true,
									label: "Время начала",
									labelPosition: "top",
									height: 40
								},
								{
									id: "endtime",
									view: "datepicker",
									timepicker: true,
									label: "Время конца",
									labelPosition: "top"
								},
								{
									id: "task_id",
									view: "text",
									label: "ID задания",
									labelPosition: "top",
								},
								{
									id: "limit",
									view: "text",
									label: "Кол-во возвращаемых координат",
									labelPosition: "top"
								}
							]
						},
					]
				},
				{
					cols: [
						{
							apikey: "1c58e6ea-09bc-4f90-a0c5-e7e3a2b1ba88",
							view: "yandex-map",
							id: "map",
							zoom: 10,
							center: [57.627398, 39.891040],
							lang: "ru-RU"
						},
						{
							view: "resizer"
						},
						{
							width: 312,
							rows: [
								{
									id: "mapTree",
									type: {
										folder: ""
									},
									view: "treetable",
									tooltip: true,
									select: true,
									header: false,
									columns: [
										{
											id: "name", header: "Значение", fillspace: 1,
											template: "{common.treetable()} #name#"
										}
									],
									ready: function () {
										webix.tasksForShow = undefined
										me.reloadView()
									},
									on: {
										onAfterSelect: function (o) {
											delete webix.tasksForShow

											let findAll = ['vehicles', 'geoobjects', 'contractors'].includes(o.id)
											let rowName = findAll ? o.id : o.id.toString()[0] === 'v' ? 'vehicles' : (o.id.toString()[0] === 'g' ? 'geoobjects' : 'contractors')
											const map = $$("map").getMap();
											let filters = {
												from: webix.Date.dateToStr("%Y-%m-%d %H:%i:%s")($$("starttime").getValue()),
												to: webix.Date.dateToStr("%Y-%m-%d %H:%i:%s")($$("endtime").getValue()),
												tasks_id: $$("task_id").getValue(),
												limit: $$("limit").getValue(),
											}
											let hasFilters = Object.values(filters).reduce((previousValue, currentValue) => previousValue + currentValue)
											map.geoObjects.removeAll();
											if (hasFilters && o.id.toString()[1] === '.') { //multiRoute
												o.id = o.id.toString().slice(2)
												webix.extend($$("map"), webix.ProgressBar)
												$$("map").showProgress()
												$$("map").disable()
												const p = me.drawRoute(o.id, filters, 1)
												p.then(() => {
													$$("map").hideProgress()
													$$("map").enable()
												})
											} else {// one object from category
												let url
												if (findAll) {
													url = rowName === 'vehicles' ? '/api/monitoring' : `${rowName}`
													o.id = null
												} else {
													o.id = o.id.toString().slice(2)
													url = rowName === 'vehicles' ? '/api/monitoring' : `${rowName}/${o.id}`
												}

												let payload = {};
												if (rowName === 'vehicles') payload.vehicles_id = o.id
												if (rowName === 'geoobjects') payload.areas = 1
												if (['contractors', 'geoobjects'].includes(rowName)) payload.count = 10000

												webix.extend($$("mapTree"), webix.ProgressBar);
												$$("mapTree").showProgress();
												$$("mapTree").disable();

												webix.ajax().get(url, payload).then((data) => {
													$$("mapTree").hideProgress();
													$$("mapTree").enable();
													let placemarks = []
													if (rowName === 'vehicles') {
														if (findAll) {
															data = data.json()
															data.map(vehicle => {
																placemarks.push({
																	name: vehicle.name,
																	coordinates: [vehicle.latitude, vehicle.longitude],
																	color: vehicle.color
																})
															})
														} else {
															data = data.json()[0]
															data.lat = data.latitude
															data.long = data.longitude
														}
													} else if (rowName === "geoobjects") {
														if (findAll) {
															data = data.json().data
															data.map(geoobject => {
																const placemark = {
																	name: geoobject.name,
																	coordinates: [geoobject.lat, geoobject.long]
																}
																if (geoobject.name) placemark.balloonContent = `<div>Название: ${geoobject.name}</div>`;
																if (geoobject.address) placemark.balloonContent += ` <div>Адрес: ${geoobject.address}</div>`;
																placemarks.push(placemark);
															})
														} else data = data.json()
													} else { //contractors
														if (findAll) {
															const contractors = data.json().data
															contractors.map(contractor => {
																contractor.addresses.map(address => {
																	if (address.address && address.name) {
																		const placemark = {
																			name: address.address,
																			coordinates: [address.lat, address.long]
																		}
																		placemark.balloonContent = `<div>Контрагент: ${contractor.name}</div>`;
																		if (contractor.inn) placemark.balloonContent += `<div>ИНН: ${contractor.inn}</div>`;
																		if (contractor.comment) placemark.balloonContent += `<div>Комментарий: ${contractor.comment}</div>`;
																		placemark.balloonContent += address.address && `<div>Название адреса: ${address.name}</div>`;
																		placemarks.push(placemark);
																	}
																});
															})
														} else { //One Contractor!!!
															findAll = true; //for many addresses of contractor
															const contractor = data.json()
															contractor.addresses.map(address => {
																if (address.address && address.name) {
																	const placemark = {
																		name: address.address,
																		coordinates: [address.lat, address.long]
																	}
																	placemark.balloonContent = `<div>Контрагент: ${contractor.name}</div>`;
																	if (contractor.inn) placemark.balloonContent += `<div>ИНН: ${contractor.inn}</div>`;
																	if (contractor.comment) placemark.balloonContent += `<div>Комментарий: ${contractor.comment}</div>`;
																	placemark.balloonContent += `<div>Название адреса: ${address.name}</div>`;
																	placemarks.push(placemark);
																}
															});
														}
													}
													if (!findAll) {
														const placemark = {
															name: data.name,
															coordinates: [data.lat, data.long]
														}
														if (rowName === "vehicles") {
															placemark.color = data.color
														}
														if (rowName === "geoobjects") {
															placemark.balloonContent = data.name
															placemark.balloonContent += `<div>Адрес: ${data.address}</div>`
														}
														if (rowName === "contractors") {
															placemark.balloonContent = data.name
															if (data.inn) placemark.balloonContent += `<div>ИНН: ${data.inn}</div>`;
															if (data.comment) placemark.balloonContent += `<div>Комментарий: ${data.comment}</div>`
															if (data.address) placemark.balloonContent += `<div>Адрес: ${data.address.address}</div>`
														}
														placemarks.push(placemark)

													}
													placemarks.map(placemark => {
														map.geoObjects.add(new ymaps.GeoObject(
															{
																geometry: {
																	type: "Point",
																	coordinates: placemark.coordinates
																},
																properties: {
																	iconCaption: placemark.name,
																	balloonContent: placemark.balloonContent,
																}
															},
															{
																iconColor: placemark.color,
																draggable: false
															}
														));
														map.setCenter(placemark.coordinates);
													})
												})
											}
										},
										onresize: function () {
											setTimeout(() => {
												if ($$("map").getMap()) {
													$$("map").getMap().container.fitToViewport()
												}
											}, 1)
										}
									},
									data: [
										{ "id": "vehicles", "name": "Машины", "open": false },
										{ "id": "geoobjects", "name": "Площадки", "open": false },
										{ "id": "contractors", "name": "Контрагенты", "open": false },
									],
									scheme: {
										$change: function (row) {
											if (row["state2trouble"]) {
												row.$css = "order-late";
											}
										},
										$init: function (o) {
										}
									}
								},
								{
									hidden: true,
									id: "areaInfo",
									rows: [
										{
											view: "label",
											label: "Информация о площадке"
										},
										{
											cols: [
												{
													view: "text",
													id: "addressArea",
													label: "Адрес",
													labelPosition: "top"
												},
												{
													view: "text",
													id: "contractorArea",
													label: "Контрагент",
													labelPosition: "top"
												},
											]
										},
										{
											cols: [
												{
													view: "text",
													id: "factArrivalArea",
													label: "Время прибытия",
													labelPosition: "top"
												},
												{
													view: "text",
													id: "factDepartureArea",
													label: "Время убытия",
													labelPosition: "top"
												},
											]
										}
									]
								},
								{
									id: "taskInfo",
									rows: [
										{
											view: "label",
											label: "Информация о маршрутном листе"
										},
										{
											cols: [
												{
													view: "text",
													id: "taskStart",
													label: "Начало",
													labelPosition: "top"
												},
												{
													view: "text",
													id: "taskEnd",
													label: "Окончание",
													labelPosition: "top"
												},
											]
										},
										{
											cols: [
												{
													view: "text",
													id: "taskVehicle",
													label: "Машина",
													labelPosition: "top"
												},
												{
													view: "text",
													id: "taskUser",
													label: "Водитель",
													labelPosition: "top"
												},
											]
										},
										{
											cols: [
												{
													view: "text",
													id: "taskNumber",
													label: "Номер",
													labelPosition: "top",
												},
												{
													view: "text",
													id: "taskDistance",
													label: "Длина маршрута",
													labelPosition: "top"
												},
											]
										}
									]
								}
							]
						}
					]
				}
			]
		};

		return webix.require({
			"https://cdn.webix.com/components/edge/yandexmap/yandexmap.js": true
		}).then(() => config);
	}

	setActionHandlers() {
		this.on(this.app, "reloadAction", () => {
			this.reloadView();
		});
	}

	getUrlsToData(ind) {
		let url  = (window.location.href.slice(ind + 1)).split('&')
		let data = {};
		url.map(i => {
			i = i.split('=')
			data[`${i[0]}`] = i[1]
		})
		return data
	}

	reloadView() {
		webix.extend($$("mapTree"), webix.ProgressBar);
		$$("mapTree").showProgress();
		$$("mapTree").disable();

		const map = $$("map").getMap();
		if (map) map.geoObjects.removeAll();

		$$('areaInfo').hide()
		this.fillAreaInfo({})

		$$('mapTree').parse([
			{"id": "vehicles", "name": "Машины", "open": false},
			{"id": "geoobjects", "name": "Площадки", "open": false},
			{"id": "contractors", "name": "Контрагенты", "open": false}
		])
		let prevRow = $$('mapTree').getSelectedId()

		$$('mapTree').unselectAll()

		let tableData = $$('mapTree').serialize();

		let ind = window.location.href.indexOf('?')
		let urlsParam = this.getUrlsToData(ind)

		let tasksForShow = webix.storage.local.get('tasksForShow')
		if (tasksForShow) webix.tasksForShow = tasksForShow
		if (webix.tasksForShow) tasksForShow = webix.tasksForShow
		webix.storage.local.remove('tasksForShow')

		if (urlsParam.taskId && tasksForShow === null && webix.tasksForShow === undefined) {
			$$('task_id').setValue(urlsParam.taskId)
		}

		const vehiclesPromise = webix.ajax().get('/vehicles').then((result) => {
			const data = result.json()
			data.map(vehicle => {
				vehicle.id = 'v.'+ vehicle.id
			})
			tableData.find(n => n.id === 'vehicles').data = data
		})

		const geoobjectsPromise = webix.ajax().get('/geoobjects?areas=1').then((result) => {
			const data = result.json()
			data.data.map(geoobject => {
				geoobject.id = 'g.' + geoobject.id
			})
			tableData.find(n => n.id === 'geoobjects').data = data.data
		})

		const contractorsPromise = webix.ajax().get('/contractors?count=1000000').then((result) => {
			const data = result.json()
			data.data.map(contractor => {
				contractor.id = 'c.' + contractor.id
			})
			tableData.find(n => n.id === 'contractors').data = data.data
		})

		webix.promise.all([vehiclesPromise, geoobjectsPromise, contractorsPromise]).then(function () {
			$$('mapTree').parse(tableData);
			$$("mapTree").hideProgress();
			$$("mapTree").enable();

			if (urlsParam.vehicles_id && !urlsParam.location && tasksForShow === null && webix.tasksForShow === undefined) {
				$$('mapTree').open('vehicles')
				$$('mapTree').select(`v.${urlsParam.vehicles_id}`)
			}
			if(prevRow) {
				switch (prevRow.row.slice()[0]) {
					case 'v':
						$$('mapTree').open('vehicles')
						break
					case 'g':
						$$('mapTree').open('geoobjects')
						break
					case 'c':
						$$('mapTree').open('contractors')
						break
				}

				$$('mapTree').select(prevRow.row)
			}

			if (tasksForShow !== null || webix.tasksForShow !== undefined) {
				webix.extend($$("map"), webix.ProgressBar)
				$$("map").showProgress()
				$$("map").disable()
				const promises = []
				if (tasksForShow === null) tasksForShow = webix.tasksForShow
				tasksForShow.map((item, index) => {
					const promise = webix.drawRoute(item.vehicleId, {tasks_id: item.taskId}, index === tasksForShow.length - 1, 0)
					promises.push(promise)
				})
				webix.promise.all(promises).then(() => {
					$$("map").hideProgress();
					$$("map").enable();
				})
			}
		})
	}

	showPhoto(photosInfo) {
		webix.ui({
			view: "window",
			id: "photoWindow",
			move: true,
			maxHeight: 800,
			position: "center",
			modal: true,
			maxWidth: 900,
			resize: true,
			head: {
				view: "toolbar",
				paddingY: 1,
				height: 40,
				cols: [{ view: "label", label: "Фото", align: "left" },
					{
						view: "icon", icon: "wxi-close", click: function () {
							$$("photoWindow").hide();
						}
					}
				]
			},
			body: {
				rows: [
					{
						view: "form", id: "photoWindowForm", autoheight: true, scroll: true, elements: [
							{
								rows: [
									{
										cols: [
											{
												view: "text",
												id: "address",
												readonly: true,
												label: "Адрес",
												css: "bigPhotoInput",
												fillspace: 1
											},
											{
												view: "text",
												id: "contractor",
												readonly: true,
												label: "Контрагент",
												css: "bigPhotoInput",
												fillspace: 1
											}
										]
									},
									{
										cols: [
											{
												view: "text",
												id: "cargo_type",
												readonly: true,
												label: "Тип груза",
												css: "bigPhotoInput",
												fillspace: 1
											},
											{
												view: "text",
												id: "action",
												readonly: true,
												label: "Действие",
												css: "bigPhotoInput",
												fillspace: 1
											}
										]
									}
								]
							},
							{
								view: "template",
								id: "image",
								template: "<img class='bigPhoto' src=" + photosInfo.path + " alt='photo'>",
								scroll: "xy",
							}
						]
					},
				]
			},
			on: {
				onShow: () => {
					$$("image").getNode().firstChild.style.textAlign = "center"
					$$("address").setValue(photosInfo.address)
					$$("cargo_type").setValue(photosInfo.cargo_type)
					$$("action").setValue(Utils.translateOrderAction(photosInfo.action))
					$$("contractor").setValue(photosInfo.name)
				}
			}
		}).show()
	}

	fillAreaInfo({address, name, fact_arrival, fact_departure}) {
		$$("addressArea").setValue(address)
		$$("contractorArea").setValue(name)
		$$("factArrivalArea").setValue(fact_arrival)
		$$("factDepartureArea").setValue(fact_departure)
	}

	fillTaskInfo(starttime, endtime, vehicleName, driverName, number, distance) {
		$$('taskStart').setValue(starttime)
		$$('taskEnd').setValue(endtime)
		$$('taskVehicle').setValue(vehicleName)
		$$('taskUser').setValue(driverName)
		$$('taskNumber').setValue(number)
		$$('taskDistance').setValue(distance)
	}

	drawRoute(vehicleId, filters, onlyOne) {
		const map = $$("map").getMap()

		return webix.ajax().get(`/api/monitoring/locations/${vehicleId}`, filters).then((data) => {
			data = data.json()
			let multiRoutePoints = []
			let photosPoints = []
			let placemarks = []
			const trackPoints = data.track
			if (trackPoints.length) {
				trackPoints.map((point, index) => {
					multiRoutePoints.push([point.latitude, point.longitude])
					if (point.photos) {
						photosPoints.push({
							photos: point.photos,
							coordinates: [point.latitude, point.longitude],
							vehicleName: point.name
						})
					}

					if ([0, trackPoints.length - 1].includes(index)) {
						const iconContent = index === 0 ? 'Начало' : 'Конец'
						const placemark = new ymaps.GeoObject(
							{
								geometry: {
									type: "Point",
									coordinates: [point.latitude, point.longitude]
								},
								properties: {iconContent},
							},
							{ preset: "islands#redStretchyIcon" }
						)
						placemark.events.add(['click'], () => {
							$$("areaInfo").show()
							webix.fillTaskInfo(
								data.starttime,
								data.endtime,
								data.vehicleName,
								data.driverName,
								data.number,
								data.distance
							)
							webix.fillAreaInfo({
								fact_arrival: point.time
							})
						});
						map.geoObjects.add(placemark);
					}
				})

				const myPolyline = new ymaps.Polyline(multiRoutePoints, {}, {
					strokeColor: data.vehicleColor,
					strokeWidth: 3,
				});
				myPolyline.events.add(['click'], () => {
					$$("areaInfo").hide()
					webix.fillTaskInfo(
						data.starttime,
						data.endtime,
						data.vehicleName,
						data.driverName,
						data.number,
						data.distance
					)
				});
				map.geoObjects.add(myPolyline);

				//add PhotoPoints
				photosPoints.map(photoPoint => {
					let images = ''
					const photos = photoPoint.photos;
					photos.map(photoInfo => {
						images += `<image class="balloonPhoto" onclick='webix.showPhoto(${JSON.stringify(photoInfo)})' src="${photoInfo.path}">`
					})

					let template = ''
					if (photos[0].cargo_type) template += `<div><b>Тип бункера:</b> ${photos[0].cargo_type}</div>`
					if (photos[0].action) template += `<div><b>Действие:</b> ${Utils.translateOrderAction(photos[0].action)}</div>`
					if (images) template += `${images}`
					placemarks.push({
						coordinates: photoPoint.coordinates,
						balloonContent: template,
						areaInfo: photos[0]
					})
				})

				placemarks.map((placemark, index) => {
					const newPlacemark = new ymaps.GeoObject(
						{
							geometry: {
								type: "Point",
								coordinates: placemark.coordinates
							},
							properties: {
								balloonContent: placemark.balloonContent,
							},
						},
						{ preset: "islands#redDotIcon" }
					)
					newPlacemark.events.add(['click'], () => {
						$$("areaInfo").show()
						webix.fillTaskInfo(
							data.starttime,
							data.endtime,
							data.vehicleName,
							data.driverName,
							data.number,
							data.distance
						)
						placemark.areaInfo.vehicleName = data.vehicleName
						webix.fillAreaInfo(placemark.areaInfo)
					});
					map.geoObjects.add(newPlacemark);
					if (index === placemark.length - 1) map.setCenter(placemark.coordinates);
				})

				if (onlyOne) {
					webix.fillTaskInfo(
						data.starttime,
						data.endtime,
						data.vehicleName,
						data.driverName,
						data.number,
						data.distance
					)
				}
			} else {
				webix.message({
					type: "info",
					text: "Информация о пройденом маршруте отсутствует"
				})
			}
		})
	}

	init() {
		$$("addAction").hide()
		$$("editAction").hide()
		$$("deleteAction").hide()

		webix.showPhoto = this.showPhoto
		webix.fillAreaInfo = this.fillAreaInfo
		webix.fillTaskInfo = this.fillTaskInfo
		webix.drawRoute = this.drawRoute

		this.setActionHandlers();
	}
}
