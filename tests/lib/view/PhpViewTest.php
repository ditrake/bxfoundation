<?php

namespace marvin255\bxfoundation\tests\lib\views;

class PhpViewTest extends \PHPUnit_Framework_TestCase
{
    protected $file = null;

    public function testConstructorEmptyFoldersException()
    {
        $this->setExpectedException('\marvin255\bxfoundation\view\Exception');
        $view = new \marvin255\bxfoundation\view\PhpView([]);
    }

    public function testConstructorWrondFolderException()
    {
        $folder = '/folder_' . mt_rand();
        $this->setExpectedException('\marvin255\bxfoundation\view\Exception', $folder);
        $view = new \marvin255\bxfoundation\view\PhpView([$folder]);
    }

    public function testCall()
    {
        $view = new \marvin255\bxfoundation\view\PhpView([
            pathinfo($this->file, PATHINFO_DIRNAME),
        ]);

        $this->assertSame(
            null,
            $view->getAbracadabra()
        );
    }

    public function testRender()
    {
        $testVariable = 'var_' . mt_rand();

        $view = new \marvin255\bxfoundation\view\PhpView([
            '/',
            pathinfo($this->file, PATHINFO_DIRNAME),
        ]);

        $this->assertSame(
            'html ' . $testVariable,
            $view->render(
                pathinfo($this->file, PATHINFO_FILENAME),
                ['testVariable' => $testVariable, 'test2' => 'test2']
            )
        );
    }

    public function testRenderWrongViewNameException()
    {
        $viewName = 'view_' . mt_rand();
        $view = new \marvin255\bxfoundation\view\PhpView([
            pathinfo($this->file, PATHINFO_DIRNAME),
        ]);
        $this->setExpectedException('\marvin255\bxfoundation\view\Exception', $viewName);
        $view->render(
            $viewName,
            ['testVariable' => 'test']
        );
    }

    public function testRenderNumericDataKeyException()
    {
        $view = new \marvin255\bxfoundation\view\PhpView([
            pathinfo($this->file, PATHINFO_DIRNAME),
        ]);
        $this->setExpectedException('\marvin255\bxfoundation\view\Exception', 2);
        $view->render(
            pathinfo($this->file, PATHINFO_FILENAME),
            ['testVariable' => 'test', 2 => 'test2']
        );
    }

    public function testGetFileExtensions()
    {
        $view = new \marvin255\bxfoundation\view\PhpView([pathinfo($this->file, PATHINFO_DIRNAME)]);

        $this->assertSame(
            ['php'],
            $view->getFileExtensions()
        );
    }

    protected function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'test') . '.php';
        file_put_contents($this->file, "html <?php echo \$testVariable;\r\n");
    }

    protected function tearDown()
    {
        unlink($this->file);
    }
}
