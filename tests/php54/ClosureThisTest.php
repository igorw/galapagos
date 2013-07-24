<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class ClosureThisTest extends AbstractTestCase {
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_closure_this($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/closure_this', 'test');
    }
}
