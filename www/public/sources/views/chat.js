import ChatModal from "./chatModal";

export default class ChatView extends ChatModal {
    config() {
        const config = super.config();

        return config.body.rows[0];
    }

    init(_$view, _$) {
        super.init(_$view, _$);

        if ($$('appToolbar')) {
            $$('appToolbar').hide();
        }
    }
}
