import BaseService from '@/services/BaseService.js';

class AppSettingsService extends BaseService
{
    constructor()
    {
        super();
        this.url = "app.settings";
    }

    getSettings(params = {})
    {
        return this.get(route(`${this.url}.fetch`), params);
    }
}

export default new AppSettingsService();
