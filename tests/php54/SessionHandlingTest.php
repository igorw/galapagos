<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class SessionHandlingTest extends AbstractTestCase {
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_session_handling($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/session_handling', 'test');
    }
}

