export default class CustomSideBar extends chat.views['messages/toolbar'] {
    init(view) {
        super.init(view);
        view.getNode().style['position'] = 'fixed'
        view.getNode().style['z-index'] = '1'
    }

    config() {
         const config = super.config();

        config.elements.map(element => {
            if (element.hasOwnProperty('onClick')) {
                element.onClick = () => {};
            }
        })

        config.elements.pop()

        return config;
     }
}
