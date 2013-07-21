<?php

namespace galapagos\php55;

const FILE_HEAD = "<?php\n\n";

class short_array_test extends \PHPUnit_Framework_TestCase {
    function test_short_array_empty() {
        $this->assertSame(FILE_HEAD.'return array();', transform_short_array(FILE_HEAD.'return [];'));
    }

    function test_short_array_list() {
        $this->assertSame(FILE_HEAD.'return array(1, 2, 3);', transform_short_array(FILE_HEAD.'return [1, 2, 3];'));
    }

    function test_short_array_map() {
        $this->assertSame(
            FILE_HEAD."return array('foo' => 'bar', 'baz' => 'qux');",
            transform_short_array(FILE_HEAD."return ['foo' => 'bar', 'baz' => 'qux'];")
        );
    }

    function test_short_array_nested() {
        $this->assertSame(
            FILE_HEAD."return array('foo' => array('+', 1, 2, array('+', 0, 3)));",
            transform_short_array(FILE_HEAD."return ['foo' => ['+', 1, 2, ['+', 0, 3]]];")
        );
    }
}
