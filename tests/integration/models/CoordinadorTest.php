<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Coordinador.php';
require_once __DIR__ . '/../../../config/connection.php';

class CoordinadorTest extends TestCase
{
    private $conn;
    private $coordinadorModel;

    protected function setUp(): void
    {
        $this->conn = connectiondb();
        $this->coordinadorModel = new Coordinador($this->conn);
    }

    public function testCreateCoordinador()
    {
        $data = [
            'nombre' => 'Luis',
            'apellidoPaterno' => 'Martínez',
            'apellidoMaterno' => 'García',
            'noPersonal' => '12345',
            'correoInstitucional' => 'luis.martinez@uv.mx',
            'rol' => 4,
            'carreras' => [1]
        ];
        $result = $this->coordinadorModel->createCoordinador($data);
        $this->assertTrue($result);
    }

    public function testGetCoordinadores()
    {
        $coordinadores = $this->coordinadorModel->getCoordinadores();
        $this->assertIsArray($coordinadores);
        $this->assertNotEmpty($coordinadores);
    }

    public function testGetCoordinadorById()
    {
        $coordinadores = $this->coordinadorModel->getCoordinadores();
        $id = $coordinadores[0]['idTutor'] ?? null;
        $coordinador = $this->coordinadorModel->getCoordinadorById($id);
        $this->assertIsArray($coordinador);
        $this->assertEquals($id, $coordinador['idTutor']);
    }

    public function testUpdateCoordinador()
    {
        $coordinadores = $this->coordinadorModel->getCoordinadores();
        $id = $coordinadores[0]['idTutor'] ?? null;
        $data = [
            'nombre' => 'Luis Actualizado',
            'apellidoPaterno' => 'Martínez',
            'apellidoMaterno' => 'García',
            'noPersonal' => '54321',
            'correoInstitucional' => 'luis.actualizado@uv.mx',
            'carreras' => [1]
        ];
        $result = $this->coordinadorModel->updateCoordinador($id, $data);
        $this->assertTrue($result);
    }

    public function testDeleteCoordinador()
    {
        $data = [
            'nombre' => 'Eliminar',
            'apellidoPaterno' => 'Prueba',
            'apellidoMaterno' => 'Test',
            'noPersonal' => '99999',
            'correoInstitucional' => 'eliminar@uv.mx',
            'rol' => 4,
            'carreras' => [1]
        ];
        $this->coordinadorModel->createCoordinador($data);
        $coordinadores = $this->coordinadorModel->getCoordinadores();
        $id = null;
        foreach ($coordinadores as $coord) {
            if ($coord['correoInstitucional'] === 'eliminar@uv.mx') {
                $id = $coord['idTutor'];
                break;
            }
        }
        $result = $this->coordinadorModel->deleteCoordinador($id);
        $this->assertTrue($result);
    }

    public function testUpdateNonExistentCoordinador()
    {
        $idInexistente = 999999;
        $data = [
            'nombre' => 'NoExiste',
            'apellidoPaterno' => 'Prueba',
            'apellidoMaterno' => 'Test',
            'noPersonal' => '00000',
            'correoInstitucional' => 'no.existe@uv.mx',
            'carreras' => [1]
        ];
        $result = $this->coordinadorModel->updateCoordinador($idInexistente, $data);
        $this->assertNotTrue($result, 'No debe permitirse actualizar un coordinador inexistente.');
    }

    public function testDeleteNonExistentCoordinador()
    {
        $idInexistente = 999999;
        $result = $this->coordinadorModel->deleteCoordinador($idInexistente);
        $this->assertNotTrue($result, 'No debe permitirse eliminar un coordinador inexistente.');
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }
}