import BaseService from "./BaseService";

class MenuService extends BaseService {
    constructor() {
        super();
    }

    url = '/menu-items';

    getMenuItems() {
        return this.get(this.url);
    }

    getMenuItem(id) {
        return this.get(`${this.url}/${id}`);
    }

    createMenuItem(data) {
        return this.post(this.url, data);
    }

    updateMenuItem(id, data) {
        return this.put(`${this.url}/${id}`, data);
    }

    deleteMenuItem(id) {
        return this.delete(`${this.url}/${id}`);
    }

    updateMenuUsage(id) {
        return this.put(`${this.url}/${id}/usage`);
    }
}

export default new MenuService();
