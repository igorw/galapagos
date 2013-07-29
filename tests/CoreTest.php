<?php

namespace galapagos;

const FILE_HEAD = "<?php\n\n";

class CoreTest extends \PHPUnit_Framework_TestCase
{
    function testTransform() {
        $this->assertSame(FILE_HEAD.'foo($bar);', transform(FILE_HEAD.'foo($bar);'));
    }
}
