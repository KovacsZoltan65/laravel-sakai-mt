import BaseService from "@/services/BaseService.js";

class CompanyService extends BaseService
{
    constructor() {
        super();
        this.url = 'tenant.companies';
    }

    getCompanies(params = {})
    {
        return this.post(route(`${this.url}.fetch`), {params});
    }

    storeCompany(params)
    {
        return this.post(route(`${this.url}.store`), {params});
    }

    updateCompany(id, params)
    {
        return this.put(route(`${this.url}.update`, id), params);
    }

    deleteCompanies(data)
    {
        return this.delete(route(`${this.url}.delete.bulk`), data);
    }

    deleteCompany(id)
    {
        //return this.delete(route(`${this.url}.delete`, id));
        return this.apiClient.post(
            route(`${this.url}.delete`, { id }),
            data,
            { headers: { 'X-HTTP-Method-Override': 'DELETE' } }
        );
    }

    restoreCompany(id)
    {
        return this.put(route(`${this.url}.restore`, id));
    }

    forceDeleteCompany(id)
    {
        return this.delete(route(`${this.url}.force-delete`, id));
    }
}

export default new CompanyService();
