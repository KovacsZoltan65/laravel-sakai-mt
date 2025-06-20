import BaseService from "@/services/BaseService.js";

class HqCompanyService extends BaseService
{
    constructor()
    {
        super();
        this.url = "hq.companies";
    }

    hq_getCompanies(params = {})
    {
        return this.post(route(`${this.url}.fetch`), params);
    }

    hq_storeCompany(data)
    {
        return this.post(route(`${this.url}.store`), data);
    }

    hq_updateCompany(id, data)
    {
        return this.put(route(`${this.url}.update`), { id }, data);
    }

    hq_deleteCompanies(data)
    {
        return this.delete(route(`${this.url}.delete.bulk`), data);
    }

    hq_deleteCompany(id, data = {})
    {
        //return this.delete(route(`${this.url}.delete`, { id }), data);
        return this.apiClient.post(
            route(`${this.url}.delete`, { id }),
            data,
            { headers: { 'X-HTTP-Method-Override': 'DELETE' } }
        );
    }

    hq_restoreCompany(id, data = {})
    {
        return this.put(route(`${this.url}`, { id }), data);
    }

    hq_forceDeleteCompany(id, data = {})
    {
        return this.delete(route(`${this.url}.force-delete`, { id }), data);
    }
}

export default new HqCompanyService();
