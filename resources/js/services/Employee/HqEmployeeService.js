import BaseService from '@/services/BaseService.js';

class HqEmployeeService extends BaseService
{
    constructor()
    {
        super();
        this.url = "/employees";
    }

    hq_getEmployees(params = {})
    {
        return this.post(route('employees.fetch'), params);
    }

    hq_storeEmployee(data) {
        return this.post(route('employees.store'), data);
    }

    hq_updateEmployee(id, data) {
        return this.put(route('employees.update', { id }), data);
    }

    hq_deleteEmployees(data) {
        return this.delete(route('employees.delete.bulk'), data);
    }

    hq_deleteEmployee(id, data = {}) {
        //return this.delete(route('employees.delete', { id }), data);
        return this.delete(route('employees.delete.bulk'), { data });
    }

    hq_restoreEmployee(id, data = {}) {
        return this.put(route('employees.restore', { id }), data);
    }

    hq_forceDeleteEmployee(id, data = {}) {
        return this.delete(route('employees.force-delete', { id }), data);
    }
}

export default new HqEmployeeService();
