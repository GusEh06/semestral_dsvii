<?php
require_once 'models/Documento.php';
require_once 'controllers/AuthController.php';

class DocumentosController
{
    private $documento;
    private $auth;

    public function __construct()
    {
        $this->documento = new Documento();
        $this->auth = new AuthController();
    }

    public function eliminar($id)
    {
        // Validar permisos
        if (!$this->auth->tienePermiso('colaboradores.acceso')) {
            http_response_code(403);
            include 'views/layouts/403.php';
            exit();
        }

        // Obtener datos del documento
        $doc = $this->documento->obtenerPorId($id);
        if (!$doc) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Documento no encontrado'];
            header('Location: colaboradores.php');
            exit();
        }

        $colaborador_id = $doc['colaborador_id'];

        // Eliminar archivo físico
        if (file_exists($doc['archivo_pdf'])) {
            unlink($doc['archivo_pdf']);
        }

        // Eliminar registro de la BD
        if ($this->documento->eliminar($id)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Documento eliminado correctamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al eliminar documento'];
        }

        header('Location: colaboradores.php?accion=ver&id=' . $colaborador_id);
        exit();
    }
}
