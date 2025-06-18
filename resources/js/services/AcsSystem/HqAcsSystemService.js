import BaseService from "@/services/BaseService.js";

class HqAcsSystemService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/acs_systems";
    }

    getAcsSystems(params = {}) {
        return this-this.post(route('hq.acs_systems.fetch'), { params });
    }
}

export default new HqAcsSystemService();
