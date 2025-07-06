import BaseService from '@/services/BaseService.js';

class CompanySettingsService extends BaseService
{
    constructor()
    {
        super();
        this.url = "company.settings";
    }

    getCompanySettings(params = {})
    {
        //
    }
}

export default new CompanySettingsService();
