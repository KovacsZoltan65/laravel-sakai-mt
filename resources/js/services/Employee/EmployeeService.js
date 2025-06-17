import BaseService from '../BaseService.js';

class EmployeeService extends BaseService
{
    getEmployees(params = {}) {
        return this.get(route('tenant.employees.index'), { params });
    }

    create(params) {
        return this.post(route('tenant.employees.store'), params);
    }

    update(id, params) {
        return this.put(route('tenant.employees.update', id), params);
    }

    delete(id) {
        return this.delete(route('tenant.employees.delete', id));
    }

    restore(id) {
        return this.put(route('tenant.employees.restore', id));
    }

    forceDelete(id) {
        return this.delete(route('tenant.employees.force-delete', id));
    }

    bulkDelete(ids) {
        //return this.delete(route('tenant.employees.delete.bulk'), { ids });
        return this.delete(route('tenant.employees.delete.bulk'), { data: { ids } });
    }

    /*
    constructor()
    {
        super();
        this.url = "/employees";
    }

    getEmployees(params = {})
    {
        return this.get(`${this.url}/fetch`, { params });
    }

    hq_getEmployees(params = {})
    {
        return this.post(`${this.url}/fetch`, params);
        //return this.post(route('employees.fetch'), params);
    }

    getEmployee(payload)
    {
        return this.post(this.url, payload);
    }

    getEmployeeByName(name)
    {
        return this.get(`${this.url}/name/${name}`);
    }

    createEmployee(data)
    {
        return this.post(this.url, data);
    }

    updateEmployee(id, payload) {
        return this.put(`${this.url}/${id}`, payload);
    }

    deleteEmployee(id) {
        return this.delete(`${this.url}/${id}`);
    }
    */
}

export default new EmployeeService();
