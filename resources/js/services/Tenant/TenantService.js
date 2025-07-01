import BaseService from "@/services/BaseService.js";

class TenantService extends BaseService
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
}

export default new TenantService();
