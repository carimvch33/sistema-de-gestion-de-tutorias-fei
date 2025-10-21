<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/User.php';
require_once __DIR__ . '/../../../config/connection.php';

class UserTest extends TestCase
{
	private $conn;
	private $userModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->userModel = new User($this->conn);
	}

	public function testFindUserTutor()
	{
		$result = $this->userModel->findUser('aarenas@uv.mx');
		$this->assertIsArray($result, 'Debe retornar un arreglo para un tutor existente.');
		$this->assertEquals(1, $result['rol'], 'El rol debe ser 1 para tutor.');
		$this->assertArrayHasKey('periodoActual', $result, 'Debe incluir el periodo actual.');
	}

	public function testFindUserTutorado()
	{
		$result = $this->userModel->findUser('zs19016362@estudiantes.uv.mx');
		$this->assertIsArray($result, 'Debe retornar un arreglo para un tutorado existente.');
		$this->assertEquals(2, $result['rol'], 'El rol debe ser 2 para tutorado.');
		$this->assertArrayHasKey('periodoActual', $result, 'Debe incluir el periodo actual.');
	}

	public function testFindUserAdministrador()
	{
		$result = $this->userModel->findUser('admintuto@uv.mx');
		$this->assertIsArray($result, 'Debe retornar un arreglo para un administrador existente.');
		$this->assertEquals(3, $result['rol'], 'El rol debe ser 3 para administrador.');
		$this->assertArrayHasKey('periodoActual', $result, 'Debe incluir el periodo actual.');
	}

	public function testFindUserCoordinador()
	{
		$result = $this->userModel->findUser('jocharan@uv.mx');
		$this->assertIsArray($result, 'Debe retornar un arreglo para un coordinador existente.');
		$this->assertEquals(4, $result['rol'], 'El rol debe ser 4 para coordinador.');
		$this->assertArrayHasKey('periodoActual', $result, 'Debe incluir el periodo actual.');
	}

	public function testFindUserByMatriculaSinDominio()
	{
		$result = $this->userModel->findUser('zs19016362');
		$this->assertIsArray($result, 'Debe encontrar usuario por matrícula sin dominio.');
	}

	public function testFindUserInexistente()
	{
		$result = $this->userModel->findUser('noexiste@uv.mx');
		$this->assertNull($result, 'Debe retornar null si el usuario no existe.');
	}

	public function testFindUserConRolInvalido()
	{
		$this->conn->query("INSERT INTO sesion (correoInstitucional, rol) VALUES ('rolinvalido@uv.mx', 99)");
		$result = $this->userModel->findUser('rolinvalido@uv.mx');
		$this->assertNull($result, 'Debe retornar null si el rol no es válido.');
		$this->conn->query("DELETE FROM sesion WHERE correoInstitucional = 'rolinvalido@uv.mx'");
	}

	public function testFindUserSinPeriodoActual()
	{
        //Modificar estado
		$this->conn->query("UPDATE periodo SET actual = 0");
		$result = $this->userModel->findUser('aarenas@uv.mx');
		$this->assertEquals('Período no definido', $result['periodoActual'], 'Debe indicar que no hay período actual.');
		$this->conn->query("UPDATE periodo SET actual = 1 LIMIT 1"); // Restaurar estado
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
