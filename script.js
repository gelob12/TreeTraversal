(() => {
    'use strict';

    const treeData = JSON.parse(document.getElementById('tree-data').textContent);
    const algorithmCode = JSON.parse(document.getElementById('algorithm-code-data').textContent);
    let nodes = [];
    let traversalData = {};
    const traversalButtons = Array.from(document.querySelectorAll('.traversal-button'));
    const treeSelector = document.getElementById('tree-selector');
    const newRandomSetButton = document.getElementById('new-random-set');
    const treeWrap = document.getElementById('tree-wrap');
    const treeLines = document.getElementById('tree-lines');
    const treeNodes = document.getElementById('tree-nodes');
    const treeHeading = document.getElementById('tree-heading');
    const treeDescription = document.getElementById('tree-description');
    const treeType = document.getElementById('tree-type');
    const playButton = document.getElementById('play-button');
    const nextButton = document.getElementById('next-button');
    const resetButton = document.getElementById('reset-button');
    const speedSlider = document.getElementById('speed-slider');
    const speedValue = document.getElementById('speed-value');
    const selectedName = document.getElementById('selected-name');
    const selectedRule = document.getElementById('selected-rule');
    const selectedDescription = document.getElementById('selected-description');
    const sequenceOutput = document.getElementById('sequence-output');
    const progressText = document.getElementById('progress-text');
    const completionMessage = document.getElementById('completion-message');
    const pseudocodeList = document.getElementById('pseudocode-list');
    const javaList = document.getElementById('java-list');
    const codeStep = document.getElementById('code-step');
    const codeDescription = document.getElementById('code-description');

    let selectedKey = null;
    let visitedCount = 0;
    let timerId = null;

    function stopPlayback() {
        if (timerId !== null) {
            window.clearTimeout(timerId);
            timerId = null;
        }
        playButton.textContent = '▶ Play Animation';
    }

    function clearNodeStates() {
        nodes.forEach((node) => node.classList.remove('is-current', 'is-visited'));
    }

    function renderTree(structure, levelCount) {
        treeLines.replaceChildren();
        treeNodes.replaceChildren();

        const inorderNodes = [];
        function collectInorder(node, depth) {
            if (!node) return;
            collectInorder(node.left, depth + 1);
            inorderNodes.push({ node, depth });
            collectInorder(node.right, depth + 1);
        }
        collectInorder(structure, 0);

        const width = 700;
        const height = Math.max(360, 100 + (levelCount - 1) * 110);
        treeWrap.style.height = `${height}px`;
        treeLines.setAttribute('viewBox', `0 0 ${width} ${height}`);

        const positions = new Map();
        inorderNodes.forEach(({ node, depth }, index) => {
            positions.set(node.value, {
                x: ((index + 0.5) / inorderNodes.length) * width,
                y: 55 + depth * 110,
            });
        });

        function addEdges(node) {
            if (!node) return;
            const parentPosition = positions.get(node.value);
            [node.left, node.right].forEach((child) => {
                if (!child) return;
                const childPosition = positions.get(child.value);
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', parentPosition.x);
                line.setAttribute('y1', parentPosition.y);
                line.setAttribute('x2', childPosition.x);
                line.setAttribute('y2', childPosition.y);
                treeLines.appendChild(line);
            });
            addEdges(node.left);
            addEdges(node.right);
        }
        addEdges(structure);

        inorderNodes.forEach(({ node, depth }, index) => {
            const circle = document.createElement('div');
            circle.className = 'tree-node';
            circle.dataset.node = node.value;
            circle.textContent = node.value;
            circle.setAttribute('aria-label', `${node.value}, level ${depth + 1}`);
            circle.style.left = `${((index + 0.5) / inorderNodes.length) * 100}%`;
            circle.style.top = `${((55 + depth * 110) / height) * 100}%`;
            treeNodes.appendChild(circle);
        });
        nodes = Array.from(treeNodes.querySelectorAll('.tree-node'));
    }

    function renderCodeList(listElement, lines) {
        listElement.replaceChildren();
        lines.forEach((line) => {
            const item = document.createElement('li');
            item.textContent = line;
            listElement.appendChild(item);
        });
    }

    function resetCodeStep(message = 'Choose a traversal') {
        codeStep.textContent = message;
    }

    function updateOutput() {
        const order = selectedKey ? traversalData[selectedKey].order : [];
        sequenceOutput.textContent = order.slice(0, visitedCount).join(' → ') || '—';
        progressText.textContent = visitedCount === 0
            ? 'No nodes visited yet.'
            : `${visitedCount} of ${order.length} nodes visited.`;
        completionMessage.hidden = !selectedKey || visitedCount !== order.length;
    }

    function selectTraversal(key) {
        stopPlayback();
        selectedKey = key;
        visitedCount = 0;
        clearNodeStates();

        const traversal = traversalData[key];
        selectedName.textContent = traversal.name;
        selectedRule.textContent = traversal.rule;
        selectedDescription.textContent = traversal.description;
        codeDescription.textContent = `${traversal.name}: ${traversal.description}`;
        renderCodeList(pseudocodeList, algorithmCode[key].pseudocode);
        renderCodeList(javaList, algorithmCode[key].java);
        resetCodeStep('Ready · Step 0');
        traversalButtons.forEach((button) => {
            const isSelected = button.dataset.traversal === key;
            button.classList.toggle('is-selected', isSelected);
            button.setAttribute('aria-pressed', String(isSelected));
        });
        playButton.disabled = false;
        nextButton.disabled = false;
        updateOutput();
    }

    function selectTree(key) {
        stopPlayback();
        const tree = treeData[key];
        traversalData = tree.traversals;
        visitedCount = 0;
        renderTree(tree.structure, tree.levelCount);
        treeHeading.textContent = tree.name;
        treeDescription.textContent = tree.description;
        treeType.textContent = `${tree.nodeCount} nodes · ${tree.levelCount} levels`;
        treeWrap.setAttribute('aria-label', tree.name);

        if (selectedKey) {
            const traversal = traversalData[selectedKey];
            selectedName.textContent = traversal.name;
            selectedRule.textContent = traversal.rule;
            selectedDescription.textContent = traversal.description;
            codeDescription.textContent = `${traversal.name}: ${traversal.description}`;
            resetCodeStep('Ready · Step 0');
        }
        updateOutput();
    }

    function visitNextNode() {
        if (!selectedKey) return false;

        const order = traversalData[selectedKey].order;
        if (visitedCount >= order.length) {
            stopPlayback();
            updateOutput();
            return false;
        }

        const value = order[visitedCount];
        nodes.forEach((item) => {
            if (item.classList.contains('is-current')) {
                item.classList.remove('is-current');
                item.classList.add('is-visited');
            }
        });
        const node = nodes.find((item) => item.dataset.node === value);
        if (node) {
            node.classList.remove('is-visited');
            node.classList.add('is-current');
        }
        visitedCount += 1;

        codeStep.textContent = `Step ${visitedCount} of ${order.length} · Visiting ${value}`;
        updateOutput();
        if (visitedCount >= order.length) stopPlayback();
        return true;
    }

    function scheduleNextStep() {
        if (!selectedKey || visitedCount >= traversalData[selectedKey].order.length) {
            stopPlayback();
            return;
        }
        timerId = window.setTimeout(() => {
            timerId = null;
            if (visitNextNode()) scheduleNextStep();
        }, Number(speedSlider.value));
    }

    traversalButtons.forEach((button) => {
        button.addEventListener('click', () => selectTraversal(button.dataset.traversal));
    });

    treeSelector.addEventListener('change', () => selectTree(treeSelector.value));
    newRandomSetButton.addEventListener('click', () => window.location.reload());

    playButton.addEventListener('click', () => {
        if (!selectedKey) return;
        if (timerId !== null) {
            stopPlayback();
            return;
        }
        if (visitedCount >= traversalData[selectedKey].order.length) {
            visitedCount = 0;
            clearNodeStates();
            resetCodeStep('Ready · Step 0');
            updateOutput();
        }
        playButton.textContent = 'Ⅱ Pause Animation';
        scheduleNextStep();
    });

    nextButton.addEventListener('click', () => {
        stopPlayback();
        visitNextNode();
    });

    resetButton.addEventListener('click', () => {
        stopPlayback();
        selectedKey = null;
        visitedCount = 0;
        clearNodeStates();
        traversalButtons.forEach((button) => {
            button.classList.remove('is-selected');
            button.setAttribute('aria-pressed', 'false');
        });
        selectedName.textContent = 'Choose an algorithm above';
        selectedRule.textContent = '—';
        selectedDescription.textContent = 'Select a traversal to see its rule and explanation.';
        codeDescription.textContent = 'Select a traversal to see its pseudocode and Java implementation.';
        pseudocodeList.replaceChildren();
        javaList.replaceChildren();
        resetCodeStep();
        playButton.disabled = true;
        nextButton.disabled = true;
        updateOutput();
    });

    speedSlider.addEventListener('input', () => {
        speedValue.value = `${(Number(speedSlider.value) / 1000).toFixed(1)} sec / node`;
        speedValue.textContent = speedValue.value;
        // Apply a changed speed to an animation already in progress.
        if (timerId !== null) {
            stopPlayback();
            playButton.textContent = 'Ⅱ Pause Animation';
            scheduleNextStep();
        }
    });

    selectTree(treeSelector.value);
})();
