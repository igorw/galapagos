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
                new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name('null'))
            );
        }
    }
    
    public function leaveNode(\PHPParser_Node $node) {
        if (($node instanceof \PHPParser_Node_Expr_MethodCall || $node instanceof \PHPParser_Node_Expr_PropertyFetch)
            && $node->var instanceof \PHPParser_Node_Expr_Ternary) {
            $subject = clone $node;
            $subject->var = $node->var->if;
            $node = $node->var;
            $node->if = $subject;
            return $node;
        }
    }
}