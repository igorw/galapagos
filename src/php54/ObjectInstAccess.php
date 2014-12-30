<?php

namespace galapagos\php54;

class ObjectInstAccess extends \PHPParser_NodeVisitorAbstract {
    public function enterNode(\PHPParser_Node $node) {
        if ((($node instanceof \PhpParser\Node\Expr\MethodCall
                || $node instanceof \PhpParser\Node\Expr\PropertyFetch)
                && $node->var instanceof \PHPParser_Node_Expr_New)
            || ($node instanceof \PhpParser\Node\Expr\PropertyFetch &&
            $node->var instanceof \PhpParser\Node\Expr\FuncCall)
        ) {
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
        if (($node instanceof \PHPParser_Node_Expr_MethodCall
                || $node instanceof \PhpParser\Node\Expr\PropertyFetch)
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
