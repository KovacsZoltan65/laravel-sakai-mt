<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import cytoscape from 'cytoscape';
import HierarchyService from '@/services/Hierarchy/HierarchySercice.js';

const container = ref(null);
let cy = null;

const stack = ref([]);
const showBackButton = computed(() => stack.value.length > 0);

const showSearchDialog = ref(false);
const searchTerm = ref('');

const contextMenu = ref({ visible: false, x: 0, y: 0, node: null });

const closeContextMenu = () => {
    contextMenu.value.visible = false
    contextMenu.value.node = null
};

const assignAction = (action) => {
    console.log(`Művelet: ${action} a(z)`, contextMenu.value.node.data());
    closeContextMenu();
};




const performSearch = async () => {
    if (!searchTerm.value.trim()) return;

    try {
        const res = await HierarchyService.search(searchTerm.value);

        if (res.data?.employee) {
            stack.value = [] // reset stack
            await loadGraph(res.data.employee.id);

            searchTerm.value = '';
            showSearchDialog.value = false;
        } else {
            alert('Nincs találat!')
        }

    } catch(err) {
        console.error('Keresési hiba:', err)
        alert('Hiba történt a keresés során.')
    } finally {
        //showSearchDialog.value = false;
    }
}

const handleSearch = async () => {
    showSearchDialog.value = true;
}

const goBack = () => {
    if (stack.value.length === 0) return

    const previous = stack.value.pop()
    if (previous) {
        renderHierarchy(previous.employee, previous.children)
    }
}

async function loadGraph(employeeId = null) {
    try {
        //const url = employeeId ? `/hierarchy/children/${employeeId}` : '/hierarchy/root';
        //const response = await axios.get(url);

        const response = await HierarchyService.getHierarchy(employeeId);
        const { employee, children } = response.data;

        // ✔️ Csak ha nem root szintű betöltés, akkor mentünk az előző állapotot
        if (employeeId && cy) {
            const previousEmployee = cy.nodes().filter(node => node.incomers().length === 0)[0]
            const previousChildren = cy.nodes()
                .filter(node => node.outgoers().length === 0 && node.id() !== previousEmployee.id())
                .map(n => ({
                    id: n.id(),
                    label: n.data('label').replace(/\s+\+$/, ''),
                    hasChildren: n.data('hasChildren'),
                }))

            stack.value.push({
                employee: {
                    id: previousEmployee.id(),
                    label: previousEmployee.data('label').replace(/\s+\+$/, '')
                },
                children: previousChildren
            })
        }

        const elements = [
            { data: { id: employee.id, label: employee.label, hasChildren: children.length > 0 } },
            ...children.map(child => ({
                data: {
                    id: child.id,
                    label: child.hasChildren ? `${child.label} +` : child.label,
                    hasChildren: child.hasChildren
                }
            })),
            ...children.map(child => ({ data: { source: employee.id, target: child.id } }))
        ]

        cy.elements().remove()
        cy.add(elements)
        cy.layout({ name: 'breadthfirst', directed: true, padding: 10, spacingFactor: 1.4, animate: true }).run()
        cy.fit()
    } catch (err) {
        console.error('Hierarchy load error:', err)
    }
}

function renderHierarchy(employee, children) {
    const elements = [
        { data: { id: employee.id, label: employee.label, hasChildren: children.length > 0 } },
        ...children.map(child => ({
            data: {
                id: child.id,
                label: child.hasChildren ? `${child.label} +` : child.label,
                hasChildren: child.hasChildren
            }
        })),
        ...children.map(child => ({ data: { source: employee.id, target: child.id } }))
    ]

    cy.elements().remove()
    cy.add(elements)
    cy.layout({ name: 'breadthfirst', directed: true, padding: 10, spacingFactor: 1.4, animate: true }).run()
    cy.fit()
}

onMounted(() => {
    cy = cytoscape({
        container: container.value,
        zoomingEnabled: true,
        minZoom: 0.1,
        maxZoom: 1.5,
        style: [
            {
                selector: 'node',
                style: {
                    label: 'data(label)',
                    'text-valign': 'center',
                    'text-halign': 'center',
                    'color': '#fff',
                    'font-size': 12,
                    'text-wrap': 'wrap',
                    'text-max-width': 80,
                    'background-color': '#3b82f6',
                    'text-outline-color': '#3b82f6',
                    'text-outline-width': 2,
                    width: 60,
                    height: 60,
                    'font-weight': 'bold'
                }
            },
            {
                selector: 'edge',
                style: {
                    width: 2,
                    'line-color': '#60a5fa',
                    'target-arrow-color': '#60a5fa',
                    'target-arrow-shape': 'triangle'
                }
            }
        ]
    })

    // ⬇️ Fontos: add class after mount
    container.value.querySelector('canvas')?.classList.add('cy-core');

    cy.on('tap', 'node', (evt) => {
        const node = evt.target
        if (node.data('hasChildren')) {
            loadGraph(node.id())
        }
    });

    cy.on('cxttap', 'node', evt => {
        const node = evt.target
        contextMenu.value = {
            visible: true,
            x: evt.originalEvent.clientX,
            y: evt.originalEvent.clientY,
            node
        }
    });

    window.addEventListener('click', closeContextMenu);
    loadGraph();
})
</script>

<style scoped>
.cy-core {
  pointer-events: none;
}
</style>

<template>
    <div ref="container" class="w-full h-full bg-gray-100 overflow-hidden relative">
        <!-- Keresés -->
        <Button
            label="Keresés"
            @click="handleSearch"
            class="mr-2"
        />
        <!-- Vissza gomb -->
        <Button
            v-if="showBackButton"
            label="← Vissza"
            @click="goBack"
        />

        <!-- Kontextus menü -->
        <div
            v-if="contextMenu.visible"
            :style="{ top: contextMenu.y + 'px', left: contextMenu.x + 'px' }"
            class="absolute z-50 bg-white shadow-lg border rounded text-sm"
        >
            <ul>
                <li
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="assignAction('add')"
                >
                    ✎ Dolgozó hozzáadása
                </li>
                <li
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="assignAction('move')"
                >
                    ⇆ Áthelyezés
                </li>
                <li
                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                    @click="assignAction('remove')"
                >
                    ❌ Eltávolítás
                </li>
            </ul>
        </div>



        <!-- Search Dialog -->
        <Dialog v-model:visible="showSearchDialog" header="Keresés" modal >
            <div class="flex flex-col gap-4">

                <InputText
                    v-model="searchTerm"
                    placeholder="Pl.: John Doe"
                />

                <div class="flex justify-end gap-2">
                    <Button label="Mégse" severity="secondary" @click="showSearchDialog = false" />
                    <Button label="Keresés" @click="performSearch" />
                </div>
            </div>
        </Dialog>

    </div>
</template>
