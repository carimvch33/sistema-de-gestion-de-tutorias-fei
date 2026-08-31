<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Estudiante.php';
require_once __DIR__ . '/../../../config/connection.php';

class EstudianteTest extends TestCase
{
	private $conn;
	private $estudianteModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->estudianteModel = new Estudiante($this->conn);
	}

	public function testCreateEstudiante()
	{
		$data = [
			'nombre' => 'Ana',
			'apellidoPaterno' => 'López',
			'apellidoMaterno' => 'García',
			'matricula' => 'A12345678',
			'carrera' => 1,
			'correoInstitucional' => 'ana.lopez@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$result = $this->estudianteModel->createEstudiante($data);
		$this->assertTrue($result);
	}

	public function testGetEstudiantes()
	{
		$estudiantes = $this->estudianteModel->getEstudiantes();
		$this->assertIsArray($estudiantes);
		$this->assertNotEmpty($estudiantes);
	}

	public function testGetEstudianteById()
	{
		$estudiantes = $this->estudianteModel->getEstudiantes();
		$id = $estudiantes[0]['idTutorado'] ?? null;
		$estudiante = $this->estudianteModel->getEstudianteById($id);
		$this->assertIsArray($estudiante);
		$this->assertEquals($id, $estudiante['idTutorado']);
	}

	public function testGetEstudianteByNonExistentId()
	{
		$idInexistente = 999999;
		$estudiante = $this->estudianteModel->getEstudianteById($idInexistente);
		$this->assertNull($estudiante, 'Debe retornar null si el ID no existe.');
	}

	public function testUpdateEstudiante()
	{
		$estudiantes = $this->estudianteModel->getEstudiantes();
		$id = $estudiantes[0]['idTutorado'] ?? null;
		$data = [
			'nombre' => 'Ana Actualizada',
			'apellidoPaterno' => 'López',
			'apellidoMaterno' => 'García',
			'matricula' => 'A87654321',
			'carrera' => 1,
			'correoInstitucional' => 'ana.actualizada@uv.mx',
			'tutor' => 1
		];
		$result = $this->estudianteModel->updateEstudiante($id, $data);
		$this->assertTrue($result);
	}

	public function testDeleteEstudiante()
	{
		$data = [
			'nombre' => 'Eliminar',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'E12345678',
			'carrera' => 1,
			'correoInstitucional' => 'eliminar@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$this->estudianteModel->createEstudiante($data);
		$estudiantes = $this->estudianteModel->getEstudiantes();
		$id = null;
		foreach ($estudiantes as $estudiante) {
			if ($estudiante['correoInstitucional'] === 'eliminar@uv.mx') {
				$id = $estudiante['idTutorado'];
				break;
			}
		}
		$result = $this->estudianteModel->deleteEstudiante($id);
		$this->assertTrue($result);
	}

	public function testCreateEstudianteWithDuplicateMatricula()
	{
		$data1 = [
			'nombre' => 'Duplicado',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'D12345678',
			'carrera' => 1,
			'correoInstitucional' => 'duplicado@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$result1 = $this->estudianteModel->createEstudiante($data1);
		$this->assertTrue($result1);
		$data2 = [
			'nombre' => 'Duplicado2',
			'apellidoPaterno' => 'Prueba2',
			'apellidoMaterno' => 'Test2',
			'matricula' => 'D12345678',
			'carrera' => 1,
			'correoInstitucional' => 'duplicado2@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$result2 = $this->estudianteModel->createEstudiante($data2);
		$this->assertNotTrue($result2, 'No debe permitirse crear un estudiante con matrícula duplicada.');
		
        $estudiantes = $this->estudianteModel->getEstudiantes();
		foreach ($estudiantes as $estudiante) {
			if ($estudiante['matricula'] === 'D12345678') {
				$this->estudianteModel->deleteEstudiante($estudiante['idTutorado']);
			}
		}
	}

	public function testUpdateNonExistentEstudiante()
	{
		$idInexistente = 999999;
		$data = [
			'nombre' => 'NoExiste',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'N00000000',
			'carrera' => 1,
			'correoInstitucional' => 'no.existe@uv.mx',
			'tutor' => 1
		];
		$result = $this->estudianteModel->updateEstudiante($idInexistente, $data);
		$this->assertNotTrue($result, 'No debe permitirse actualizar un estudiante inexistente.');
	}

	public function testDeleteNonExistentEstudiante()
	{
		$idInexistente = 999999;
		$result = $this->estudianteModel->deleteEstudiante($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar un estudiante inexistente.');
	}

	public function testIsStudentRegistered()
	{
		$data = [
			'nombre' => 'Registrado',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'R12345678',
			'carrera' => 1,
			'correoInstitucional' => 'registrado@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$this->estudianteModel->createEstudiante($data);
		$isRegistered = $this->estudianteModel->isStudentRegistered('R12345678');
		$this->assertTrue($isRegistered);
		$estudiantes = $this->estudianteModel->getEstudiantes();
		foreach ($estudiantes as $estudiante) {
			if ($estudiante['matricula'] === 'R12345678') {
				$this->estudianteModel->deleteEstudiante($estudiante['idTutorado']);
			}
		}
	}

	public function testCreateEstudianteWithMissingData()
	{
		$data = [
			'nombre' => 'SinMatricula',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			// 'matricula' => '',
			'carrera' => 1,
			'correoInstitucional' => 'sinmatricula@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$result = $this->estudianteModel->createEstudiante($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un estudiante sin matrícula.');
	}

	public function testCreateEstudianteWithInvalidCorreo()
	{
		$data = [
			'nombre' => 'CorreoInvalido',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'C12345678',
			'carrera' => 1,
			'correoInstitucional' => 'correo-invalido', 
			'rol' => 2,
			'tutor' => 1
		];
		$result = $this->estudianteModel->createEstudiante($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un estudiante con correo inválido.');
	}

	public function testCreateEstudianteWithNonExistentCarrera()
	{
		$data = [
			'nombre' => 'CarreraInexistente',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'X12345678',
			'carrera' => 999999,
			'correoInstitucional' => 'carrera.inexistente@uv.mx',
			'rol' => 2,
			'tutor' => 1
		];
		$result = $this->estudianteModel->createEstudiante($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un estudiante con carrera inexistente.');
	}

	public function testCreateEstudianteWithNonExistentTutor()
	{
		$data = [
			'nombre' => 'TutorInexistente',
			'apellidoPaterno' => 'Prueba',
			'apellidoMaterno' => 'Test',
			'matricula' => 'T12345678',
			'carrera' => 1,
			'correoInstitucional' => 'tutor.inexistente@uv.mx',
			'rol' => 2,
			'tutor' => 999999
		];
		$result = $this->estudianteModel->createEstudiante($data);
		$this->assertNotTrue($result, 'No debe permitirse crear un estudiante con tutor inexistente.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
