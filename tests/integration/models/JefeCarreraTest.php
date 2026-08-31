<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/JefeCarrera.php';
require_once __DIR__ . '/../../../config/connection.php';

class JefeCarreraTest extends TestCase
{
	private $conn;
	private $jefeModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->jefeModel = new JefeCarrera($this->conn);
	}

	public function testCreateJefeCarrera()
	{
		$data = [
			'nombre' => 'Carlos',
			'apellidoPaterno' => 'Ramírez',
			'apellidoMaterno' => 'Gómez',
			'noPersonal' => '11111',
			'correoInstitucional' => 'carlos.ramirez@uv.mx',
			'rol' => 5
		];
		$result = $this->jefeModel->createJefeCarrera($data);
		$this->assertTrue($result);
	}

	public function testGetJefesCarrera()
	{
		$jefes = $this->jefeModel->getJefesCarrera();
		$this->assertIsArray($jefes);
		$this->assertNotEmpty($jefes);
	}

	public function testCreateJefeCarreraWithDuplicateCorreo()
	{
		$data1 = [
			'nombre' => 'Duplicado',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '22222',
			'correoInstitucional' => 'duplicado@uv.mx',
			'rol' => 5
		];
		$result1 = $this->jefeModel->createJefeCarrera($data1);
		$this->assertTrue($result1);
		$data2 = [
			'nombre' => 'Duplicado2',
			'apellidoPaterno' => 'Prueba2',
			'apellidoMaterno' => 'Test2',
			'noPersonal' => '33333',
			'correoInstitucional' => 'duplicado@uv.mx', // Correo duplicado
			'rol' => 5
		];
		$result2 = $this->jefeModel->createJefeCarrera($data2);
		$this->assertNotTrue($result2, 'No debe permitirse crear un jefe de carrera con correo duplicado.');
	}

	public function testCreateJefeCarreraWithMissingData()
	{
		$data = [
			'nombre' => 'SinCorreo',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '44444',
			// 'correoInstitucional' => '',
			'rol' => 5
		];
		$result = $this->jefeModel->createJefeCarrera($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un jefe de carrera sin correo institucional.');
	}

	public function testCreateJefeCarreraWithInvalidCorreo()
	{
		$data = [
			'nombre' => 'CorreoInvalido',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '55555',
			'correoInstitucional' => 'correo-invalido',
			'rol' => 5
		];
		$result = $this->jefeModel->createJefeCarrera($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un jefe de carrera con correo inválido.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
