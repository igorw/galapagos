<?php

namespace galapagos\php54;

class ArrayDeref extends \PHPParser_NodeVisitorAbstract {
    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Expr_ArrayDimFetch
            && $node->var instanceof \PHPParser_Node_Expr_FuncCall) {

            $tmp = new \PHPParser_Node_Expr_Variable('tmp');
            $null = new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name('null'));

            return new \PHPParser_Node_Expr_Ternary(
                new \PHPParser_Node_Expr_Assign($tmp, $node->var),
                new \PHPParser_Node_Expr_ArrayDimFetch($tmp, $node->dim),
                $null
            );
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if (($node instanceof \PHPParser_Node_Expr_ArrayDimFetch)
            && $node->var instanceof \PHPParser_Node_Expr_Ternary) {
            $subject = clone $node;
            $subject->var = $node->var->if;
            $node = $node->var;
            $node->if = $subject;
            return $node;
        }
    }
}
