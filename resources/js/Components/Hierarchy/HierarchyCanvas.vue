<template>
    <div ref="container" class="w-full h-full bg-gray-100 overflow-hidden relative">
        <button
            v-if="showBackButton"
            @click="goBack"
            class="px-3 py-1 mb-3 ml-6 bg-white border rounded shadow text-sm"
        >
            ← Vissza
        </button>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import cytoscape from 'cytoscape'

const container = ref(null)
let cy = null

const stack = ref([])
const showBackButton = computed(() => stack.value.length > 0)

const goBack = () => {
    if (stack.value.length === 0) return

    const previous = stack.value.pop()
    if (previous) {
        renderHierarchy(previous.employee, previous.children)
    }
}

async function loadGraph(employeeId = null) {
    try {
        const url = employeeId ? `/hierarchy/children/${employeeId}` : '/hierarchy/root'
        const response = await axios.get(url)

        const { employee, children } = response.data

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

    cy.on('tap', 'node', (evt) => {
        const node = evt.target
        if (node.data('hasChildren')) {
            loadGraph(node.id())
        }
    })

    loadGraph()
})
</script>

<style scoped></style>
