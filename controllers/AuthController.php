<?php
/**
 * Controlador de Autenticación
 * Gestiona login, registro y logout
 */
class AuthController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Vista de Login
     */
    public function login() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $error = 'Por favor complete todos los campos';
            } else {
                $usuario = new Usuario($this->db);
                
                if ($usuario->login($email, $password)) {
                    $_SESSION['usuario_id'] = $usuario->id;
                    $_SESSION['nombre'] = $usuario->nombre;
                    $_SESSION['email'] = $usuario->email;
                    $_SESSION['rol'] = $usuario->rol;
                    
                    setFlash('success', 'Bienvenido ' . $usuario->nombre);
                    
                    if ($usuario->rol == 'administrador') {
                        redirect('admin/dashboard.php');
                    } else {
                        redirect('index.php');
                    }
                } else {
                    $error = 'Credenciales incorrectas';
                }
            }
        }
        
        require_once 'views/auth/login.php';
    }
    
    /**
     * Vista de Registro
     */
    public function register() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = sanitize($_POST['nombre'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validaciones
            if (empty($nombre) || empty($email) || empty($password) || empty($confirm_password)) {
                $error = 'Por favor complete todos los campos';
            } elseif (!validateEmail($email)) {
                $error = 'Por favor ingrese un email válido';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } elseif ($password !== $confirm_password) {
                $error = 'Las contraseñas no coinciden';
            } else {
                $usuario = new Usuario($this->db);
                
                if ($usuario->emailExiste($email)) {
                    $error = 'Este email ya está registrado';
                } else {
                    $usuario->nombre = $nombre;
                    $usuario->email = $email;
                    $usuario->contraseña = $password;
                    $usuario->rol = 'cliente';
                    
                    if ($usuario->crear()) {
                        $success = 'Registro exitoso. Ya puedes iniciar sesión.';
                        setFlash('success', $success);
                    } else {
                        $error = 'Error al crear la cuenta. Intente nuevamente.';
                    }
                }
            }
        }
        
        require_once 'views/auth/register.php';
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        redirect('index.php');
    }
    
    /**
     * Vista de Perfil
     */
    public function perfil() {
        requireAuth();
        
        $usuario = new Usuario($this->db);
        $usuario_data = $usuario->obtenerPorId($_SESSION['usuario_id']);
        
        if (!$usuario_data) {
            setFlash('error', 'Usuario no encontrado');
            redirect('index.php');
        }
        
        require_once 'views/customer/perfil.php';
    }
}
