<?php

namespace galapagos\php54;

class StreamContext extends \PHPParser_NodeVisitorAbstract {

    public function enterNode(\PHPParser_Node $node) {

        if ($node instanceof \PhpParser\Node\Expr\FuncCall &&
            $node->name == "stream_context_create" &&
            count($node->args) == 2)
        {
            array_pop($node->args);
            return $node;
        }
    }

}



