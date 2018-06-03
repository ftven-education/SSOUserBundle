<?php

namespace FTVEN\Education\SSOUserBundle\Test\Validator;

use FTVEN\Education\SSOUserBundle\Validator\TokenValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Class TokenValidatorTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Validator
 */
class TokenValidatorTest extends TestCase
{
    /**
     * @var LoggerInterface | MockObject
     */
    protected $logger;

    public function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /** @test */
    public function itMustHaveInvalidDataBecauseTheServiceWasNotFound()
    {
        $this->logger->expects($this->never())->method('emergency');
        $this->logger->expects($this->never())->method('alert');
        $this->logger->expects($this->exactly(1))->method('critical');
        $this->logger->expects($this->exactly(1))->method('error');
        $this->logger->expects($this->never())->method('warning');
        $this->logger->expects($this->never())->method('notice');
        $this->logger->expects($this->never())->method('info');
        $this->logger->expects($this->exactly(1))->method('debug');

        $data = "<root></root>";
        $validator = new TokenValidator($this->logger);
        $validator->handledData($data);
        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function itMustHaveInvalidDataBecauseTheUserIsNotAuthenticate()
    {
        $this->logger->expects($this->never())->method('emergency');
        $this->logger->expects($this->never())->method('alert');
        $this->logger->expects($this->exactly(1))->method('critical');
        $this->logger->expects($this->exactly(1))->method('error');
        $this->logger->expects($this->never())->method('warning');
        $this->logger->expects($this->never())->method('notice');
        $this->logger->expects($this->never())->method('info');
        $this->logger->expects($this->exactly(1))->method('debug');

        $data = "<cas:serviceResponse xmlns:cas='http://www.yale.edu/tp/cas'><cas:badSuccess>Error</cas:badSuccess></cas:serviceResponse>";
        $validator = new TokenValidator($this->logger);
        $validator->handledData($data);
        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function itMustNotThrowAnyException()
    {
        $this->logger->expects($this->never())->method('emergency');
        $this->logger->expects($this->never())->method('alert');
        $this->logger->expects($this->never())->method('critical');
        $this->logger->expects($this->never())->method('error');
        $this->logger->expects($this->never())->method('warning');
        $this->logger->expects($this->never())->method('notice');
        $this->logger->expects($this->never())->method('info');
        $this->logger->expects($this->exactly(1))->method('debug');

        $data = '<cas:serviceResponse xmlns:cas=\'http://www.yale.edu/tp/cas\'>
    <cas:authenticationSuccess>
	<cas:user>alexandre.lucas</cas:user>
	<cas:nom>Lucas</cas:nom>
	<cas:prenom>Alexandre</cas:prenom>
	<cas:email>alexandre.lucas@cndp.fr</cas:email>
	<cas:fonction>1</cas:fonction>
	<cas:idUser>11</cas:idUser>    
    </cas:authenticationSuccess>
</cas:serviceResponse>';
        $validator = new TokenValidator($this->logger);
        $validator->handledData($data);
        $this->assertTrue($validator->isValid());
        $this->assertInstanceOf(\DOMDocument::class, $validator->getData());
    }
}