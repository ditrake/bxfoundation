<?php

namespace creative\foundation\tests\lib\views;

class PhpViewTest extends \PHPUnit_Framework_TestCase
{
    protected $file = null;

    public function testRender()
    {
        $testVariable = 'var_' . mt_rand();
        $view = new \creative\foundation\view\PhpView;

        $viewName = pathinfo($this->file, PATHINFO_DIRNAME) . '/' . pathinfo($this->file, PATHINFO_FILENAME);

        $this->assertSame(
            $testVariable,
            $view->render(
                $viewName,
                ['testVariable' => $testVariable, 'test2' => 'test2']
            )
        );
    }

    public function testRenderWrongViewNameException()
    {
        $viewName = pathinfo($this->file, PATHINFO_DIRNAME) . '/../' . pathinfo($this->file, PATHINFO_FILENAME);
        $view = new \creative\foundation\view\PhpView;
        $this->setExpectedException('\creative\foundation\view\Exception', $viewName);
        $view->render($viewName);
    }

    public function testRenderInvalidViewFileException()
    {
        $viewName = pathinfo($this->file, PATHINFO_DIRNAME) . '/test';
        $view = new \creative\foundation\view\PhpView;
        $this->setExpectedException('\creative\foundation\view\Exception', $viewName);
        $view->render($viewName);
    }

    public function testRenderNumericDataKeyException()
    {
        $viewName = pathinfo($this->file, PATHINFO_DIRNAME) . '/' . pathinfo($this->file, PATHINFO_FILENAME);
        $view = new \creative\foundation\view\PhpView;
        $this->setExpectedException('\creative\foundation\view\Exception', 2);
        $view->render($viewName, ['testVariable' => 'test', 2 => 'test2']);
    }

    public function testGetFileExtensions()
    {
        $view = new \creative\foundation\view\PhpView;

        $this->assertSame(
            ['php'],
            $view->getFileExtensions()
        );
    }

    protected function setUp()
    {
        $this->file = tempnam(sys_get_temp_dir(), 'test') . '.php';
        file_put_contents($this->file, "<?php\r\necho \$testVariable;\r\n");
    }

    protected function tearDown()
    {
        unlink($this->file);
    }
}
