<?php

namespace galapagos\php54;

class ArrayDeref extends \PHPParser_NodeVisitorAbstract {
    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Expr_ArrayDimFetch
            && $node->var instanceof \PHPParser_Node_Expr_FuncCall) {

            $tmp = new \PHPParser_Node_Expr_Variable('tmp');

            return new \PHPParser_Node_Expr_Ternary(
                new \PHPParser_Node_Expr_Assign($tmp, $node->var),
                new \PHPParser_Node_Expr_ArrayDimFetch($tmp, $node->dim),
                new \PHPParser_Node_Expr_ArrayDimFetch($tmp, $node->dim)
            );
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if (($node instanceof \PHPParser_Node_Expr_ArrayDimFetch)
            && $node->var instanceof \PHPParser_Node_Expr_Ternary) {
            $subject = clone $node;
            $subject->var = $node->var->if;

            return new \PHPParser_Node_Expr_Ternary(
                $node->var->cond,
                $subject,
                $subject
            );
        }
    }
}
