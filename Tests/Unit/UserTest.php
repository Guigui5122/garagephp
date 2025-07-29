<?php

namespace Tests\Unit;

use App\Models\User;
use InvalidArgumentException;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;


/**
 * @covers \App\Models\User
 */

class UserTest extends TestCase{

    public function testSetUsernameWithValidUsernameSetsProperty():void{

        // 1- préparation
        $user = new User();
        $username = 'testuser';

        // 2- action
        $user->setUsername($username);

        // 3- assertion (=vérifications)
        $this->assertEquals($username, $user->getUsername());

     
    }

    public function testSetEmailWithInvalidEmailThrowsException(): void {


        // On s'attend à ce qu'une exception soit lancée
        $this->expectException(InvalidArgumentException::class);

        // 1- préparation
        $user = new User();

        // 2- action
        $user->setEmail('email-invalide');
    }

    public function testSaveWhenCreatingNewUserReturnsTrue(): void{


        // 1- préparation: créer des mocks pour PDO et PDOStatement
        // On simule le comportement de la BDD sans s'y connecter

        $pdoStatementMock = $this->createMock(PDOStatement::class);
        $pdoStatementMock->expects($this->once()) // On s'attend à ce que execute() soit appelée une fois
                         ->method('execute')
                         ->willReturn(true); // et qu'elle retourne true  

        $pdoMock = $this->createMock(PDO::class);
        $pdoMock->expects($this->once())
                ->method('prepare')
                ->willReturn($pdoStatementMock); // prepare()  doit retourner notre mock de statement

        $pdoMock->expects($this->once())
                ->method('lastInsertId')
                ->willReturn('1'); // lastInsertId() doit retourner un ID

        // 2- action : créer un user et on va appeler la méthode save()
        // on injecte notre fausse BDD dans le constructeur (mais cela necessite une petite adaptation de notre modele)

        $user = new User($pdoMock);
        $user->setUsername('John.Doe')
             ->setEmail('john@doe.com')
             ->setPassword('Password1234');

        $result = $user->save();

        // 3- assertion
        $this->assertTrue($result);
        $this->assertEquals(1, $user->getId());
    }
}
