import BaseService from "@/services/BaseService.js";

class MenuService extends BaseService
{
    constructor()
    {
        super();
        this.url = "";
    }

    getMenu() {
        return this.get('/menu-items');
    }
}

export default new MenuService();
