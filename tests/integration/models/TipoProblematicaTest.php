<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/TipoProblematica.php';
require_once __DIR__ . '/../../../config/connection.php';

class TipoProblematicaTest extends TestCase
{
	private $conn;
	private $tipoModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->tipoModel = new TipoProblematica($this->conn);
	}

	public function testCreateProblematica()
	{
		$name = 'Tipo de Prueba';
		$result = $this->tipoModel->createProblematica($name);
		$this->assertTrue($result);
	}

	public function testGetTipos()
	{
		$tipos = $this->tipoModel->getTipos();
		$this->assertIsArray($tipos);
		$this->assertNotEmpty($tipos);
	}

	public function testGetTiposProblematicasById()
	{
		$tipos = $this->tipoModel->getTipos();
		$id = $tipos[0]['idTipoProblematica'] ?? null;
		$tipo = $this->tipoModel->getTiposProblematicasById($id);
		$this->assertIsArray($tipo);
		$this->assertEquals($id, $tipo['idTipoProblematica']);
	}

	public function testUpdateTipoProblematica()
	{
		$tipos = $this->tipoModel->getTipos();
		$id = $tipos[0]['idTipoProblematica'] ?? null;
		$nuevoNombre = 'Tipo Actualizado';
		$result = $this->tipoModel->updateTipoProblematica($id, $nuevoNombre);
		$this->assertTrue($result);
	}

	public function testDeleteProblematica()
	{
		$name = 'Tipo para Eliminar';
		$this->tipoModel->createProblematica($name);
		$tipos = $this->tipoModel->getTipos();
		$id = null;
		foreach ($tipos as $t) {
			if ($t['nombre'] === $name) {
				$id = $t['idTipoProblematica'];
				break;
			}
		}
		$result = $this->tipoModel->deleteProblematica($id);
		$this->assertTrue($result);
	}

	public function testCreateProblematicaWithDuplicateName()
	{
		$name = 'Tipo Duplicado';
		$result1 = $this->tipoModel->createProblematica($name);
		$this->assertTrue($result1);
		$result2 = $this->tipoModel->createProblematica($name);
		$this->assertNotTrue($result2, 'No debe permitirse crear un tipo de problemática con nombre duplicado.');
	}

	public function testUpdateNonExistentTipoProblematica()
	{
		$idInexistente = 999999;
		$result = $this->tipoModel->updateTipoProblematica($idInexistente, 'NoExiste');
		$this->assertNotTrue($result, 'No debe permitirse actualizar un tipo de problemática inexistente.');
	}

	public function testDeleteNonExistentProblematica()
	{
		$idInexistente = 999999;
		$result = $this->tipoModel->deleteProblematica($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar un tipo de problemática inexistente.');
	}

	public function testCreateProblematicaWithMissingData()
	{
		$name = '';
		$result = $this->tipoModel->createProblematica($name);
		$this->assertNotTrue($result, 'No debe permitirse crear un tipo de problemática sin nombre.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
