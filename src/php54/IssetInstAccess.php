<?php

namespace galapagos\php54;

class IssetInstAccess extends \PHPParser_NodeVisitorAbstract {

    public function leaveNode(\PHPParser_Node $node) {
        if ($node instanceof \PhpParser\Node\Expr\Isset_ &&
            count($node->vars[0]) == 1 &&
            $node->vars[0] instanceof \PhpParser\Node\Expr\Ternary)
        {
            $if = array($node->vars[0]->if);
            $node->vars[0]->if = new \PhpParser\Node\Expr\Isset_($if);

            $else = array($node->vars[0]->else);
            $node->vars[0]->else = new \PhpParser\Node\Expr\Isset_($else);

            return $node->vars[0];
        }
    }

}

