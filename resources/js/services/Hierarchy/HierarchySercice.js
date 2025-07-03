import BaseService from '../BaseService.js';

class HierarchyService extends BaseService
{
    constructor()
    {
        super();
        this.url = "tenant.hierarchy";
    }

    search(query)
    {
        return axios.get('/hierarchy/search', {
            params: { q: query }
        });

        //return axios.get(`${this.url}/search`, { params: { q: query } });
        //return axios.get(route(`${this.url}.search`, { params: { q: query } }));
    }

    getHierarchy(employeeId = null)
    {
        const url = employeeId
            ? `/hierarchy/children/${employeeId}`
            : '/hierarchy/root';

        return axios.get(url);
    }

    assign(childId, parentId)
    {
        return axios.post('/hierarchy/assign', { child_id: childId, parent_id: parentId });
    }

    reassign(childId, parentId)
    {
        return axios.post('/hierarchy/reassign', { child_id: childId, parent_id: parentId });
    }

    remove(childId)
    {
        return axios.post('/hierarchy/remove', { child_id: childId });
    }
}

export default new HierarchyService();
