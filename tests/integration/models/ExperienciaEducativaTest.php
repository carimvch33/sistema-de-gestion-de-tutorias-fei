<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/ExperienciaEducativa.php';
require_once __DIR__ . '/../../../config/connection.php';

class ExperienciaEducativaTest extends TestCase
{
	private $conn;
	private $eeModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->eeModel = new ExperienciaEducativa($this->conn);
	}

	public function testCreateExperiencia()
	{
		$nombre = 'Matemáticas de Prueba';
		$programa = 1;
		$result = $this->eeModel->createExperiencia($nombre, $programa);
		$this->assertTrue($result);
	}

	public function testGetExperiencias()
	{
		$experiencias = $this->eeModel->getExperiencias();
		$this->assertIsArray($experiencias);
		$this->assertNotEmpty($experiencias);
	}

	public function testGetExperienciaById()
	{
		$experiencias = $this->eeModel->getExperiencias();
		$id = $experiencias[0]['idExperienciaEducativa'] ?? null;
		$experiencia = $this->eeModel->getExperienciaById($id);
		$this->assertIsArray($experiencia);
		$this->assertEquals($id, $experiencia['idExperienciaEducativa']);
	}

	public function testUpdateExperiencia()
	{
		$experiencias = $this->eeModel->getExperiencias();
		$id = $experiencias[0]['idExperienciaEducativa'] ?? null;
		$nuevoNombre = 'Matemáticas Actualizada';
		$nuevoPrograma = 1;
		$result = $this->eeModel->updateExperiencia($id, $nuevoNombre, $nuevoPrograma);
		$this->assertTrue($result);
	}

	public function testDeleteExperiencia()
	{
		
		$nombre = 'Experiencia para Eliminar';
		$programa = 1;
		$this->eeModel->createExperiencia($nombre, $programa);
		$experiencias = $this->eeModel->getExperiencias();
		$id = null;
		foreach ($experiencias as $ee) {
			if ($ee['nombreEE'] === $nombre) {
				$id = $ee['idExperienciaEducativa'];
				break;
			}
		}
		$result = $this->eeModel->deleteExperiencia($id);
		$this->assertTrue($result);
	}

	public function testCreateExperienciaWithDuplicateName()
	{
		$nombre = 'Experiencia Duplicada';
		$programa = 1;
		$result1 = $this->eeModel->createExperiencia($nombre, $programa);
		$this->assertTrue($result1);
		$result2 = $this->eeModel->createExperiencia($nombre, $programa);
		$this->assertNotTrue($result2, 'No debe permitirse crear una experiencia educativa con nombre duplicado.');
		$experiencias = $this->eeModel->getExperiencias();
		foreach ($experiencias as $ee) {
			if ($ee['nombreEE'] === $nombre) {
				$this->eeModel->deleteExperiencia($ee['idExperienciaEducativa']);
			}
		}
	}

	public function testUpdateNonExistentExperiencia()
	{
		$idInexistente = 999999;
		$result = $this->eeModel->updateExperiencia($idInexistente, 'Nombre Inexistente', 1);
		$this->assertNotTrue($result, 'No debe permitirse actualizar una experiencia inexistente.');
	}

	public function testDeleteNonExistentExperiencia()
	{
		$idInexistente = 999999;
		$result = $this->eeModel->deleteExperiencia($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar una experiencia inexistente.');
	}

	public function testCreateExperienciaWithNonExistentPrograma()
	{
		$nombre = 'ProgramaInexistente';
		$programa = 999999;
		$result = $this->eeModel->createExperiencia($nombre, $programa);
		$this->assertNotTrue($result, 'No debe permitirse crear una experiencia educativa con programa inexistente.');
	}

	public function testGetExperienciasByCarrera()
	{
		$idCarrera = 1; 
		$experiencias = $this->eeModel->getExperienciasByCarrera($idCarrera);
		$this->assertIsArray($experiencias);
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
