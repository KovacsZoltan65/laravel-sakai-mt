import BaseService from '../BaseService.js';

class HierarchyService extends BaseService
{
    constructor()
    {
        super();
        this.url = "tenant.hierarchy";
    }
}

export default new HierarchyService();
