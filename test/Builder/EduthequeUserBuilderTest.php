<?php

namespace FTVEN\Education\SSOUserBundle\Test\Builder;

use FTVEN\Education\SSOUserBundle\Builder\EduthequeUserBuilder;
use FTVEN\Education\SSOUserBundle\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * Class EduthequeUserBuilderTest
 *
 * @package FTVEN\Education\SSOUserBundle\Test\Builder
 */
class EduthequeUserBuilderTest extends TestCase
{
    /** @test */
    public function itMustRenderATeacherUser()
    {
        $xml = '<cas:serviceResponse xmlns:cas=\'http://www.yale.edu/tp/cas\'>
    <cas:authenticationSuccess>
	<cas:user>alexandre.lucas</cas:user>
	<cas:nom>Lucas</cas:nom>
	<cas:prenom>Alexandre</cas:prenom>
	<cas:email>alexandre.lucas@cndp.fr</cas:email>
	<cas:fonction>1</cas:fonction>
	<cas:idUser>11</cas:idUser>    
    </cas:authenticationSuccess>
</cas:serviceResponse>';


        $document = new \DOMDocument("1.0");
        $document->loadXML($xml);


        $builder = new EduthequeUserBuilder();
        /** @var User $user */
        $user = $builder->buildUser($document, 'ticket');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('ticket', $user->getUsername());
        $this->assertEquals('ticket', $user->getApiKey());
        $this->assertEquals('eduteque', $user->getConnector());
        $this->assertEquals([], $user->getStages());
        $this->assertEquals([], $user->getCourses());
        $this->assertEquals('Alexandre', $user->getFirstName());
        $this->assertEquals('Lucas', $user->getLastName());
        $this->assertEquals('alexandre.lucas@cndp.fr', $user->getEmail());
        $this->assertEquals(['ROLE_USER', 'ROLE_TEACHER'], $user->getRoles());

    }
    /** @test */
    public function itMustRenderAClassUser()
    {
        $xml = "<cas:serviceResponse xmlns:cas='http://www.yale.edu/tp/cas'>
<cas:authenticationSuccess>
<cas:user>elevesDeBruno</cas:user>
<cas:nom></cas:nom>
<cas:prenom></cas:prenom>
<cas:email></cas:email>
<cas:fonction>2</cas:fonction>
<cas:idUser>14</cas:idUser>
</cas:authenticationSuccess>
</cas:serviceResponse>";


        $document = new \DOMDocument("1.0");
        $document->loadXML($xml);


        $builder = new EduthequeUserBuilder();
        /** @var User $user */
        $user = $builder->buildUser($document, 'ticket');
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('ticket', $user->getUsername());
        $this->assertEquals('ticket', $user->getApiKey());
        $this->assertEquals('eduteque', $user->getConnector());
        $this->assertEquals([], $user->getStages());
        $this->assertEquals([], $user->getCourses());
        $this->assertEquals('elevesDeBruno', $user->getFirstName());
        $this->assertNull($user->getLastName());
        $this->assertNull($user->getEmail());
        $this->assertEquals(['ROLE_USER', 'ROLE_STUDENT'], $user->getRoles());

    }
}
