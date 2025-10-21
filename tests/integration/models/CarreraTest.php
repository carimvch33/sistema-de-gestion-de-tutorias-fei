<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Carrera.php';
require_once __DIR__ . '/../../../config/connection.php';

class CarreraTest extends TestCase
{
    private $conn;
    private $carreraModel;

    protected function setUp(): void
    {
        $this->conn = connectiondb();
        $this->carreraModel = new Carrera($this->conn);
    }

    public function testCreateCarrera()
    {
        $nombre = 'Ingeniería de Pruebas';
        $result = $this->carreraModel->createCarrera($nombre);
        $this->assertTrue($result);
    }

    public function testGetCarreras()
    {
        $carreras = $this->carreraModel->getCarreras();
        $this->assertIsArray($carreras);
        $this->assertNotEmpty($carreras);
    }

    public function testGetCarreraById()
    {
        $carreras = $this->carreraModel->getCarreras();
        $id = $carreras[0]['idCarrera'] ?? null;
        $carrera = $this->carreraModel->getCarreraById($id);
        $this->assertIsArray($carrera);
        $this->assertEquals($id, $carrera['idCarrera']);
    }

    public function testUpdateCarrera()
    {
        $carreras = $this->carreraModel->getCarreras();
        $id = $carreras[0]['idCarrera'] ?? null;
        $nuevoNombre = 'Ingeniería Actualizada';
        $result = $this->carreraModel->updateCarrera($id, $nuevoNombre);
        $this->assertTrue($result);
    }

    public function testDeleteCarrera()
    {
        // Creamos una carrera para eliminar
        $nombre = 'Carrera para Eliminar';
        $this->carreraModel->createCarrera($nombre);
        $carreras = $this->carreraModel->getCarreras();
        $id = null;
        foreach ($carreras as $carrera) {
            if ($carrera['carrera'] === $nombre) {
                $id = $carrera['idCarrera'];
                break;
            }
        }
        $result = $this->carreraModel->deleteCarrera($id);
        $this->assertTrue($result);
    }

    public function testCreateCarreraWithDuplicateName()
    {
        $nombre = 'Carrera Duplicada';
        $result1 = $this->carreraModel->createCarrera($nombre);
        $this->assertTrue($result1);
        $result2 = $this->carreraModel->createCarrera($nombre);
        $this->assertNotTrue($result2, 'No debe permitirse crear una carrera con nombre duplicado.');
        // Limpieza
        $carreras = $this->carreraModel->getCarreras();
        foreach ($carreras as $carrera) {
            if ($carrera['carrera'] === $nombre) {
                $this->carreraModel->deleteCarrera($carrera['idCarrera']);
            }
        }
    }

    public function testUpdateNonExistentCarrera()
    {
        $idInexistente = 999999;
        $result = $this->carreraModel->updateCarrera($idInexistente, 'Nombre Inexistente');
        $this->assertNotTrue($result, 'No debe permitirse actualizar una carrera inexistente.');
    }

    public function testDeleteNonExistentCarrera()
    {
        $idInexistente = 999999;
        $result = $this->carreraModel->deleteCarrera($idInexistente);
        $this->assertNotTrue($result, 'No debe permitirse eliminar una carrera inexistente.');
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }
}
