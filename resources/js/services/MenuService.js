import BaseService from "@/services/BaseService.js";

class MenuService extends BaseService
{
    constructor()
    {
        super();
        this.url = "";
    }

    getMenu()
    {
        let items = [
            {
                items: [
                    {
                        label: 'home',
                        icon: 'pi pi-home',
                        to: null,
                        items: [
                        {
                            label: 'dashboard',
                            icon: 'pi pi-th-large',
                            to: 'http://company-02.mt/dashboard',
                            items: []
                        }
                        ]
                    },
                    {
                        label: 'administration',
                        icon: 'pi pi-cog',
                        to: null,
                        items: [
                        {
                            label: 'employees',
                            icon: 'pi pi-users',
                            to: 'http://company-02.mt/employees',
                            items: []
                        },
                        {
                            label: 'hierarchy',
                            icon: 'pi pi-share-alt',
                            to: 'http://company-02.mt/hierarchy',
                            items: []
                        },
                        {
                            label: 'companies',
                            icon: 'pi pi-building',
                            to: 'http://company-02.mt/companies',
                            items: []
                        }
                        ]
                    },
                    {
                        label: 'reports',
                        icon: 'pi pi-chart-line',
                        to: null,
                        items: [
                        {
                            label: 'monthly',
                            icon: 'pi pi-calendar',
                            to: null,
                            items: [
                            {
                                label: 'monthly_01',
                                icon: '',
                                to: 'http://company-02.mt/dashboard',
                                items: []
                            }
                            ]
                        },
                        {
                            label: 'annual',
                            icon: 'pi pi-file',
                            to: 'http://company-02.mt/dashboard',
                            items: []
                        }
                        ]
                    }
                ]
            }
        ];

        console.log('items', items);

        return items;
    }
    async getMenu_api() {

        const response = await this.get('/menu-items');
        return response.data;
    }
}

export default new MenuService();
