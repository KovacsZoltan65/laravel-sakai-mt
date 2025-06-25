import BaseService from '../BaseService.js';

class EmployeeService extends BaseService
{
    constructor()
    {
        super();
        this.url = "tenant.employees";
    }

    getEmployees(params = {})
    {
        return this.post(route(`${this.url}.fetch`), params);
    }

    storeEmployee(params)
    {
        return this.post(route(`${this.url}.store`), params);
    }

    updateEmployee(id, params)
    {
        //console.log('EmployeeService', id, params);
        return this.put(route(`${this.url}.update`, id), params);
    }

    deleteEmployees(data)
    {
        return this.delete(route(`${this.url}.delete.bulk`), data);
    }

    deleteEmployee(id)
    {
        //return this.delete(route(`${this.url}.delete`, id));
        return this.apiClient.post(
            route(`${this.url}.delete`, { id }),
            data,
            { headers: { 'X-HTTP-Method-Override': 'DELETE' } }
        );
    }

    restoreEmployee(id)
    {
        return this.put(route(`${this.url}.restore`, id));
    }

    forceDeleteEmployee(id)
    {
        return this.delete(route(`${this.url}.force-delete`, id));
    }
}

export default new EmployeeService();
