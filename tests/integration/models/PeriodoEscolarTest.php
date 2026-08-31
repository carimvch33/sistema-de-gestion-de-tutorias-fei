<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/PeriodoEscolar.php';
require_once __DIR__ . '/../../../config/connection.php';

class PeriodoEscolarTest extends TestCase
{
	private $conn;
	private $periodoModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->periodoModel = new PeriodoEscolar($this->conn);
	}

	public function testCreatePeriodo()
	{
		$nombre = '2025-1';
		$actual = 0;
		$result = $this->periodoModel->createPeriodo($nombre, $actual);
		$this->assertTrue($result);
	}

	public function testGetPeriodos()
	{
		$periodos = $this->periodoModel->getPeriodos();
		$this->assertIsArray($periodos);
		$this->assertNotEmpty($periodos);
	}

	public function testGetPeriodoById()
	{
		$periodos = $this->periodoModel->getPeriodos();
		$id = $periodos[0]['idPeriodo'] ?? null;
		$periodo = $this->periodoModel->getPeriodoById($id);
		$this->assertIsArray($periodo);
		$this->assertEquals($id, $periodo['idPeriodo']);
	}

	public function testGetCurrentPeriodo() //Replantear revisar código
	{
		$periodo = $this->periodoModel->getCurrentPeriodo();
		$this->assertIsArray($periodo);
	}

	public function testUpdatePeriodo()
	{
		$periodos = $this->periodoModel->getPeriodos();
		$id = $periodos[0]['idPeriodo'] ?? null;
		$nuevoNombre = '2025-2';
		$actual = 1;
		$result = $this->periodoModel->updatePeriodo($id, $nuevoNombre, $actual);
		$this->assertTrue($result);
	}

	public function testDeletePeriodo()
	{
		$nombre = 'Periodo para Eliminar';
		$actual = 0;
		$this->periodoModel->createPeriodo($nombre, $actual);
		$periodos = $this->periodoModel->getPeriodos();
		$id = null;
		foreach ($periodos as $p) {
			if ($p['periodo'] === $nombre) {
				$id = $p['idPeriodo'];
				break;
			}
		}
		$result = $this->periodoModel->deletePeriodo($id);
		$this->assertTrue($result);
	}

	public function testCreatePeriodoWithDuplicateName()
	{
		$nombre = 'Periodo Duplicado';
		$actual = 0;
		$result1 = $this->periodoModel->createPeriodo($nombre, $actual);
		$this->assertTrue($result1);
		$result2 = $this->periodoModel->createPeriodo($nombre, $actual);
		$this->assertNotTrue($result2, 'No debe permitirse crear un periodo escolar con nombre duplicado.');
	}

	public function testUpdateNonExistentPeriodo()
	{
		$idInexistente = 999999;
		$result = $this->periodoModel->updatePeriodo($idInexistente, 'Nombre Inexistente', 0);
		$this->assertNotTrue($result, 'No debe permitirse actualizar un periodo inexistente.');
	}

	public function testDeleteNonExistentPeriodo()
	{
		$idInexistente = 999999;
		$result = $this->periodoModel->deletePeriodo($idInexistente);
		$this->assertNotTrue($result, 'No debe permitirse eliminar un periodo inexistente.');
	}

	public function testCreatePeriodoWithMissingData()
	{
		$nombre = '';
		$actual = 0;
		$result = $this->periodoModel->createPeriodo($nombre, $actual);
		$this->assertNotTrue($result, 'No debe permitirse crear un periodo escolar sin nombre.');
	}

	public function testGetPeriodoByNombre()
	{
		$nombre = '2025-1';
		$periodo = $this->periodoModel->getPeriodoByNombre($nombre);
		$this->assertIsArray($periodo);
		$this->assertEquals($nombre, $periodo['periodo']);
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
