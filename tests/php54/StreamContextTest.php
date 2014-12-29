<?php

namespace galapagos\php54;

use galapagos\AbstractTestCase;

class StreamContextTest extends AbstractTestCase {
    /** @dataProvider provideTests */
    public function testTransform($name, $code, $expected) {
        $this->assertSame(
            $this->canonicalize($expected),
            $this->canonicalize(transform_stream_context($code)),
            $name
        );
    }

    public function provideTests() {
        return $this->getTests(__DIR__.'/stream_context', 'test');
    }
}


