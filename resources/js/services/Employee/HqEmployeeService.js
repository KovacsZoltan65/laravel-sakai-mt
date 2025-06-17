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
        return this.post(route('hq.employees.fetch'), params);
    }

    hq_storeEmployee(data) {
        return this.post(route('hq.employees.store'), data);
    }

    hq_updateEmployee(id, data) {
        console.log(id, data);
        return this.put(route('hq.employees.update', { id }), data);
    }

    hq_deleteEmployees(data) {
        return this.delete(route('hq.employees.delete.bulk'), data);
    }

    hq_deleteEmployee(id, data = {}) {
        return this.delete(route('hq.employees.delete', { id }), {
            data // itt kell, hogy a data kulcsban legyen
        });
    }

    hq_restoreEmployee(id, data = {}) {
        return this.put(route('hq.employees.restore', { id }), data);
    }

    hq_forceDeleteEmployee(id, data = {}) {
        return this.delete(route('hq.employees.force-delete', { id }), data);
    }
}

export default new HqEmployeeService();
