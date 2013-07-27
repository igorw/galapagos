<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class CallableTypehintTest extends AbstractTestCase
{
    /** @dataProvider provideTests */
    public function test_transform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_callable_typehint($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/callable_typehint', 'test');
    }
}
