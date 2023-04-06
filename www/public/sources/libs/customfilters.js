webix.protoUI({
    name: "my_suggest",
    $init: function (config) {
        const count = config.body.rows.length;
        if (count > 1) {
            config.body.rows[count] = {
                view: "button", label: "Очистить все", click: () => {
                    $$(this.config.master).setValue();
                }
            }
            config.body.rows[1].yCount = 10
        }
    }
}, webix.ui.checksuggest);