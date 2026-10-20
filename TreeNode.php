<?php
/** A node in a binary tree. */
class TreeNode
{
    public string $value;
    public ?TreeNode $left;
    public ?TreeNode $right;

    public function __construct(string $value, ?TreeNode $left = null, ?TreeNode $right = null)
    {
        $this->value = $value;
        $this->left = $left;
        $this->right = $right;
    }
}
