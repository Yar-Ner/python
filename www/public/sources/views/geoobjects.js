import {JetView} from "webix-jet";

export default class GeoobjectsView extends JetView {
    config() {
        const config = {
            id: "geoobjectsMain",
            cols: [
                {$subview: "tables.geoobjects"},
                {view: "resizer"},
                {
                    apikey: "1c58e6ea-09bc-4f90-a0c5-e7e3a2b1ba88",
                    view: "yandex-map",
                    id: "map",
                    minWidth: 400,
                    zoom: 10,
                    center: [57.627398, 39.891040],
                    lang: "ru-RU"
                }
            ]
        };

        return webix.require({
            "https://cdn.webix.com/components/edge/yandexmap/yandexmap.js": true
        }).then(() => config);
    }

    init() {
    }
}