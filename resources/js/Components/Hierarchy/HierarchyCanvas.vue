<template>
    <div class="w-full h-full bg-gray-100 relative">
        <button
            v-if="historyStack.length > 0"
            @click="goBack"
            class="absolute z-10 top-4 left-4 bg-blue-600 text-white px-3 py-1 rounded shadow"
        >
            ← Vissza
        </button>
        <div ref="container" class="w-full h-full"></div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import cytoscape from 'cytoscape'

const container = ref(null)
const cy = ref(null)
const historyStack = ref([])

const currentNodeId = ref(null)

onMounted(() => {
    fetchRoot()
})

async function fetchRoot() {
    try {
        const response = await axios.get('/hierarchy/root')
        renderGraph(response.data.employee, response.data.children)
    } catch (error) {
        console.error('Root lekérés hiba:', error)
    }
}

async function fetchChildren(employeeId) {
    try {
        const response = await axios.get(`/hierarchy/children/${employeeId}`)
        renderGraph(response.data.employee, response.data.children)
    } catch (error) {
        console.error('Children lekérés hiba:', error)
    }
}

function renderGraph(root, children) {
    if (cy.value) {
        cy.value.destroy()
    }

    const elements = [
        { data: { id: root.id, label: root.label, hasChildren: true } },
        ...children.map((child) => ({
            data: {
                id: child.id,
                label: child.label,
                hasChildren: child.hasChildren
            }
        })),
        ...children.map((child) => ({
            data: { source: root.id, target: child.id }
        }))
    ]

    currentNodeId.value = root.id

    cy.value = cytoscape({
        container: container.value,
        elements,
        layout: {
            name: 'breadthfirst',
            directed: true,
            padding: 10,
            spacingFactor: 1.4,
            animate: true
        },
        style: [
            {
                selector: 'node',
                style: {
                    label: 'data(label)',
                    'text-valign': 'center',
                    color: '#fff',
                    'font-size': 14,
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
            },
            {
                selector: 'node[hasChildren = 1]',
                style: {
                    'overlay-opacity': 0,
                    'background-image': 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"><circle cx="8" cy="8" r="8" fill="%23007bff"/><text x="4" y="12" font-size="12" fill="white">+</text></svg>',
                    'background-image-opacity': 1,
                    'background-fit': 'cover'
                }
            }
        ]
    })

    cy.value.on('tap', 'node', (evt) => {
        const node = evt.target
        if (node.data('hasChildren')) {
            historyStack.value.push(currentNodeId.value)
            fetchChildren(node.id())
        }
    })
}

function goBack() {
    const previousId = historyStack.value.pop()
    if (previousId) {
        fetchChildren(previousId)
    }
}
</script>
