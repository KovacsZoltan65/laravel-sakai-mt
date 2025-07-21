import BaseService from "@/services/BaseService.js";

class MenuService extends BaseService {
    constructor() {
        super();
        this.url = "hq.menu";
    }

    async fetchTree(params = {}) {
        return this.get(route(`${this.url}.fetch`), params);
    }

    async store(params) {
        return this.post(route(`${this.url}.store`), params);
    }

    async update(id, params) {
        //return this.put(route(`${this.url}.update`), id, params);
        return this.put(route(`${this.url}.update`, { menuItem: id }), params);
    }

    async delete(id) {
        return this.delete(route(`${this.url}.delete`, id));
    }

}

export default new MenuService();
