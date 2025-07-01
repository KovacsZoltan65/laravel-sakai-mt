import BaseService from "@/services/BaseService.js";

class HqTenantService extends BaseService
{
    constructor()
    {
        super();
        this.url = "hq.tenants";
    }

    getTenants(params = {})
    {
        return this.post(route(`${this.url}.fetch`), params);
    }

    storeTenant(data)
    {
        return this.post(route(`${this.url}.store`), data);
    }

    updateTenant(id, data)
    {
        return this.put(route(`${this.url}.update`, { id }), data);
    }
}

export default new HqTenantService();
