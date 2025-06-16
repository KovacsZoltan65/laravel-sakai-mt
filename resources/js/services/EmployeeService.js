import BaseService from './BaseService.js';

class EmployeeService extends BaseService
{
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
        return this.post(route('employees.fetch'), { params });
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
}

export default new EmployeeService();
