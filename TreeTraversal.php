<?php
require_once __DIR__ . '/TreeNode.php';

/** Visit root, then the left subtree, then the right subtree. */
function preorder(?TreeNode $node): array
{
    if ($node === null) {
        return [];
    }

    return array_merge([$node->value], preorder($node->left), preorder($node->right));
}

/** Visit the left subtree, then root, then the right subtree. */
function inorder(?TreeNode $node): array
{
    if ($node === null) {
        return [];
    }

    return array_merge(inorder($node->left), [$node->value], inorder($node->right));
}

/** Visit the left subtree, then the right subtree, then root. */
function postorder(?TreeNode $node): array
{
    if ($node === null) {
        return [];
    }

    return array_merge(postorder($node->left), postorder($node->right), [$node->value]);
}

/** Visit nodes level by level using a first-in, first-out queue. */
function levelOrder(?TreeNode $root): array
{
    if ($root === null) {
        return [];
    }

    $result = [];
    $queue = [$root];
    $head = 0;

    while ($head < count($queue)) {
        $node = $queue[$head++];
        $result[] = $node->value;

        if ($node->left !== null) {
            $queue[] = $node->left;
        }
        if ($node->right !== null) {
            $queue[] = $node->right;
        }
    }

    return $result;
}
