<?php

namespace galapagos\php55;

function transform_code($code, callable $transform) {
    $parser = new \PHPParser_Parser(new \PHPParser_Lexer);
    $prettyPrinter = new \PHPParser_PrettyPrinter_Default;

    $ast = $parser->parse($code);
    $ast = $transform($ast);
    return '<?php'."\n\n".$prettyPrinter->prettyPrint($ast);
}

function transform_with_visitors($code, array $visitors) {
    return transform_code($code, function ($ast) use ($code, $visitors) {
        $traverser = new \PHPParser_NodeTraverser;
        foreach ($visitors as $visitor) {
            $traverser->addVisitor($visitor);
        }
        return $traverser->traverse($ast);
    });
}

function transform_short_array($code) {
    return transform_with_visitors($code, []);
}

function transform_closure_this($code) {
    return transform_with_visitors($code, [
        new NodeVisitor_ClosureThis,
    ]);
}

class NodeVisitor_ClosureThis extends \PHPParser_NodeVisitorAbstract
{
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
            $has_that = $this->closure->getAttribute('has_that');
            $this->closure = null;

            if ($has_that) {
                $node = clone $node;
                $node->uses = array_merge($node->uses, [
                    new \PHPParser_Node_Expr_Variable('that'),
                ]);
                return $node;
            }
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $has_that = $this->method->getAttribute('has_that');
            $this->method = null;
            $this->closure = null;

            if ($has_that) {
                $node = clone $node;
                $node->stmts = array_merge([
                    new \PHPParser_Node_Expr_Assign(
                        new \PHPParser_Node_Expr_Variable('that'),
                        new \PHPParser_Node_Expr_Variable('this')
                    ),
                ], $node->stmts);
                return $node;
            }
        }
    }
}
