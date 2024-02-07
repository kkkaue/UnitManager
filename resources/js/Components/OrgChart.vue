<script setup>
import { defineProps, ref, onMounted } from 'vue';
import { Button } from '@/Components/ui/button';

import unitService from '@/services/unitService';

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

// Função para criar um menu
const createContextMenu = () => {
  const contextMenu = new go.Adornment("Vertical");

  contextMenu.add(
    new go.TextBlock("Opções"),
    makeButton("Visualizar", (e, obj) => {
      const node = obj.part.adornedPart;
      // Aqui você pode adicionar a lógica para visualizar os dados da unidade
      console.log("Visualizar", node.data);
    }),
    makeButton("Editar", (e, obj) => {
      const node = obj.part.adornedPart;
      // Aqui você pode adicionar a lógica para editar a unidade
      console.log("Editar", node.data);
    }),
    makeButton("Excluir", (e, obj) => {
      const node = obj.part.adornedPart;
      // Aqui você pode adicionar a lógica para excluir a unidade
      console.log("Excluir", node.data);
    })
  );

  return contextMenu;
}

// Função para criar um botão para o menu de contexto
const makeButton = (text, action) => {
  const button = new go.TextBlock(text);
  button.click = action;
  return button;
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
    myDiagram.zoomToFit();
    myDiagram.commitTransaction("save Changes");
    initialHierarchyData = JSON.parse(JSON.stringify(myDiagram.model.nodeDataArray));

    // Atualiza os dados no componente pai
    orgChartProps.data = myDiagram.model.nodeDataArray;
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

</script>

<template>
  <!-- Exibe o diagrama -->
  <div class="w-full h-3/5" @hierarchyChanged="hierarchyChanged = true">
    <div id="myDiagramDiv" class="w-full h-full bg-gray-100 border border-gray-200 rounded-md shadow-md">
    </div>
  </div>

  <!-- caso exista alguma alteração na hierarquia, exibe botões para salvar ou cancelar a alteração -->
  <div class="flex justify-end mt-4" v-if="hierarchyChanged">
    <Button variant="destructive" class="mr-2" @click="cancelHierarchyChanges">Cancelar</Button>
    <Button @click="saveHierarchyChanges">Salvar</Button>
  </div>
</template>