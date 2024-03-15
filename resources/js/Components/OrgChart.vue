<script setup>
import { defineProps, defineEmits, ref, onMounted, watch } from 'vue';
import { Button } from '@/Components/ui/button';

import unitService from '@/services/unitService';
import { BadgeInfo, Pencil, Trash } from 'lucide-vue-next';

// Definindo as propriedades que serão recebidas do componente pai
const orgChartProps = defineProps({
  data: {
    type: Object,
    required: true
  }
});

// Definindo as referências para o estado da hierarquia e os dados iniciais
let hierarchyChanged = ref(false);
let initialHierarchyData = [];

//Definindo os emissores de eventos para atualizar o valor do modelo no componente pai
const emitEvent = defineEmits([
  'view:unit',
  'edit:unit',
  'delete:unit'
]);

// Função para criar um menu
const createContextMenu = () => {
  var myContextMenu = new go.HTMLInfo();
  
  myContextMenu.div = document.getElementById("contextMenu");

  myContextMenu.show = function(obj, diagram, tool) {
    var node = obj.part;
    if (node !== null) {
      var cm = myContextMenu.div;
      cm.style.display = "flex";
      var mousePt = diagram.lastInput.viewPoint;
      cm.style.left = mousePt.x + 35 + "px";
      cm.style.top = mousePt.y + 240 + "px";

      var viewButton = document.getElementById("viewButton");
      viewButton.onclick = function() {
        emitEvent('view:unit', node.data);
        myContextMenu.hide();
      };

      var editButton = document.getElementById("editButton");
      editButton.onclick = function() {
        emitEvent('edit:unit', node.data);
        myContextMenu.hide();
      };

      var deleteButton = document.getElementById("deleteButton");
      deleteButton.onclick = function() {
        emitEvent('delete:unit', node.data);
        myContextMenu.hide(); 
      };
    }
  };

  myContextMenu.hide = function() {
    var cm = myContextMenu.div;
    cm.style.display = "none";
  };

  return myContextMenu;
};

// Função para criar um nó no diagrama
const createOrgChartNode = (text) => {
  const node = new go.Node("Auto", {
    background: "#ffffff",
    margin: 10,
    stroke: null,
    shadowColor: "rgba(0, 0, 0, 0.3)",
    shadowOffset: new go.Point(3, 3),
    shadowBlur: 5,
    zOrder: 1,
    selectionChanged: p => {
      // para sobrepor os outros nós caso esteja selecionado
      if (p.isSelected) {
        p.layerName = "Foreground";
      } else {
        p.layerName = "";
      }
    }
  }).add(
    new go.Shape("RoundedRectangle", {
      name: "RoundedRectangle",
      fill: "#ffffff",
      stroke: "#000000",
    }),
    new go.TextBlock(text, {
      margin: new go.Margin(12, 24, 12, 24),
      stroke: "#000000",
      font: "16px sans-serif"
    }).bind("text", "name")
  );

  // Adiciona o efeito de hover
  node.mouseEnter = function(e, obj) {
    obj.part.findObject("RoundedRectangle").fill = "#f3f4f6";
  };

  node.mouseLeave = function(e, obj) {
    obj.part.findObject("RoundedRectangle").fill = "#ffffff";
  };

  // Adiciona o efeito de arrastar e soltar
  node.mouseDragEnter = function(e, obj) {
    obj.part.findObject("RoundedRectangle").fill = "#f3f4f6";
    obj.part.findObject("RoundedRectangle").stroke = "#2563eb";
  };

  node.mouseDragLeave = function(e, obj) {
    obj.part.findObject("RoundedRectangle").fill = "#ffffff";
    obj.part.findObject("RoundedRectangle").stroke = "#000000";
  };

  node.mouseDrop = function(e, obj) {
    const draggedNode = e.diagram.selection.first();
    const droppedOnNode = obj.part;

    if (draggedNode !== null && droppedOnNode !== null) {
      const draggedNodeIsParent = checkIfChild(draggedNode, droppedOnNode);
      if (draggedNodeIsParent) {
        return;
      }
      draggedNode.data.parent = droppedOnNode.data.key;
      e.diagram.model.updateTargetBindings(draggedNode.data);
      e.diagram.rebuildParts();

      markHierarchyAsChanged();
    }
  };

  // Adiciona o menu de contexto
  node.contextMenu = createContextMenu();

  return node;
};

// Função para verificar se um nó é filho de outro
function checkIfChild(parentNode, childNode) {
  if (childNode.data.parent === parentNode.data.key) {
    return true;
  } else {
    return false;
  }
}

// Função para lidar com a alteração na hierarquia
const markHierarchyAsChanged = () => {
  hierarchyChanged.value = true;
};

// Função para cancelar as alterações
const cancelHierarchyChanges = () => {
  const myDiagram = go.Diagram.fromDiv("myDiagramDiv");
  myDiagram.startTransaction("cancel Changes");
  while (myDiagram.undoManager.canUndo()) {
    myDiagram.undoManager.undo();
  }
  myDiagram.zoomToFit();
  hierarchyChanged.value = false;

  // Restaura o estado inicial dos nós
  myDiagram.model.nodeDataArray = JSON.parse(JSON.stringify(initialHierarchyData));
  myDiagram.commitTransaction("cancel Changes");
};

// Função para salvar as alterações
const saveHierarchyChanges = async () => {
  const myDiagram = go.Diagram.fromDiv("myDiagramDiv");
  myDiagram.startTransaction("save Changes");
  const data = myDiagram.model.nodeDataArray;
  const units = data.map(unit => {
    return {
      unit_id: unit.key,
      parent_id: unit.parent,
    };
  });

  try {
    await unitService.updateHierarchy(units);
    hierarchyChanged.value = false;
  } catch (error) {
    console.error(error);
  } finally {
    myDiagram.commitTransaction("save Changes");
    initialHierarchyData = JSON.parse(JSON.stringify(myDiagram.model.nodeDataArray));
    myDiagram.zoomToFit();
  }
};

// Função executada quando o componente é montado
onMounted(() => {
  const myDiagram = new go.Diagram('myDiagramDiv', {
    "undoManager.isEnabled": true,
    layout: new go.TreeLayout(
      {
        angle: 90,
        layerSpacing: 35,
      }
    )
  });

  myDiagram.nodeTemplate = createOrgChartNode("Default Text");

  myDiagram.linkTemplate = new go.Link({
    routing: go.Link.Orthogonal,
    corner: 5,
  }).add( new go.Shape({ 
      strokeWidth: 1,
      stroke: "#d1d5db", 
    }))
  myDiagram.model = new go.TreeModel(orgChartProps.data);

  initialHierarchyData = JSON.parse(JSON.stringify(myDiagram.model.nodeDataArray));

  myDiagram.zoomToFit();
});

watch(() => orgChartProps.data, (newData) => {
  const myDiagram = go.Diagram.fromDiv("myDiagramDiv");
  myDiagram.model = new go.TreeModel(newData);
  initialHierarchyData = JSON.parse(JSON.stringify(myDiagram.model.nodeDataArray));
  myDiagram.zoomToFit();
});

</script>

<template>
  <div class="w-full h-3/5" @hierarchyChanged="hierarchyChanged = true">
    <div id="myDiagramDiv" class="w-full h-full bg-gray-100 border border-gray-200 rounded-md shadow-md">
    </div>
    <div id="contextMenu" class="flex-col border-4 border-white bg-white rounded-md shadow-lg" style="position: absolute; z-index: 1000; display: none;">
      <button id="viewButton" class="w-full text-sm font-medium items-center justify-start flex rounded border-gray-200 hover:bg-gray-100 py-1 px-4 text-left hover:text-gray-900">
        <BadgeInfo class="mr-2 text-gray-900" size="16" /> Visualizar
      </button>
      <button id="editButton" class="w-full text-sm font-medium flex items-center justify-start rounded border-gray-200 hover:bg-gray-100 py-1 px-4 text-left hover:text-gray-900">
        <Pencil class="mr-2 text-gray-900" size="16" /> Editar
      </button>
      <button id="deleteButton" class="w-full text-sm font-medium flex items-center justify-start rounded hover:bg-gray-100 py-1 px-4 text-left text-red-600 hover:text-red-800">
        <Trash class="mr-2 text-red-600" size="16" /> Excluir
      </button>
    </div>
  </div>
  <div class="flex justify-end mt-4" v-if="hierarchyChanged">
    <Button variant="destructive" class="mr-2" @click="cancelHierarchyChanges">Cancelar</Button>
    <Button @click="saveHierarchyChanges">Salvar</Button>
  </div>
</template>