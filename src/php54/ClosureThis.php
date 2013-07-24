<?php

namespace galapagos\php54;

class ClosureThis extends \PHPParser_NodeVisitorAbstract {
    private $method = null;
    private $closure = null;

    public function enterNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->method = $node;
        }

        if ($this->method && $node instanceof \PHPParser_Node_Expr_Closure) {
            $this->closure = $node;
        }

        if ($this->closure
            && $node instanceof \PHPParser_Node_Expr_Variable
            && 'this' === $node->name) {

            $this->method->setAttribute('has_that', true);
            $this->closure->setAttribute('has_that', true);
            return new \PHPParser_Node_Expr_Variable('that');
        }
    }

    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Expr_Closure) {
            if ($this->closure->getAttribute('has_that')) {
                $node->uses = array_merge($node->uses, [
                    new \PHPParser_Node_Expr_Variable('that'),
                ]);
            }

            $this->closure = null;
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            if ($this->method->getAttribute('has_that')) {
                $node->stmts = array_merge([
                    new \PHPParser_Node_Expr_Assign(
                        new \PHPParser_Node_Expr_Variable('that'),
                        new \PHPParser_Node_Expr_Variable('this')
                    ),
                ], $node->stmts);
            }

            $this->method = null;
            $this->closure = null;
        }
    }
}