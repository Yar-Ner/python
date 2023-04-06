import "./styles/app.css";
import {JetApp} from "webix-jet";
import {Auth} from "libs/auth";
import ruLocale from "libs/chat_ru";

export default class InventoryApp extends JetApp {
	constructor(config){
		super(webix.extend({
			id:			  APPNAME,
			version:	VERSION,
			start:		"/main/map",			
			debug:		!PRODUCTION
		}, config, true));

		/* error tracking */
		this.attachEvent("app:error:resolve", function(name, error){
			window.console.error(error);
		});

		webix.Date.startOnMonday = true;
		webix.i18n.setLocale("ru-RU");

		if (!webix.env.touch && webix.env.scrollSize) 
		  webix.CustomScroll.init();

		chat.locales.ru = ruLocale;
	}
	render(root, url, parent) {
		webix.auth = new Auth();		

		webix.attachEvent("onBeforeAjax",
			function(mode, url, data, request, headers, files, promise){
				headers["token"] = webix.auth.token;
			}
		);

		webix.subviewIndex = 1;

		webix.auth.check(() => {
			super.render(root, url, parent);
		})
	}
}