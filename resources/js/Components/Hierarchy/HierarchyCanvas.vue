<template>
    <div ref="containerWrapper" class="relative w-full h-full bg-gray-100 overflow-hidden">
        <!-- Cytoscape canvas -->
        <div ref="container" class="absolute inset-0 z-0"></div>

        <!-- + ikonok DOM overlay-ként -->
        <div v-for="node in nodesWithChildren" :key="node.id()" class="absolute z-10" :style="getPlusPosition(node)">
            <button
                class="w-5 h-5 text-xs bg-blue-600 text-white rounded-full shadow flex items-center justify-center hover:bg-blue-700"
                @click="onExpand(node)">
                +
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import cytoscape from 'cytoscape'

const container = ref(null)
const containerWrapper = ref(null)
const cy = ref(null)
const nodesWithChildren = ref([])

const dummyGraph = {
    nodes: [
        { data: { id: '1', label: 'CEO', hasChildren: true } },
        { data: { id: '2', label: 'CTO', hasChildren: true } },
        { data: { id: '3', label: 'Dev Lead', hasChildren: false } },
        { data: { id: '4', label: 'CFO', hasChildren: false } },
        { data: { id: '5', label: 'HR', hasChildren: false } }
    ],
    edges: [
        { data: { source: '1', target: '2' } },
        { data: { source: '2', target: '3' } },
        { data: { source: '1', target: '4' } },
        { data: { source: '1', target: '5' } }
    ]
}

const employees = {};

const hierarchy = {};

onMounted(() => {
    cy.value = cytoscape({
        container: container.value,
        elements: [...dummyGraph.nodes, ...dummyGraph.edges],
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
                    'color': '#fff',
                    'font-size': 14,
                    'background-color': '#3b82f6',
                    'text-outline-color': '#3b82f6',
                    'text-outline-width': 2,
                    'width': 60,
                    'height': 60,
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

    // Frissítsük a + gombokat, miután elrendezte a gráfot
    cy.value.ready(() => {
        updatePlusButtons()
    })

    // Ha átméretezed az ablakot vagy zoomolsz: újrapozícionálás
    cy.value.on('render zoom pan', () => {
        updatePlusButtons()
    })
})

// Szűrés: csak azok a node-ok, akiknek van gyerekük
function updatePlusButtons() {
    nodesWithChildren.value = cy.value.nodes().filter(n => n.data('hasChildren'))
}

// + ikon pozíciója
function getPlusPosition(node) {
    const pos = node.renderedPosition()
    return {
        top: `${pos.y - 30}px`,
        left: `${pos.x + 20}px`
    }
}

// Dummy kibontás
function onExpand(node) {
    alert(`"${node.data('label')}" csomópont bővítése (itt lehet API-t hívni)`)
}
</script>
