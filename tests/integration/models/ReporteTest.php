<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Reporte.php';
require_once __DIR__ . '/../../../config/connection.php';

class ReporteTest extends TestCase
{
	private $conn;
	private $reporteModel;

	protected function setUp(): void
	{
		$this->conn = connectiondb();
		$this->reporteModel = new Reporte($this->conn);
	}

	public function testGetReportesByTutor()
	{
		$correo = 'mario.lopez@uv.mx';
		$reportes = $this->reporteModel->getReportesByTutor($correo);
		$this->assertIsArray($reportes);
	}

	public function testGetReportesByTutorWithNonExistentCorreo()
	{
		$correo = 'no.existe@uv.mx';
		$reportes = $this->reporteModel->getReportesByTutor($correo);
		$this->assertIsArray($reportes);
		$this->assertEmpty($reportes, 'No debe retornar reportes para un tutor inexistente.');
	}

	public function testGetReportHistoryByTutor()
	{
		$correo = 'mario.lopez@uv.mx';
		$reportes = $this->reporteModel->getReportHistoryByTutor($correo);
		$this->assertIsArray($reportes);
	}

	public function testGetReportesByCoordinador()
	{
		$idSesion = 1;
		$reportes = $this->reporteModel->getReportesByCoordinador($idSesion);
		$this->assertIsArray($reportes);
	}

	public function testGetReportesByCoordinadorWithNonExistentSesion()
	{
		$idSesion = 999999;
		$reportes = $this->reporteModel->getReportesByCoordinador($idSesion);
		$this->assertIsArray($reportes);
		$this->assertEmpty($reportes, 'No debe retornar reportes para un coordinador inexistente.');
	}

	public function testGetHistorialReportesByCoordinador()
	{
		$idSesion = 1;
		$reportes = $this->reporteModel->getHistorialReportesByCoordinador($idSesion);
		$this->assertIsArray($reportes);
	}

	public function testCreateCarreraTutorWithDuplicate()
	{
		$carrera = 1;
		$idTutor = 1;
		$id1 = $this->reporteModel->createCarreraTutor($carrera, $idTutor);
		$id2 = $this->reporteModel->createCarreraTutor($carrera, $idTutor);
		$this->assertEquals($id1, $id2, 'No debe crear duplicados en carrera_tutor.');
	}

	public function testCreateReporteTutoriaWithMissingData()
	{
		$data = [
			// Falta numAsistencias
			'numRiesgo' => 1,
			'comentario' => 'Prueba',
			'fechaCreacion' => date('Y-m-d'),
			'carreraTutor' => 1,
			'esBorrador' => 0,
			'tutoria' => 1
		];
		$this->expectException(Exception::class);
		$this->reporteModel->createReporteTutoria($data);
	}

	public function testGetReporteByIdWithNonExistentId()
	{
		$idReporte = 999999;
		$reporte = $this->reporteModel->getReporteById($idReporte);
		$this->assertNull($reporte, 'No debe retornar reporte para un ID inexistente.');
	}

	public function testDeleteReporteTutoriaWithNonExistentId()
	{
		$idReporte = 999999;
		$this->reporteModel->deleteReporteTutoria($idReporte);
		$this->assertTrue(true);
	}

	protected function tearDown(): void
	{
		$this->conn->close();
	}
}
