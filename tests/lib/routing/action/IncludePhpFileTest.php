<?php

namespace marvin255\bxfoundation\tests\lib\routing\action;

use marvin255\bxfoundation\tests\BaseCase;
use marvin255\bxfoundation\Exception;
use marvin255\bxfoundation\routing\action\IncludePhpFile;
use marvin255\bxfoundation\request\Bitrix as Request;
use marvin255\bxfoundation\response\Bitrix as Response;
use marvin255\bxfoundation\routing\rule\RuleResultInterface;

class IncludePhpFileTest extends BaseCase
{
    /**
     * @var string
     */
    protected $filepath;

    /**
     * @test
     */
    public function testUnexistedFileException()
    {
        $file = '/tests_test_' . mt_rand();

        $this->setExpectedException(Exception::class, $file);
        new IncludePhpFile($file);
    }

    /**
     * @test
     */
    public function testWrongParameterNameException()
    {
        $this->setExpectedException(Exception::class, '0');
        new IncludePhpFile($this->filepath, ['0' => 'test']);
    }

    /**
     * @test
     */
    public function testRun()
    {
        $testItem = 'test_item_' . mt_rand();

        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $response = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();
        $ruleResult = $this->getMockBuilder(RuleResultInterface::class)
            ->getMock();

        $action = new IncludePhpFile($this->filepath, [
            'testItem' => $testItem,
        ]);
        $res = $action->run($ruleResult, $request, $response);

        $this->assertSame("{$testItem} static", $res);
    }

    /**
     * Подготавливает файл для отображения.
     */
    public function setUp()
    {
        $this->filepath = sys_get_temp_dir() . '/template_' . mt_rand() . '.php';
        file_put_contents($this->filepath, '<?php echo $testItem; ?> static');
        parent::setUp();
    }

    /**
     * Удаляет файл для отображения.
     */
    public function tearDown()
    {
        if (file_exists($this->filepath)) {
            unlink($this->filepath);
        }
        parent::tearDown();
    }
}
