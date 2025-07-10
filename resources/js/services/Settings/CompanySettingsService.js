import BaseService from '@/services/BaseService.js';

class CompanySettingsService extends BaseService
{
    constructor()
    {
        super();
        this.url = "tenant.comp_settings";
    }

    getSettings(params = {})
    {
        return this.post(route(`${this.url}.fetch`), params);
    }

    createSetting(data) {
        return this.post(route(`${this.url}.store`), data);
    }

    updateSetting(id, data) {
        return this.put(route(`${this.url}.update`, id), data);
    }

    deleteSetting(id) {
        return this.delete(route(`${this.url}.destroy`, id));
    }
}

export default new CompanySettingsService();
