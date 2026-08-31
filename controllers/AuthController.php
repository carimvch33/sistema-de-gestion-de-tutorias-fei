<?php
require_once '../config/connection.php';
require_once '../config/config.php';
require_once '../models/User.php';
require_once '../models/Administrador.php';

class AuthController
{
    public function login($data)
    {
        if (empty($data['user']) || empty($data['password'])) {
            $this->redirectWithMessage('error');
        }

        $user = $data['user'];
        $password = $data['password'];

        $conn = connectiondb();
        $userModel = new User($conn);
        $userInfo = $userModel->findUser($user);
        $adminModel = new Administrador($conn);

        if (isProduction()) {
            if ($this->authenticateWithLDAP($user, $password)) {
                $userInfo = $userModel->findUser($user);
                if ($userInfo) {
                    $this->startUserSession($userInfo);
                } else {
                    $this->redirectWithMessage('no_exist');
                }
            } else {
                $adminInfo = $adminModel->getAdministradorByCorreo($user);
                if ($adminInfo && isset($adminInfo['password'])) {
                    if (password_verify($password, $adminInfo['password'])) {
                        $userInfo = $userModel->findUser($user);
                        $this->startUserSession($userInfo);
                    } else {
                        $this->redirectWithMessage('no_login');
                    }
                } else {
                    $this->redirectWithMessage('no_exist');
                }
            }
        } else {
            if ($userInfo) {
                $this->startUserSession($userInfo);
            } else {
                $this->redirectWithMessage('no_exist');
            }
        }
    }

    private function authenticateWithLDAP($username, $password)
    {
        $ldaprdn = $username;
        $ldappass = $password;
        $ldapconn = ldap_connect('148.226.12.10');

        if ($ldapconn) {
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

            $ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);

            if ($ldapbind) {
                return true;
            }
        }

        return false;
    }

    private function startUserSession($userInfo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION["user"] = $userInfo['nombre'];
        $_SESSION["rol"] = $userInfo['rol'];
        $_SESSION["correoInstitucional"] = $userInfo['correoInstitucional'];
        $_SESSION["periodoActual"] = $userInfo['periodoActual'];

        header("Location: " . BASE_URL . "/menu.php");
        exit();
    }

    private function startAdminSession($userInfo)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION["user"] = $userInfo['nombre'];
        $_SESSION["rol"] = 3;
        $_SESSION["correoInstitucional"] = $userInfo['correoInstitucional'];
        $_SESSION["periodoActual"] = $userInfo['periodoActual'];

        header("Location: " . BASE_URL . "/menu.php");
        exit();
    }

    private function redirectWithMessage($message)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION["message"] = $message;
        header("Location: " . BASE_URL . "/index.php");
        exit();
    }
}
?>