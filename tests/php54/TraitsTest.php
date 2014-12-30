<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class TraitsTest extends AbstractTestCase {
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_traits($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/traits', 'test');
    }
}
