<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Seccion.php';
require_once __DIR__ . '/../../../config/connection.php';

class SeccionTest extends TestCase
{
	private $conn;
	private $seccionModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->seccionModel = new Seccion($this->conn);
	}

	public function testCreateSeccion()
	{
		$idProfesor = 1;
		$idExperienciaEducativa = 1;
		$idPeriodo = 1;
		$nrc = '12345';
		$result = $this->seccionModel->createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$this->assertTrue($result);
	}

	public function testGetSecciones()
	{
		$secciones = $this->seccionModel->getSecciones();
		$this->assertIsArray($secciones);
		$this->assertNotEmpty($secciones);
	}

	public function testGetSeccionById()
	{
		$secciones = $this->seccionModel->getSecciones();
		$id = $secciones[0]['id'] ?? null;
		$seccion = $this->seccionModel->getSeccionById($id);
		$this->assertIsArray($seccion);
		$this->assertEquals($id, $seccion['id']);
	}

	public function testUpdateSeccion()
	{
		$secciones = $this->seccionModel->getSecciones();
		$id = $secciones[0]['id'] ?? null;
		$idProfesor = 1;
		$idExperienciaEducativa = 1;
		$idPeriodo = 1;
		$nrc = '54321';
		$result = $this->seccionModel->updateSeccion($id, $idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$this->assertTrue($result);
	}

	public function testDeleteSeccion()
	{
		$idProfesor = 1;
		$idExperienciaEducativa = 1;
		$idPeriodo = 1;
		$nrc = '99999';
		$this->seccionModel->createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$secciones = $this->seccionModel->getSecciones();
		$id = null;
		foreach ($secciones as $s) {
			if ($s['nrc'] === $nrc) {
				$id = $s['id'];
				break;
			}
		}
		$result = $this->seccionModel->deleteSeccion($id);
		$this->assertTrue($result);
	}

	public function testCreateSeccionWithDuplicateNrc()
	{
		$idProfesor = 1;
		$idExperienciaEducativa = 1;
		$idPeriodo = 1;
		$nrc = 'DUPLICADO';
		$result1 = $this->seccionModel->createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$this->assertTrue($result1);
		$result2 = $this->seccionModel->createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$this->assertNotTrue($result2, 'No debe permitirse crear una sección con NRC duplicado.');
	}

	public function testUpdateNonExistentSeccion()
	{
		$idInexistente = 999999;
		$idProfesor = 1;
		$idExperienciaEducativa = 1;
		$idPeriodo = 1;
		$nrc = 'INEXISTENTE';
		$result = $this->seccionModel->updateSeccion($idInexistente, $idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$this->assertNotTrue($result, 'No debe permitirse actualizar una sección inexistente.');
	}

	public function testDeleteNonExistentSeccion()
	{
		$idInexistente = 999999;
		$result = $this->seccionModel->deleteSeccion($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar una sección inexistente.');
	}

	public function testCreateSeccionWithMissingData()
	{
		$idProfesor = 1;
		$idExperienciaEducativa = 1;
		$idPeriodo = 1;
		$nrc = '';
		$result = $this->seccionModel->createSeccion($idProfesor, $idExperienciaEducativa, $idPeriodo, $nrc);
		$this->assertNotTrue($result, 'No debe permitirse crear una sección sin NRC.');
	}

	public function testGetProfesoresPorExperienciaWithNonExistentExperiencia()
	{
		$idExperiencia = 999999;
		$idCarrera = 1;
		$profesores = $this->seccionModel->getProfesoresPorExperiencia($idExperiencia, $idCarrera);
		$this->assertIsArray($profesores);
		$this->assertEmpty($profesores, 'No debe retornar profesores para una experiencia inexistente.');
	}

	public function testGetExperienciasPorProfesorWithNonExistentProfesor()
	{
		$idProfesor = 999999;
		$idCarrera = 1;
		$experiencias = $this->seccionModel->getExperienciasPorProfesor($idProfesor, $idCarrera);
		$this->assertIsArray($experiencias);
		$this->assertEmpty($experiencias, 'No debe retornar experiencias para un profesor inexistente.');
	}

	public function testGetSeccionesByCarreraWithNonExistentCarrera()
	{
		$idCarrera = 999999;
		$secciones = $this->seccionModel->getSeccionesByCarrera($idCarrera);
		$this->assertIsArray($secciones);
		$this->assertEmpty($secciones, 'No debe retornar secciones para una carrera inexistente.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
