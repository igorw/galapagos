<?php

namespace galapagos\php54;

class ObjectInstAccess extends \PHPParser_NodeVisitorAbstract {
    public function enterNode(\PHPParser_Node $node) {
        if (($node instanceof \PHPParser_Node_Expr_MethodCall || $node instanceof \PHPParser_Node_Expr_PropertyFetch)
            && $node->var instanceof \PHPParser_Node_Expr_New) {

            $tmp = new \PHPParser_Node_Expr_Variable('tmp');
            $assignment = new \PHPParser_Node_Expr_Assign($tmp, $node->var);
            $node->var = $tmp;

            return new \PHPParser_Node_Expr_Ternary(
                $assignment,
                $node,
                $node
            );
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if (($node instanceof \PHPParser_Node_Expr_MethodCall || $node instanceof \PHPParser_Node_Expr_PropertyFetch)
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