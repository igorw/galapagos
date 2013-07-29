<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class ArrayDerefTest extends AbstractTestCase
{
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_array_deref($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/array_deref', 'test');
    }
}
