<?php
require_once 'models/Colaborador.php';
require_once 'controllers/AuthController.php';
require_once 'utils/Sanitizador.php';

class ColaboradoresController
{
    private $colaborador;
    private $auth;

    public function __construct()
    {
        $this->colaborador = new Colaborador();
        $this->auth = new AuthController();
    }

    private function verificarAcceso()
    {
        if (!$this->auth->tienePermiso('colaboradores.acceso')) {
            http_response_code(403);
            include 'views/layouts/403.php';
            exit();
        }
    }

    public function index()
    {
        $this->verificarAcceso();
        $colaboradores = $this->colaborador->obtenerTodos();
        include 'views/colaboradores/index.php';
    }

    public function crear()
    {
        $this->verificarAcceso();
        include 'views/colaboradores/crear.php';
    }

    public function store()
    {
        $this->verificarAcceso();

        // Sanitizar datos del colaborador
        $datos = [
            'primer_nombre'      => Sanitizador::texto($_POST['primer_nombre']),
            'segundo_nombre'     => Sanitizador::texto($_POST['segundo_nombre']),
            'primer_apellido'    => Sanitizador::texto($_POST['primer_apellido']),
            'segundo_apellido'   => Sanitizador::texto($_POST['segundo_apellido']),
            'sexo'               => Sanitizador::texto($_POST['sexo']),
            'cedula'             => Sanitizador::texto($_POST['cedula']),
            'fecha_nacimiento'   => $_POST['fecha_nacimiento'],
            'telefono'           => Sanitizador::texto($_POST['telefono']),
            'celular'            => Sanitizador::texto($_POST['celular']),
            'direccion'          => Sanitizador::texto($_POST['direccion']),
            'correo_personal'    => Sanitizador::email($_POST['correo_personal']),
            'sueldo'             => Sanitizador::flotante($_POST['sueldo']),
            'departamento_id'    => Sanitizador::entero($_POST['departamento_id']),
            'fecha_contratacion' => $_POST['fecha_contratacion'],
            'tipo_empleado'      => Sanitizador::texto($_POST['tipo_empleado']),
            'ocupacion'          => Sanitizador::texto($_POST['ocupacion']),
            'empleado_activo'    => 1,
            'foto_perfil'        => null
        ];

        // 📸 Subir foto de perfil
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $foto = $this->subirArchivo($_FILES['foto_perfil'], 'uploads/fotos', ['jpg', 'jpeg', 'png'], 2 * 1024 * 1024);
            if ($foto) {
                $datos['foto_perfil'] = $foto;
            }
        }

        if ($this->colaborador->crear($datos)) {
            $colaborador_id = $this->colaborador->getLastInsertId();

            // 📄 Subir historial académico si está activado
            if (!empty($_POST['tipo_documento']) && !empty($_FILES['archivo_pdf']['name'])) {
                $this->guardarDocumentoAcademico($colaborador_id);
            }

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador creado exitosamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al crear colaborador'];
        }
        header('Location: colaboradores.php');
        exit();
    }

    public function editar($id)
    {
        $this->verificarAcceso();

        $colaborador = $this->colaborador->obtenerPorId($id);
        $documentos = $this->colaborador->obtenerDocumentos($id);
        if (!$colaborador) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Colaborador no encontrado'];
            header('Location: colaboradores.php');
            exit();
        }

        include 'views/colaboradores/editar.php';
    }

    public function update($id)
    {
        $this->verificarAcceso();

        // Sanitizar datos del colaborador
        $datos = [
            'primer_nombre'      => Sanitizador::texto($_POST['primer_nombre']),
            'segundo_nombre'     => Sanitizador::texto($_POST['segundo_nombre']),
            'primer_apellido'    => Sanitizador::texto($_POST['primer_apellido']),
            'segundo_apellido'   => Sanitizador::texto($_POST['segundo_apellido']),
            'sexo'               => Sanitizador::texto($_POST['sexo']),
            'cedula'             => Sanitizador::texto($_POST['cedula']),
            'fecha_nacimiento'   => $_POST['fecha_nacimiento'],
            'telefono'           => Sanitizador::texto($_POST['telefono']),
            'celular'            => Sanitizador::texto($_POST['celular']),
            'direccion'          => Sanitizador::texto($_POST['direccion']),
            'correo_personal'    => Sanitizador::email($_POST['correo_personal']),
            'sueldo'             => Sanitizador::flotante($_POST['sueldo']),
            'departamento_id'    => Sanitizador::entero($_POST['departamento_id']),
            'fecha_contratacion' => $_POST['fecha_contratacion'],
            'tipo_empleado'      => Sanitizador::texto($_POST['tipo_empleado']),
            'ocupacion'          => Sanitizador::texto($_POST['ocupacion']),
        ];

        // 📸 Subir nueva foto de perfil
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $foto = $this->subirArchivo($_FILES['foto_perfil'], 'uploads/fotos', ['jpg', 'jpeg', 'png'], 2 * 1024 * 1024);
            if ($foto) {
                $datos['foto_perfil'] = $foto;
            }
        }

        if ($this->colaborador->actualizar($id, $datos)) {
            // 📄 Subir nuevo historial académico si está activado
            if (!empty($_POST['tipo_documento']) && !empty($_FILES['archivo_pdf']['name'])) {
                $this->guardarDocumentoAcademico($id);
            }

            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador actualizado correctamente'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al actualizar colaborador'];
        }
        header('Location: colaboradores.php');
        exit();
    }

    private function guardarDocumentoAcademico($colaborador_id)
    {
        $pdf = $this->subirArchivo($_FILES['archivo_pdf'], 'uploads/pdf', ['pdf'], 5 * 1024 * 1024);
        if ($pdf) {
            $doc = [
                'colaborador_id'   => $colaborador_id,
                'tipo_documento'   => Sanitizador::texto($_POST['tipo_documento']),
                'nombre_documento' => Sanitizador::texto($_POST['nombre_documento']),
                'institucion'      => Sanitizador::texto($_POST['institucion']),
                'fecha_emision'    => $_POST['fecha_emision'],
                'archivo_pdf'      => $pdf,
                'verificado'       => 0,
                'observaciones'    => null
            ];
            $this->colaborador->guardarDocumento($doc);
        }
    }

    private function subirArchivo($file, $ruta, $extensionesPermitidas, $maxSize)
    {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $extensionesPermitidas)) return false;
        if ($file['size'] > $maxSize) return false;

        $nuevoNombre = uniqid() . '.' . $ext;
        $destino = $ruta . '/' . $nuevoNombre;

        if (!is_dir($ruta)) mkdir($ruta, 0777, true);

        if (move_uploaded_file($file['tmp_name'], $destino)) {
            return $destino;
        }
        return false;
    }

    public function ver($id)
    {
        $this->verificarAcceso();

        $colaborador = $this->colaborador->obtenerPorId($id);
        $documentos = $this->colaborador->obtenerDocumentos($id);
        if (!$colaborador) {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Colaborador no encontrado'];
            header('Location: colaboradores.php');
            exit();
        }

        include 'views/colaboradores/ver.php';
    }

    public function desactivar($id)
    {
        $this->verificarAcceso();

        if ($this->colaborador->desactivar($id)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador desactivado'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al desactivar colaborador'];
        }
        header('Location: colaboradores.php');
        exit();
    }

    public function activar($id)
    {
        $this->verificarAcceso();

        if ($this->colaborador->activar($id)) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Colaborador activado'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => 'Error al activar colaborador'];
        }
        header('Location: colaboradores.php');
        exit();
    }
}
