<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import cytoscape from 'cytoscape'

const container = ref(null)
const containerWrapper = ref(null)
const cy = ref(null)
const nodesWithChildren = ref([])

// Kiinduló node (CEO vagy bárki)
const ROOT_ID = '1'

onMounted(async () => {

    const res = await axios.get('/hierarchy/root');

    const baseNode = {
        data: {
            id: res.data.employee.id,
            label: res.data.employee.label,
            hasChildren: true,
            expanded: true
        }
    };

    const childNodes = res.data.children.map(child => ({
        data: {
            id: child.id,
            label: child.label,
            hasChildren: child.hasChildren,
            expanded: false
        }
    }));

    const edges = res.data.children.map(child => ({
        data: {
            source: res.data.employee.id,
            target: child.id
        }
    }));

    cy.value = cytoscape({
        container: container.value,
        elements: [baseNode, ...childNodes, ...edges],
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

    cy.value.ready(() => updatePlusButtons())
    cy.value.on('render zoom pan', updatePlusButtons)
})

// + gombos node-ok újrapozicionálása
function updatePlusButtons() {
    nodesWithChildren.value = cy.value.nodes().filter(n =>
        n.data('hasChildren') && !n.data('expanded')
    )
}

// + ikon elhelyezése
function getPlusPosition(node) {
    const pos = node.renderedPosition()
    return {
        top: `${pos.y - 30}px`,
        left: `${pos.x + 20}px`
    }
}

// API-ból gyerekek betöltése
async function onExpand(node) {
    if (node.data('expanded')) return

    try {
        const res = await axios.get(`/api/hierarchy/children/${node.id()}`)

        const newNodes = res.data.children.map(child => ({
            data: {
                id: child.id,
                label: child.label,
                hasChildren: child.hasChildren,
                expanded: false
            }
        }))

        const newEdges = res.data.children.map(child => ({
            data: {
                source: node.id(),
                target: child.id
            }
        }))

        cy.value.add([...newNodes, ...newEdges])
        node.data('expanded', true)

        cy.value.layout({
            name: 'breadthfirst',
            directed: true,
            padding: 10,
            spacingFactor: 1.4,
            animate: true
        }).run()

        updatePlusButtons()
    } catch (err) {
        console.error('API hiba a bővítés során:', err)
    }
}
</script>

<template>
    <div ref="containerWrapper" class="relative w-full h-full bg-gray-100 overflow-hidden">
        <!-- Cytoscape -->
        <div ref="container" class="absolute inset-0 z-0"></div>

        <!-- DOM overlay + ikonok -->
        <div v-for="node in nodesWithChildren" :key="node.id()" class="absolute z-10" :style="getPlusPosition(node)">
            <button
                class="w-5 h-5 text-xs bg-blue-600 text-white rounded-full shadow flex items-center justify-center hover:bg-blue-700"
                @click="onExpand(node)">
                +
            </button>
        </div>
    </div>
</template>
