<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Problematica.php';
require_once __DIR__ . '/../../../config/connection.php';

class ProblematicaTest extends TestCase
{
	private $conn;
	private $problematicaModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->problematicaModel = new Problematica($this->conn);
	}

	public function testCreateProblematica()
	{
		$descripcion = 'Problema de Prueba';
		$tipoProblematica = 1;
		$result = $this->problematicaModel->createProblematica($descripcion, $tipoProblematica);
		$this->assertTrue($result);
	}

	public function testGetProblematicas()
	{
		$problematicas = $this->problematicaModel->getProblematicas();
		$this->assertIsArray($problematicas);
		$this->assertNotEmpty($problematicas);
	}

	public function testGetProblematicaById()
	{
		$problematicas = $this->problematicaModel->getProblematicas();
		$id = $problematicas[0]['idProblematica'] ?? null;
		$problematica = $this->problematicaModel->getProblematicaById($id);
		$this->assertIsArray($problematica);
		$this->assertEquals($id, $problematica['idProblematica']);
	}

	public function testUpdateProblematica()
	{
		$problematicas = $this->problematicaModel->getProblematicas();
		$id = $problematicas[0]['idProblematica'] ?? null;
		$nuevaDescripcion = 'Problema Actualizado';
		$tipoProblematica = 1;
		$result = $this->problematicaModel->updateProblematica($id, $nuevaDescripcion, $tipoProblematica);
		$this->assertTrue($result);
	}

	public function testDeleteProblematica()
	{
		$descripcion = 'Problema para Eliminar';
		$tipoProblematica = 1;
		$this->problematicaModel->createProblematica($descripcion, $tipoProblematica);
		$problematicas = $this->problematicaModel->getProblematicas();
		$id = null;
		foreach ($problematicas as $p) {
			if ($p['descripcion'] === $descripcion) {
				$id = $p['idProblematica'];
				break;
			}
		}
		$result = $this->problematicaModel->deleteProblematica($id);
		$this->assertTrue($result);
	}

	public function testCreateProblematicaWithDuplicateDescription()
	{
		$descripcion = 'Problematica Duplicada';
		$tipoProblematica = 1;
		$result1 = $this->problematicaModel->createProblematica($descripcion, $tipoProblematica);
		$this->assertTrue($result1);
		$result2 = $this->problematicaModel->createProblematica($descripcion, $tipoProblematica);
		$this->assertNotTrue($result2, 'No debe permitirse crear una problemática con descripción duplicada.');
	}

	public function testUpdateNonExistentProblematica()
	{
		$idInexistente = 999999;
		$result = $this->problematicaModel->updateProblematica($idInexistente, 'NoExiste', 1);
		$this->assertNotTrue($result, 'No debe permitirse actualizar una problemática inexistente.');
	}

	public function testDeleteNonExistentProblematica()
	{
		$idInexistente = 999999;
		$result = $this->problematicaModel->deleteProblematica($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar una problemática inexistente.');
	}

	public function testCreateProblematicaWithMissingData()
	{
		$descripcion = '';
		$tipoProblematica = 1;
		$result = $this->problematicaModel->createProblematica($descripcion, $tipoProblematica);
		$this->assertNotTrue($result, 'No debe permitirse crear una problemática sin descripción.');
	}

	public function testCreateProblematicaWithNonExistentTipo()
	{
		$descripcion = 'TipoInexistente';
		$tipoProblematica = 999999;
		$result = $this->problematicaModel->createProblematica($descripcion, $tipoProblematica);
		$this->assertNotTrue($result, 'No debe permitirse crear una problemática con tipo inexistente.');
	}

	public function testGetProblematicasByReporteWithNonExistentReporte()
	{
		$idReporte = 999999;
		$problematicas = $this->problematicaModel->getProblematicasByReporte($idReporte);
		$this->assertIsArray($problematicas);
		$this->assertEmpty($problematicas, 'No debe retornar problemáticas para un reporte inexistente.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
