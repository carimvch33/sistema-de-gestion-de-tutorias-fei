<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Profesor.php';
require_once __DIR__ . '/../../../config/connection.php';

class ProfesorTest extends TestCase
{
	private $conn;
	private $profesorModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->profesorModel = new Profesor($this->conn);
	}

	public function testCreateProfesor()
	{
		$data = [
			'nombre' => 'Mario',
			'apellidoPaterno' => 'López',
			'apellidoMaterno' => 'García',
			'noPersonal' => '12345',
			'correoInstitucional' => 'mario.lopez@uv.mx',
			'rol' => 3
		];
		$result = $this->profesorModel->createProfesor($data);
		$this->assertTrue($result);
	}

	public function testGetProfesores()
	{
		$profesores = $this->profesorModel->getProfesores();
		$this->assertIsArray($profesores);
		$this->assertNotEmpty($profesores);
	}

	public function testGetProfesorById()
	{
		$profesores = $this->profesorModel->getProfesores();
		$id = $profesores[0]['idTutor'] ?? null;
		$profesor = $this->profesorModel->getProfesorById($id);
		$this->assertIsArray($profesor);
		$this->assertEquals($id, $profesor['idTutor']);
	}

	public function testUpdateProfesor()
	{
		$profesores = $this->profesorModel->getProfesores();
		$id = $profesores[0]['idTutor'] ?? null;
		$data = [
			'nombre' => 'Mario Actualizado',
			'apellidoPaterno' => 'López',
			'apellidoMaterno' => 'García',
			'noPersonal' => '54321',
			'correoInstitucional' => 'mario.actualizado@uv.mx',
			'rol' => 3
		];
		$result = $this->profesorModel->updateProfesor($id, $data);
		$this->assertTrue($result);
	}

	public function testDeleteProfesor()
	{
		$data = [
			'nombre' => 'Eliminar',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '99999',
			'correoInstitucional' => 'eliminar@uv.mx',
			'rol' => 3
		];
		$this->profesorModel->createProfesor($data);
		$profesores = $this->profesorModel->getProfesores();
		$id = null;
		foreach ($profesores as $profesor) {
			if ($profesor['correoInstitucional'] === 'eliminar@uv.mx') {
				$id = $profesor['idTutor'];
				break;
			}
		}
		$result = $this->profesorModel->deleteProfesor($id);
		$this->assertTrue($result);
	}

	public function testCreateProfesorWithDuplicateCorreo()
	{
		$data1 = [
			'nombre' => 'Duplicado',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '22222',
			'correoInstitucional' => 'duplicado@uv.mx',
			'rol' => 3
		];
		$result1 = $this->profesorModel->createProfesor($data1);
		$this->assertTrue($result1);
		$data2 = [
			'nombre' => 'Duplicado2',
			'apellidoPaterno' => 'Prueba2',
			'apellidoMaterno' => 'Test2',
			'noPersonal' => '33333',
			'correoInstitucional' => 'duplicado@uv.mx',
			'rol' => 3
		];
		$result2 = $this->profesorModel->createProfesor($data2);
		$this->assertNotTrue($result2, 'No debe permitirse crear un profesor con correo duplicado.');
	}

	public function testUpdateNonExistentProfesor()
	{
		$idInexistente = 999999;
		$data = [
			'nombre' => 'NoExiste',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '00000',
			'correoInstitucional' => 'no.existe@uv.mx',
			'rol' => 3
		];
		$result = $this->profesorModel->updateProfesor($idInexistente, $data);
		$this->assertNotTrue($result, 'No debe permitirse actualizar un profesor inexistente.');
	}

	public function testDeleteNonExistentProfesor()
	{
		$idInexistente = 999999;
		$result = $this->profesorModel->deleteProfesor($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar un profesor inexistente.');
	}

	public function testCreateProfesorWithMissingData()
	{
		$data = [
			'nombre' => 'SinCorreo',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '44444',
			// 'correoInstitucional' => '',
			'rol' => 3
		];
		$result = $this->profesorModel->createProfesor($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un profesor sin correo institucional.');
	}

	public function testCreateProfesorWithInvalidCorreo()
	{
		$data = [
			'nombre' => 'CorreoInvalido',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '55555',
			'correoInstitucional' => 'correo-invalido',
			'rol' => 3
		];
		$result = $this->profesorModel->createProfesor($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un profesor con correo inválido.');
	}

	public function testIsProfessorRegistered()
	{
		$data = [
			'nombre' => 'Registrado',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'noPersonal' => '66666',
			'correoInstitucional' => 'registrado@uv.mx',
			'rol' => 3
		];
		$this->profesorModel->createProfesor($data);
		$isRegistered = $this->profesorModel->isProfessorRegistered('registrado@uv.mx');
		$this->assertTrue($isRegistered);
		$profesores = $this->profesorModel->getProfesores();
		foreach ($profesores as $profesor) {
			if ($profesor['correoInstitucional'] === 'registrado@uv.mx') {
				$this->profesorModel->deleteProfesor($profesor['idTutor']);
			}
		}
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
