import BaseService from "@/services/BaseService.js";

class AcsSystemService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/acs_systems";
    }

    getAcsSystems(params = {}) {
        return this.post(route('tenant.acs_systems.fetch'), { params });
    }

    storeAcsSystem() {
        return this.post(route('tenant.acs_systems.store'), params);
    }

    updateAcsSystem(id, params) {
        //console.log('EmployeeService', id, params);
        return this.put(route('tenant.acs_systems.update', id), params);
    }

    deleteAcsSystems(data) {
        return this.delete(route('tenant.acs_systems.delete.bulk'), data);
    }

    deleteAcsSystem(id) {
        return this.delete(route('tenant.acs_systems.delete', id));
    }

    restoreAcsSystem(id) {
        return this.put(route('tenant.acs_systems.restore', id));
    }

    forceDeleteAcsSystem(id) {
        return this.delete(route('tenant.acs_systems.force-delete', id));
    }
}

export default new AcsSystemService();
