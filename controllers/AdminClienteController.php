<?php
/**
 * Controlador de Administración de Clientes
 * Gestiona usuarios y clientes desde el área administrativa
 */
class AdminClienteController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Listar todos los clientes
     */
    public function listar() {
        requireAdmin();
        
        $usuario = new Usuario($this->db);
        $clientes = $usuario->obtenerClientes();
        
        require_once 'views/admin/clientes-listar.php';
    }
    
    /**
     * Ver detalle de cliente
     */
    public function detalle($id) {
        requireAdmin();
        
        $usuario = new Usuario($this->db);
        $cliente = $usuario->obtenerPorId($id);
        
        if (!$cliente || $cliente['rol'] !== 'cliente') {
            setFlash('error', 'Cliente no encontrado');
            redirect('admin/clientes.php');
        }
        
        // Obtener pedidos del cliente
        $pedido = new Pedido($this->db);
        $pedidos = $pedido->obtenerPorCliente($id);
        
        require_once 'views/admin/clientes-detalle.php';
    }
    
    /**
     * Crear nuevo cliente
     */
    public function crear() {
        requireAdmin();
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = sanitize($_POST['nombre'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            if (empty($nombre) || empty($email) || empty($password)) {
                $error = 'Por favor complete todos los campos';
            } elseif (!validateEmail($email)) {
                $error = 'Email inválido';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
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
                        setFlash('success', 'Cliente creado exitosamente');
                        redirect('admin/clientes.php');
                    } else {
                        $error = 'Error al crear el cliente';
                    }
                }
            }
        }
        
        require_once 'views/admin/clientes-crear.php';
    }
    
    /**
     * Editar cliente
     */
    public function editar($id) {
        requireAdmin();
        
        $usuario = new Usuario($this->db);
        $cliente = $usuario->obtenerPorId($id);
        
        if (!$cliente || $cliente['rol'] !== 'cliente') {
            setFlash('error', 'Cliente no encontrado');
            redirect('admin/clientes.php');
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = sanitize($_POST['nombre'] ?? '');
            
            if (empty($nombre)) {
                $error = 'Por favor complete el nombre';
            } else {
                $usuario->id = $id;
                $usuario->nombre = $nombre;
                
                if ($usuario->actualizar()) {
                    setFlash('success', 'Cliente actualizado exitosamente');
                    redirect('admin/clientes.php');
                } else {
                    $error = 'Error al actualizar el cliente';
                }
            }
        }
        
        require_once 'views/admin/clientes-editar.php';
    }
    
    /**
     * Eliminar cliente
     */
    public function eliminar() {
        requireAdmin();
        
        $cliente_id = intval($_POST['cliente_id'] ?? 0);
        
        if ($cliente_id > 0) {
            $usuario = new Usuario($this->db);
            
            if ($usuario->eliminar($cliente_id)) {
                setFlash('success', 'Cliente eliminado exitosamente');
            } else {
                setFlash('error', 'Error al eliminar el cliente');
            }
        }
        
        redirect('admin/clientes.php');
    }
}
