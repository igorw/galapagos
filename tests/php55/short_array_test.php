<?php

namespace galapagos\php55;

class short_array_test extends \PHPUnit_Framework_TestCase {
    function test_short_array_empty() {
        $this->assertSame('<?php return array();', transform_short_array('<?php return [];'));
    }

    function test_short_array_list() {
        $this->assertSame('<?php return array(1, 2, 3);', transform_short_array('<?php return [1, 2, 3];'));
    }

    function test_short_array_map() {
        $this->assertSame(
            "<?php return array('foo' => 'bar', 'baz' => 'qux');",
            transform_short_array("<?php return ['foo' => 'bar', 'baz' => 'qux'];")
        );
    }

    function test_short_array_nested() {
        $this->assertSame(
            "<?php return array('foo' => array('+', 1, 2, array('+', 0, 3)));",
            transform_short_array("<?php return ['foo' => ['+', 1, 2, ['+', 0, 3]]];")
        );
    }
}
