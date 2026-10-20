<?php
require_once __DIR__ . '/TreeTraversal.php';

function treeToArray(?TreeNode $node): ?array
{
    if ($node === null) {
        return null;
    }

    return [
        'value' => $node->value,
        'left' => treeToArray($node->left),
        'right' => treeToArray($node->right),
    ];
}

function treeHeight(?TreeNode $node): int
{
    if ($node === null) {
        return 0;
    }

    return 1 + max(treeHeight($node->left), treeHeight($node->right));
}

function makeRandomTree(array $values): TreeNode
{
    shuffle($values);
    $root = new TreeNode((string) array_shift($values));
    $openChildSlots = [[$root, 'left'], [$root, 'right']];

    foreach ($values as $value) {
        $slotIndex = random_int(0, count($openChildSlots) - 1);
        [$parent, $side] = array_splice($openChildSlots, $slotIndex, 1)[0];
        $child = new TreeNode((string) $value);
        $parent->{$side} = $child;
        $openChildSlots[] = [$child, 'left'];
        $openChildSlots[] = [$child, 'right'];
    }

    return $root;
}

// Keep one familiar sample and generate four different random trees on each page load.
$trees = [
    'balanced' => [
        'name' => 'Balanced Binary Tree',
        'description' => 'A complete three-level tree with four leaf nodes.',
        'root' => new TreeNode('A',
            new TreeNode('B', new TreeNode('D'), new TreeNode('E')),
            new TreeNode('C', new TreeNode('F'), new TreeNode('G'))
        ),
    ],
];

for ($example = 1; $example <= 4; $example++) {
    $labels = range('A', 'Z');
    shuffle($labels);
    $trees['random' . $example] = [
        'name' => 'Random Tree ' . $example,
        'description' => 'A randomly generated binary tree with seven uniquely labeled nodes.',
        'root' => makeRandomTree(array_slice($labels, 0, 7)),
    ];
}

$treeData = [];
foreach ($trees as $key => $tree) {
    $root = $tree['root'];
    $treeData[$key] = [
        'name' => $tree['name'],
        'description' => $tree['description'],
        'nodeCount' => count(preorder($root)),
        'levelCount' => treeHeight($root),
        'structure' => treeToArray($root),
        'traversals' => [
            'preorder' => ['name' => 'Preorder', 'rule' => 'ROOT → LEFT → RIGHT', 'description' => 'Visit the root node first, then explore the left subtree, followed by the right subtree.', 'order' => preorder($root)],
            'inorder' => ['name' => 'Inorder', 'rule' => 'LEFT → ROOT → RIGHT', 'description' => 'Visit the left subtree first, then the root node, followed by the right subtree.', 'order' => inorder($root)],
            'postorder' => ['name' => 'Postorder', 'rule' => 'LEFT → RIGHT → ROOT', 'description' => 'Visit the left subtree, then the right subtree, and visit the root node last.', 'order' => postorder($root)],
            'levelOrder' => ['name' => 'Level-Order', 'rule' => 'TOP TO BOTTOM → LEFT TO RIGHT', 'description' => 'Visit the nodes level by level, starting from the root and moving from left to right.', 'order' => levelOrder($root)],
        ],
    ];
}

// Java and pseudocode examples shown beside the tree.
$algorithmCode = [
    'preorder' => [
        'pseudocode' => [
            ['text' => 'PREORDER(node)', 'kind' => ''],
            ['text' => 'if node is null: return', 'kind' => ''],
            ['text' => 'visit(node)', 'kind' => 'visit'],
            ['text' => 'PREORDER(node.left)', 'kind' => ''],
            ['text' => 'PREORDER(node.right)', 'kind' => ''],
        ],
        'java' => [
            ['text' => 'void preorder(Node node) {', 'kind' => ''],
            ['text' => '    if (node == null) return;', 'kind' => ''],
            ['text' => '    visit(node.value);', 'kind' => 'visit'],
            ['text' => '    preorder(node.left);', 'kind' => ''],
            ['text' => '    preorder(node.right);', 'kind' => ''],
            ['text' => '}', 'kind' => ''],
        ],
    ],
    'inorder' => [
        'pseudocode' => [
            ['text' => 'INORDER(node)', 'kind' => ''],
            ['text' => 'if node is null: return', 'kind' => ''],
            ['text' => 'INORDER(node.left)', 'kind' => ''],
            ['text' => 'visit(node)', 'kind' => 'visit'],
            ['text' => 'INORDER(node.right)', 'kind' => ''],
        ],
        'java' => [
            ['text' => 'void inorder(Node node) {', 'kind' => ''],
            ['text' => '    if (node == null) return;', 'kind' => ''],
            ['text' => '    inorder(node.left);', 'kind' => ''],
            ['text' => '    visit(node.value);', 'kind' => 'visit'],
            ['text' => '    inorder(node.right);', 'kind' => ''],
            ['text' => '}', 'kind' => ''],
        ],
    ],
    'postorder' => [
        'pseudocode' => [
            ['text' => 'POSTORDER(node)', 'kind' => ''],
            ['text' => 'if node is null: return', 'kind' => ''],
            ['text' => 'POSTORDER(node.left)', 'kind' => ''],
            ['text' => 'POSTORDER(node.right)', 'kind' => ''],
            ['text' => 'visit(node)', 'kind' => 'visit'],
        ],
        'java' => [
            ['text' => 'void postorder(Node node) {', 'kind' => ''],
            ['text' => '    if (node == null) return;', 'kind' => ''],
            ['text' => '    postorder(node.left);', 'kind' => ''],
            ['text' => '    postorder(node.right);', 'kind' => ''],
            ['text' => '    visit(node.value);', 'kind' => 'visit'],
            ['text' => '}', 'kind' => ''],
        ],
    ],
    'levelOrder' => [
        'pseudocode' => [
            ['text' => 'LEVEL_ORDER(root)', 'kind' => ''],
            ['text' => 'if root is null: return', 'kind' => ''],
            ['text' => 'queue ← [root]', 'kind' => ''],
            ['text' => 'while queue is not empty:', 'kind' => ''],
            ['text' => '    node ← queue.removeFirst()', 'kind' => ''],
            ['text' => '    visit(node)', 'kind' => 'visit'],
            ['text' => '    enqueue node.left if it exists', 'kind' => ''],
            ['text' => '    enqueue node.right if it exists', 'kind' => ''],
        ],
        'java' => [
            ['text' => 'void levelOrder(Node root) {', 'kind' => ''],
            ['text' => '    if (root == null) return;', 'kind' => ''],
            ['text' => '    Queue<Node> queue = new ArrayDeque<>();', 'kind' => ''],
            ['text' => '    queue.add(root);', 'kind' => ''],
            ['text' => '    while (!queue.isEmpty()) {', 'kind' => ''],
            ['text' => '        Node node = queue.remove();', 'kind' => ''],
            ['text' => '        visit(node.value);', 'kind' => 'visit'],
            ['text' => '        if (node.left != null) queue.add(node.left);', 'kind' => ''],
            ['text' => '        if (node.right != null) queue.add(node.right);', 'kind' => ''],
            ['text' => '    }', 'kind' => ''],
            ['text' => '}', 'kind' => ''],
        ],
    ],
];

foreach ($algorithmCode as &$algorithm) {
    foreach (['pseudocode', 'java'] as $language) {
        $algorithm[$language] = array_map(
            static fn(array $line): string => $line['text'],
            $algorithm[$language]
        );
    }
}
unset($algorithm);

$treeDataJson = json_encode($treeData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$algorithmCodeJson = json_encode($algorithmCode, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="An interactive classroom demonstration of binary tree traversals.">
    <title>Tree and Tree Traversal Simulation</title>
    <link rel="stylesheet" href="style.css">
    <script defer src="script.js"></script>
</head>
<body>
    <main class="page-shell">
        <header class="page-header">
            <p class="eyebrow">DATA STRUCTURES &amp; ALGORITHMS</p>
            <h1>TREE AND TREE TRAVERSAL SIMULATION</h1>
            <p class="subtitle">Data Structures and Algorithms – Interactive Demonstration</p>
        </header>

        <section class="card simulation-card" aria-labelledby="tree-heading">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">INTERACTIVE SIMULATION</p>
                    <h2 id="tree-heading">Balanced Binary Tree</h2>
                    <p id="tree-description" class="tree-description">A complete three-level tree with four leaf nodes.</p>
                </div>
                <div class="tree-selection">
                    <label for="tree-selector">Example tree</label>
                    <select id="tree-selector">
                        <?php foreach ($treeData as $key => $tree): ?>
                            <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tree['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="tree-type" id="tree-type">7 nodes · 3 levels</span>
                    <button type="button" id="new-random-set" class="random-set-button">Generate New Random Trees</button>
                </div>
            </div>

            <div class="simulation-layout">
                <div class="tree-column">
                    <div class="tree-wrap" id="tree-wrap" role="img" aria-label="Balanced binary tree">
                        <svg class="tree-lines" id="tree-lines" viewBox="0 0 700 360" preserveAspectRatio="none" aria-hidden="true"></svg>
                        <div class="tree-nodes" id="tree-nodes"></div>
                    </div>

                    <div class="legend" aria-label="Node color legend">
                        <span><i class="legend-dot default-dot"></i>Not visited</span>
                        <span><i class="legend-dot current-dot"></i>Current node</span>
                        <span><i class="legend-dot visited-dot"></i>Visited</span>
                    </div>
                </div>

                <aside class="code-panel" aria-labelledby="code-heading" aria-live="polite">
                    <div class="code-panel-heading">
                        <div>
                            <p class="eyebrow">ALGORITHM WALKTHROUGH</p>
                            <h3 id="code-heading">Pseudocode &amp; Java</h3>
                        </div>
                        <span id="code-step" class="code-step">Choose a traversal</span>
                    </div>
                    <p id="code-description" class="code-description">Select a traversal to see its pseudocode and Java implementation.</p>
                    <section class="code-section" aria-labelledby="pseudocode-heading">
                        <h4 id="pseudocode-heading">Pseudocode</h4>
                        <ol id="pseudocode-list" class="code-list" aria-label="Pseudocode"></ol>
                    </section>
                    <section class="code-section java-section" aria-labelledby="java-heading">
                        <h4 id="java-heading">Java implementation</h4>
                        <ol id="java-list" class="code-list" aria-label="Java implementation"></ol>
                    </section>
                    <p class="code-note">Assume <code>Node</code> has value, left, and right fields; <code>visit(value)</code> records the node.</p>
                </aside>
            </div>

            <div class="traversal-picker">
                <h3>Choose a traversal</h3>
                <div class="traversal-buttons" role="group" aria-label="Choose traversal algorithm">
                    <button type="button" class="traversal-button" data-traversal="preorder">Preorder Traversal</button>
                    <button type="button" class="traversal-button" data-traversal="inorder">Inorder Traversal</button>
                    <button type="button" class="traversal-button" data-traversal="postorder">Postorder Traversal</button>
                    <button type="button" class="traversal-button" data-traversal="levelOrder">Level-Order Traversal</button>
                </div>
            </div>

            <div class="player-controls" aria-label="Animation controls">
                <button type="button" class="control-button play-button" id="play-button" disabled>▶ Play Animation</button>
                <button type="button" class="control-button" id="next-button" disabled>Next Step</button>
                <button type="button" class="control-button reset-button" id="reset-button">Reset</button>
                <label class="speed-control" for="speed-slider">
                    <span>Speed</span>
                    <input id="speed-slider" type="range" min="300" max="2000" step="100" value="1000">
                    <output id="speed-value" for="speed-slider">1.0 sec / node</output>
                </label>
            </div>
        </section>

        <section class="card result-card" aria-labelledby="result-heading" aria-live="polite">
            <div class="result-heading-row">
                <div>
                    <p class="eyebrow">FOLLOW EACH VISIT</p>
                    <h2 id="result-heading">TRAVERSAL RESULT</h2>
                </div>
                <span id="completion-message" class="completion-message" hidden>Traversal Complete!</span>
            </div>
            <div class="result-details">
                <p><strong>Selected Traversal:</strong> <span id="selected-name">Choose an algorithm above</span></p>
                <p><strong>Traversal Rule:</strong> <span id="selected-rule">—</span></p>
                <p class="explanation" id="selected-description">Select a traversal to see its rule and explanation.</p>
            </div>
            <div class="sequence-box">
                <span class="sequence-label">Current Output</span>
                <p id="sequence-output" class="sequence-output" aria-label="Visited node sequence">—</p>
            </div>
            <p id="progress-text" class="progress-text">No nodes visited yet.</p>
        </section>

        <section class="card learning-card" aria-labelledby="learn-heading">
            <p class="eyebrow">QUICK REFERENCE</p>
            <h2 id="learn-heading">LEARN ABOUT TREES</h2>
            <div class="definition-grid">
                <article class="definition"><h3>Tree</h3><p>A non-linear, hierarchical data structure consisting of connected nodes without cycles.</p></article>
                <article class="definition"><h3>Root</h3><p>The topmost node, which has no parent.</p></article>
                <article class="definition"><h3>Parent</h3><p>A node that has one or more child nodes.</p></article>
                <article class="definition"><h3>Child</h3><p>A node directly connected below another node.</p></article>
                <article class="definition"><h3>Leaf</h3><p>A node that has no children.</p></article>
                <article class="definition"><h3>General Tree</h3><p>A tree where nodes can have any number of children.</p></article>
                <article class="definition"><h3>Binary Tree</h3><p>A tree where each node can have at most two children.</p></article>
                <article class="definition"><h3>Binary Search Tree</h3><p>A binary tree where smaller values belong to the left subtree and larger values belong to the right subtree.</p></article>
            </div>
            <div class="comparison-wrap">
                <h3>Traversal methods</h3>
                <div class="table-scroll">
                    <table>
                        <thead><tr><th scope="col">Traversal</th><th scope="col">Visiting Order</th><th scope="col">Category</th></tr></thead>
                        <tbody>
                            <tr><th scope="row">Preorder</th><td>Root → Left → Right</td><td>Depth-First (DFS)</td></tr>
                            <tr><th scope="row">Inorder</th><td>Left → Root → Right</td><td>Depth-First (DFS)</td></tr>
                            <tr><th scope="row">Postorder</th><td>Left → Right → Root</td><td>Depth-First (DFS)</td></tr>
                            <tr><th scope="row">Level-Order</th><td>Level by level, left to right</td><td>Breadth-First (BFS)</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="complexity-note"><strong>Time complexity:</strong> Traversing all nodes takes O(n) time, where n is the number of nodes.</p>
            </div>
        </section>

        <footer>Classroom demonstration · PHP calculates the traversal orders; JavaScript animates them.</footer>
    </main>
    <script id="tree-data" type="application/json"><?= $treeDataJson ?></script>
    <script id="algorithm-code-data" type="application/json"><?= $algorithmCodeJson ?></script>
</body>
</html>
