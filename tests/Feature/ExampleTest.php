<?php

use PHPUnit\Framework\TestCase;
use phpcommon\Utils\File;

class ExampleTest extends TestCase {
    /**
     * @test
     */
    public function exampleTest() {
        $this->assertEquals(File::download('myuuid'), '/file/myuuid/download');
    }
}
