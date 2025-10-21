<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/PeriodoTutorias.php';
require_once __DIR__ . '/../../../config/connection.php';

class PeriodoTutoriasTest extends TestCase
{
	private $conn;
	private $ptModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->ptModel = new PeriodoTutorias($this->conn);
	}

	public function testGetPeriodosTutoriasByCarrera()
	{
		$idCarrera = 1;
		$fechas = $this->ptModel->getPeriodosTutoriasByCarrera($idCarrera);
		$this->assertIsArray($fechas);
	}

	public function testGetPeriodosTutoriasByCarreraWithNonExistentCarrera()
	{
		$idCarrera = 999999;
		$fechas = $this->ptModel->getPeriodosTutoriasByCarrera($idCarrera);
		$this->assertIsArray($fechas);
		$this->assertEmpty($fechas, 'No debe retornar periodos para una carrera inexistente.');
	}

	public function testGetPeriodoTutoriasById()
	{
		$idPeriodoTutorias = 1;
		$fechas = $this->ptModel->getPeriodoTutoriasById($idPeriodoTutorias);
		$this->assertIsArray($fechas);
	}

	public function testGetPeriodoTutoriasByIdWithNonExistentId()
	{
		$idPeriodoTutorias = 999999;
		$fechas = $this->ptModel->getPeriodoTutoriasById($idPeriodoTutorias);
		$this->assertIsArray($fechas);
		$this->assertEmpty($fechas, 'No debe retornar datos para un ID de periodo tutorías inexistente.');
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
