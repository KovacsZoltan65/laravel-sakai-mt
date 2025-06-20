import BaseService from '@/services/BaseService.js';

class HqEmployeeService extends BaseService
{
    constructor()
    {
        super();
        this.url = "hq.employees";
    }

    hq_getEmployees(params = {})
    {
        return this.post(route('hq.employees.fetch', params));
    }

    hq_storeEmployee(data)
    {
        return this.post(route(`${this.url}.store`), data);
    }

    hq_updateEmployee(id, data)
    {
        console.log(id, data);
        return this.put(route(`${this.url}.update`, { id }), data);
    }

    hq_deleteEmployees(data)
    {
        return this.delete(route(`${this.url}.delete.bulk'`), data);
    }

    hq_deleteEmployee(id, data = {})
    {
        //return this.delete(route(`${this.url}.delete`, { id }), {data});
        return this.apiClient.post(
            route(`${this.url}.delete`, { id }),
            data,
            { headers: { 'X-HTTP-Method-Override': 'DELETE' } }
        );
    }

    hq_restoreEmployee(id, data = {})
    {
        return this.put(route(`${this.url}.restore`, { id }), data);
    }

    hq_forceDeleteEmployee(id, data = {})
    {
        return this.delete(route(`${this.url}.force-delete`, { id }), data);
    }
}

export default new HqEmployeeService();
