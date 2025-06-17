import BaseService from '../BaseService.js';

class EmployeeService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/employees";
    }

    getEmployees(params = {}) {
        return this.post(route('tenant.employees.fetch'), { params });
    }

    storeEmployee(params) {
        return this.post(route('tenant.employees.store'), params);
    }

    updateEmployee(id, params) {
        //console.log('EmployeeService', id, params);
        return this.put(route('tenant.employees.update', id), params);
    }

    deleteEmployees(data) {
        return this.delete(route('tenant.employees.delete.bulk'), data);
    }

    deleteEmployee(id) {
        return this.delete(route('tenant.employees.delete', id));
    }

    restoreEmployee(id) {
        return this.put(route('tenant.employees.restore', id));
    }

    forceDeleteEmployee(id) {
        return this.delete(route('tenant.employees.force-delete', id));
    }
}

export default new EmployeeService();
