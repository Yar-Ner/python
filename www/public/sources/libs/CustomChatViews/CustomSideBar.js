export default class CustomSideBar extends chat.views.sidebar {
     init(view) {
         super.init(view);
     }

    config() {
        const config = super.config();
        config.rows.splice(0, 1) //remove tabbar
        return config;
    }
}