// useDataTableFetcher.js
import { ref, reactive, watch } from "vue";
import { debounce, pickBy } from "lodash";

export function useDataTableFetcher(initialParams, fetchCallback, watchKeys = ['search', 'field', 'order']) {
    const isLoading = ref(false);
    const data = ref(null);

    const params = reactive({
        search: initialParams.search ?? '',
        field: initialParams.field ?? '',
        order: initialParams.order ?? '',
        page: 1
    });

    const fetchData = async () => {
        isLoading.value = true;

        const query = pickBy({
            page: params.page,
            search: params.search,
            field: params.field,
            order: params.order,
        });

        try {
            const response = await fetchCallback(query);
            data.value = response;
        } catch (error) {
            console.error('Hiba a lekérés során', error);
        } finally {
            isLoading.value = false;
        }
    };

    const debouncedFetch = debounce(() => {
        params.page = 1;
        fetchData();
    }, 300);

    watchKeys.forEach(key => {
        watch(() => params[key], debouncedFetch);
    });

    const onPageChange = (event) => {
        params.page = event.page + 1;
        fetchData();
    };

    const clearSearch = () => {
        params.search = '';
        params.page = 1;
        fetchData();
    };

    return {
        data,
        params,
        isLoading,
        fetchData,
        onPageChange,
        clearSearch,
    };
}
