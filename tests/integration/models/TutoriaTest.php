<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Tutoria.php';
require_once __DIR__ . '/../../../config/connection.php';

class TutoriaTest extends TestCase
{
	private $conn;
	private $tutoriaModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->tutoriaModel = new Tutoria($this->conn);
	}

	public function testCrearTutoria()
	{
		// Asume que el tutor ya existe en la base de datos
		$data = [
			'modalidad' => 'Presencial',
			'fecha' => '2025-09-12',
			'fecha_fin' => '2025-09-12',
			'lugar' => 'Aula 1',
			'notas' => 'Todo bien',
			'periodoTutoria' => 1
		];
		$result = $this->tutoriaModel->crearTutoria($data, 'aarenas@uv.mx', 'archivo.pdf');
		$this->assertTrue($result, 'Debe permitir crear una tutoría válida.');
	}

	public function testGetTutoriasByTutor()
	{
		$result = $this->tutoriaModel->getTutoriasByTutor('tutor@uv.mx');
		$this->assertInstanceOf(mysqli_result::class, $result, 'Debe retornar un resultado de tipo mysqli_result.');
	}

	public function testGetTutoriaById()
	{
		$result = $this->tutoriaModel->getTutoriaById(1);
		$this->assertIsArray($result, 'Debe retornar un arreglo con los datos de la tutoría.');
		$this->assertEquals(1, $result['periodoTutorias'], 'El periodo de la tutoría debe ser 1.');
	}

	public function testGetTutoriaByNonExistentId()
	{
		$idInexistente = 999999;
		$result = $this->tutoriaModel->getTutoriaById($idInexistente);
		$this->assertNull($result, 'Debe retornar null si el ID de la tutoría no existe.');
	}

	public function testUpdateTutoria()
	{
		$data = [
			'modalidad' => 'Virtual',
			'fecha' => '2025-09-13',
			'fecha_fin' => '2025-09-13',
			'lugar' => 'Aula 2',
			'notas' => 'Actualizado',
			'periodoTutoria' => 1
		];
		$result = $this->tutoriaModel->updateTutoria(1, 1, $data, 'nuevo.pdf');
		$this->assertTrue($result, 'Debe permitir actualizar una tutoría existente.');
	}

	public function testDeleteTutoria()
	{
		$result = $this->tutoriaModel->eliminarTutoria(1, 1);
		$this->assertTrue($result, 'Debe permitir eliminar una tutoría existente.');
	}

	public function testCrearTutoriaWithDuplicate()
	{
		$data1 = [
			'modalidad' => 'Presencial',
			'fecha' => '2025-09-12',
			'fecha_fin' => '2025-09-12',
			'lugar' => 'Aula 1',
			'notas' => 'Duplicado',
			'periodoTutoria' => 1
		];
		$result1 = $this->tutoriaModel->crearTutoria($data1, 'tutor@uv.mx', 'archivo.pdf');
		$this->assertTrue($result1, 'Debe permitir crear la primera tutoría.');
		$result2 = $this->tutoriaModel->crearTutoria($data1, 'tutor@uv.mx', 'archivo.pdf');
		$this->assertNotTrue($result2, 'No debe permitirse crear una tutoría duplicada.');
	}

	public function testUpdateNonExistentTutoria()
	{
		$idInexistente = 999999;
		$data = [
			'modalidad' => 'NoExiste',
			'fecha' => '2025-09-13',
			'fecha_fin' => '2025-09-13',
			'lugar' => 'Aula X',
			'notas' => 'No existe',
			'periodoTutoria' => 1
		];
		$result = $this->tutoriaModel->updateTutoria($idInexistente, $idInexistente, $data);
		$this->assertNotTrue($result, 'No debe permitirse actualizar una tutoría inexistente.');
	}

	public function testDeleteNonExistentTutoria()
	{
		$idInexistente = 999999;
		$result = $this->tutoriaModel->eliminarTutoria($idInexistente, $idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar una tutoría inexistente.');
	}

	public function testCrearTutoriaWithMissingData()
	{
		$data = [
			'modalidad' => '',
			'fecha' => '',
			'fecha_fin' => '',
			'lugar' => '',
			'notas' => '',
			'periodoTutoria' => 1
		];
		$result = $this->tutoriaModel->crearTutoria($data, 'tutor@uv.mx');
		$this->assertNotTrue($result, 'No debe permitirse crear una tutoría con datos faltantes.');
	}

	public function testGetTutoriasByTutorSinDatos()
	{
		$result = $this->tutoriaModel->getTutoriasByTutor('noexiste@uv.mx');
	$this->assertInstanceOf(mysqli_result::class, $result, 'Debe retornar un resultado de tipo mysqli_result aunque no existan tutorías.');
	$this->assertEquals(0, $result->num_rows, 'No debe retornar tutorías si el correo no existe.');
	}

	public function testGetCarrerasByTutorSinDatos()
	{
		$result = $this->tutoriaModel->getCarrerasByTutor(999999);
	$this->assertInstanceOf(mysqli_result::class, $result, 'Debe retornar un resultado de tipo mysqli_result aunque no existan carreras.');
	$this->assertEquals(0, $result->num_rows, 'No debe retornar carreras si el tutor no existe.');
	}

	public function testGetCorreoCreadorTutoriaInexistente()
	{
		$result = $this->tutoriaModel->getCorreoCreadorTutoria(999999);
	$this->assertNull($result, 'Debe retornar null si el ID de la tutoría no existe para el correo del creador.');
	}

	public function testGetSesionesByTutoradoSinDatos()
	{
		$result = $this->tutoriaModel->getSesionesByTutorado('noexiste@uv.mx');
	$this->assertIsArray($result, 'Debe retornar un arreglo aunque no existan sesiones.');
	$this->assertCount(0, $result, 'No debe retornar sesiones si el tutorado no existe.');
	}

	public function testGetAllTutoriasSinDatos()
	{
		$result = $this->tutoriaModel->getAllTutorias();
	$this->assertIsArray($result, 'Debe retornar un arreglo aunque no existan tutorías.');
	$this->assertCount(0, $result, 'No debe retornar tutorías si no existen.');
	}

	public function testGetTutoriasByCoordinadorSinDatos()
	{
		$result = $this->tutoriaModel->getTutoriasByCoordinador(999999);
	$this->assertIsArray($result, 'Debe retornar un arreglo aunque no existan tutorías para el coordinador.');
	$this->assertCount(0, $result, 'No debe retornar tutorías si el coordinador no existe.');
	}

	public function testGetTutoriasByCarreraTutorSinDatos()
	{
		$result = $this->tutoriaModel->getTutoriasByCarreraTutor(999999, 'noexiste@uv.mx', null);
	$this->assertIsArray($result, 'Debe retornar un arreglo aunque no existan tutorías para la carrera y tutor.');
	$this->assertCount(0, $result, 'No debe retornar tutorías si la carrera o el tutor no existen.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
