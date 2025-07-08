import BaseService from '@/services/BaseService.js';

class AppSettingsService extends BaseService
{
    constructor()
    {
        super();
        this.url = "tenant.app_settings";
    }

    getSettings(params = {})
    {
        return this.post(route(`${this.url}.fetch`), params);
    }
}

export default new AppSettingsService();
