export class Utils {
    constructor() {

    }

    static getUrlParam(param) {
        if (window.location.href) {
            let urls = window.location.href.split('?')[1];
            if (urls) {
                urls = urls.split('#')[0];
                if (urls) {
                    urls = urls.split('&');
                    let needle = urls.find(el => {
                        return el.indexOf(param) + 1;
                    });
                    if (needle) {
                        needle = needle.split('=')[1];
                        return needle;
                    }
                }
            }
        }

        return false;
    };

    static translateOrderAction(action) {
        switch (action) {
            case 'deliver':
                return 'Доставка'
            case 'delivergrab':
                return 'Доставить и вывезти'
            case 'grabdeliver':
                return 'Вывезти и доставить'
            case 'change':
                return 'Заменить'
            case 'transportation':
                return 'Транспортировка'
            case 'grab':
                return 'Вывоз'
        }
    }

    static translateTaskStatus(status) {
        switch (status) {
            case 'draft':
                return 'Черновик'
            case 'queued':
                return 'В очереди'
            case 'process':
                return 'В процессе'
            case 'done':
                return 'Выполнено'
        }
    }

    static translateOrderStatus(status) {
        switch (status) {
            case 'draft':
                return 'Черновик'
            case 'queued':
                return 'В очереди'
            case 'process':
                return 'В процессе'
            case 'done':
                return 'Выполнено'
            case 'failed':
                return 'Не выполнено'
        }
    }

    static compareArray(key, values, initValues) {
        values[key] = values[key] === '' ? [] : values[key].split(',')
        if (values[key].length !== initValues[key].length) {
            return 1
        }
        values[key].forEach(item => {
            const res = initValues[key].find(initItem => item == initItem)
            if (res === undefined) return 1
        })

        return 0
    }
}