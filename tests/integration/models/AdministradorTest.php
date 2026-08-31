<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../models/Administrador.php';
require_once __DIR__ . '/../../../config/connection.php';

class AdministradorTest extends TestCase
{
    private $conn;
    private $adminModel;

    protected function setUp(): void
    {
        $this->conn = connectiondb();
        $this->adminModel = new Administrador($this->conn);
    }

    public function testCreateAdministrador()
    {
        $data = [
            'nombre' => 'Juan',
            'apellidoPaterno' => 'Pérez',
            'apellidoMaterno' => 'Gómez',
            'correoInstitucional' => 'juan.perez@uv.mx',
            'rol' => 1,
            'password' => '123456'
        ];
        $result = $this->adminModel->createAdministrador($data);
        $this->assertTrue($result);
    }

    public function testGetAdministradorByCorreo()
    {
        $correo = 'juan.perez@uv.mx';
        $admin = $this->adminModel->getAdministradorByCorreo($correo);
        $this->assertIsArray($admin);
        $this->assertEquals($correo, $admin['correoInstitucional']);
    }

    public function testGetAdministradorByNonExistentCorreo()
    {
        $correo = 'no.existe@uv.mx';
        $admin = $this->adminModel->getAdministradorByCorreo($correo);
        $this->assertTrue($admin === null || $admin === false, 'Debe retornar null o false si el correo no existe.');
    }

    public function testUpdateAdministrador()
    {
        $admin = $this->adminModel->getAdministradorByCorreo('juan.perez@uv.mx');
        $id = $admin['idAdministrador'];
        $data = [
            'nombre' => 'Juanito',
            'apellidoPaterno' => 'Pérez',
            'apellidoMaterno' => 'Gómez',
            'correoInstitucional' => 'juanito.perez@uv.mx',
            'password' => '654321'
        ];
        $result = $this->adminModel->updateAdministrador($id, $data);
        $this->assertTrue($result);
    }

    public function testDeleteAdministrador()
    {
        $admin = $this->adminModel->getAdministradorByCorreo('juanito.perez@uv.mx');
        $id = $admin['idAdministrador'];
        $result = $this->adminModel->deleteAdministrador($id);
        $this->assertTrue($result);
    }

    public function testCreateAdministradorWithDuplicateCorreo()
    {
        $data1 = [
            'nombre' => 'Pedro',
            'apellidoPaterno' => 'López',
            'apellidoMaterno' => 'Ramírez',
            'correoInstitucional' => 'pedro.lopez@uv.mx',
            'rol' => 1,
            'password' => 'abc123'
        ];
        $result1 = $this->adminModel->createAdministrador($data1);
        $this->assertTrue($result1, 'El primer administrador debe crearse correctamente.');

        $data2 = [
            'nombre' => 'Pedro2',
            'apellidoPaterno' => 'López2',
            'apellidoMaterno' => 'Ramírez2',
            'correoInstitucional' => 'pedro.lopez@uv.mx',
            'rol' => 1,
            'password' => 'def456'
        ];
        $result2 = $this->adminModel->createAdministrador($data2);

        $this->assertNotTrue($result2, 'No debe permitirse crear un administrador con correo duplicado.');

        $admin = $this->adminModel->getAdministradorByCorreo('pedro.lopez@uv.mx');
        if ($admin && isset($admin['idAdministrador'])) {
            $this->adminModel->deleteAdministrador($admin['idAdministrador']);
        }
    }

    public function testUpdateNonExistentAdministrador()
    {
        $idInexistente = 999999;
        $data = [
            'nombre' => 'NoExiste',
            'apellidoPaterno' => 'Prueba',
            'apellidoMaterno' => 'Test',
            'correoInstitucional' => 'no.existe@uv.mx',
            'password' => '123456'
        ];
        $result = $this->adminModel->updateAdministrador($idInexistente, $data);
        $this->assertNotTrue($result, 'No debe permitirse actualizar un administrador inexistente.');
    }

    public function testDeleteNonExistentAdministrador()
    {
        $idInexistente = 999999;
        $result = $this->adminModel->deleteAdministrador($idInexistente);
        $this->assertNotTrue($result, 'No debe permitirse eliminar un administrador inexistente.');
    }

    public function testCreateAdministradorWithInvalidCorreo()
    {
        $data = [
            'nombre' => 'CorreoInvalido',
            'apellidoPaterno' => 'Prueba',
            'apellidoMaterno' => 'Test',
            'correoInstitucional' => 'correo-invalido', 
            'rol' => 1,
            'password' => '123456'
        ];
        $result = $this->adminModel->createAdministrador($data);
        $this->assertNotTrue($result, 'No debe permitirse crear un administrador con correo inválido.');
    }

    protected function tearDown(): void
    {
        $this->conn->close();
    }
}