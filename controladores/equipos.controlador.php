<?php



class ControladorEquipos{

    static public function ctrMostrarEquipos($item, $valor)
    {
        $tabla = "equipos";
        $respuesta = ModeloEquipos::mdlMostrarEquipos($tabla, $item, $valor);
        //var_dump($respuesta[0]);
        // error_log($respuesta);
        return $respuesta;
    }

    public static function ctrAgregarEquipos() {
        if (isset($_POST["numero_serie"]) && isset($_POST["etiqueta"]) && isset($_POST["descripcion"]) && isset($_POST["categoria_id"]) && isset($_POST["cuentadante_id"])) {
    
            // Validación de caracteres
            if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["numero_serie"]) &&
                preg_match('/^[a-zA-Z0-9]+$/', $_POST["etiqueta"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcion"])) {
    
                // Definir los datos sin ubicación, cuentadante ni estado (se asignan en el modelo)
                $tabla = "equipos";
                $datos = array(
                    "numero_serie" => $_POST["numero_serie"],
                    "cuentadante_id" => $_POST["cuentadante_id"],
                    "etiqueta" => $_POST["etiqueta"],
                    "descripcion" => $_POST["descripcion"],
                    "categoria_id" => $_POST["categoria_id"] // Solo enviamos categoría, los demás valores se asignan en el modelo
                );
    
                // Insertar datos en la base de datos
                $respuesta = ModeloEquipos::mdlAgregarEquipos($tabla, $datos);
    
                if ($respuesta == "ok") {
                    echo '<script>Swal.fire({
                        icon: "success",
                        title: "¡Equipo agregado correctamente!",
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#28a745"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "inventario";
                        }
                    });</script>';
                } else {
                    echo '<script>Swal.fire({
                        icon: "error",
                        title: "¡Error al agregar el equipo!",
                        confirmButtonText: "Cerrar",
                        confirmButtonColor: "#d84c4c"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "inventario";
                        }
                    });</script>';
                }
    
            } else {
                echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "¡Error, caracteres ingresados no válidos!",
                    confirmButtonText: "Cerrar",
                    confirmButtonColor: "#d84c4c"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = "inventario";
                    }
                });
                </script>';
            }
        }
    }

    public static function ctrEditarEquipos() {
        if (isset($_POST["idEditEquipo"]) && isset($_POST["etiquetaEdit"]) && isset($_POST["descripcionEdit"]) && 
            isset($_POST["categoriaEditId"]) && isset($_POST["estadoEdit"])) {
            
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["etiquetaEdit"]) &&
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["descripcionEdit"])) {
                
                $tabla = "equipos";
                $datos = array(
                    "equipo_id" => $_POST["idEditEquipo"],
                    "etiquetaEdit" => $_POST["etiquetaEdit"],
                    "descripcionEdit" => $_POST["descripcionEdit"],
                    "estadoEdit" => $_POST["estadoEdit"],
                    "categoriaEdit" => $_POST["categoriaEditId"]
                );
                $respuesta = ModeloEquipos::mdlEditarEquipos($tabla, $datos);
                
                if ($respuesta == "ok") {
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡El equipo ha sido editado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            confirmButtonColor: "#28a745"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error al editar el equipo!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            confirmButtonColor: "#d84c4c"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
                }
            } else {
                echo '<script>
                        Swal.fire({
                            icon: "error",
                            title: "¡Error, no se aceptan caracteres especiales!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            confirmButtonColor: "#d84c4c"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
            } 
            
        }
    }

    static public function ctrMostrarDatosCuentadanteOrigen($item, $valor)
    {
        $tabla = "equipos";
        $respuesta = ModeloEquipos::mdlMostrarDatosCuentadanteOrigen($tabla, $item, $valor);
        //var_dump($respuesta);
        return $respuesta;
    }

    static public function ctrMostrarDatosCuentadanteTraspaso($item, $valor)
    {
        $tabla = "usuarios";
        $respuesta = ModeloEquipos::mdlMostrarDatosCuentadanteTraspaso($tabla, $item, $valor);
        return $respuesta;
    }

    public static function ctrMostrarUbicacion($item, $valor){
        $tabla = "equipos";
        $respuesta = ModeloEquipos::mdlMostrarUbicacion($tabla, $item, $valor);
        return $respuesta;
    }

    public static function ctrMostrarUbicacionDestino($item, $valor){
        $tabla = "equipos";
        $respuesta = ModeloEquipos::mdlMostrarUbicacionDestino($tabla, $item, $valor);
        return $respuesta;
    }

    public static function ctrRealizarTraspasoUbicacion(){
        if(isset($_POST["idTraspasoUbicacion"]) && isset($_POST["nuevaUbicacionId"])){
            $tabla = "equipos";
            $datos = array(
                "equipo_id" => $_POST["idTraspasoUbicacion"],
                "ubicacion_id" => $_POST["nuevaUbicacionId"]
            );
            // mostramos el array de datos en error_log para debug
            error_log(print_r($datos, true));            

            $respuesta = ModeloEquipos::mdlRealizarTraspasoUbicacion($tabla, $datos);
            // var_dump($respuesta);
            // error_log($respuesta);
            if($respuesta == "ok"){
                echo '<script>
                        swal.fire({
                            icon: "success",
                            title: "¡Traspaso de ubicación realizado con éxito!",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#28a745"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
            } else {
                echo '<script>
                        swal.fire({
                            icon: "error",
                            title: "¡Traspaso de ubicación fallido!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            confirmButtonColor: "#d84c4c"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
            }
        }
    }

    static public function ctrRealizarTraspasoCuentadante()
    {
        if (isset($_POST["idTraspasoEquipo"]) && isset($_POST["cuentadanteDestinoId"])) {
            $tabla = "equipos";
            $datos = array(
                "equipo_id" => $_POST["idTraspasoEquipo"],
                "cuentadante_id" => $_POST["cuentadanteDestinoId"],
            );

            $respuesta = ModeloEquipos::mdlRealizarTraspasoCuentadante($tabla, $datos);


            if ($respuesta == "ok") {
                echo '<script>
                        swal.fire({
                            icon: "success",
                            title: "¡Traspaso realizado con éxito!",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#28a745"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
            } else {
                echo '<script>
                        swal.fire({
                            icon: "error",
                            title: "Algo ha fallado. No se realizaron cambios",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            confirmButtonColor: "#d84c4c"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = "inventario";
                            }
                        });
                    </script>';
            }
        }
    }
   static public function ctrImportarEquiposMasivo()
{
    ob_start();
    try {
        if (!isset($_FILES['archivoEquipos']) || $_FILES['archivoEquipos']['error'] !== UPLOAD_ERR_OK) {
            ob_end_clean();
            return json_encode([
                "status" => "error",
                "message" => "Archivo no seleccionado o con error de carga."
            ]);
        }

        $filePath = $_FILES['archivoEquipos']['tmp_name'];

        if (!file_exists($filePath)) {
            ob_end_clean();
            return json_encode([
                "status" => "error",
                "message" => "El archivo temporal no existe o fue eliminado."
            ]);
        }

        try {
            $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filePath);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();

            $equiposImportados = [];
            $equiposFallidos = [];

            
            $cuentadante_id = $_POST["cuentadante_id"] ?? null;

            if (empty($cuentadante_id)) {
                ob_end_clean();
                return json_encode(["status" => "error", "message" => "Faltan  cuentadante."]);
            }

            for ($row = 2; $row <= $highestRow; $row++) {
                $numeroSerie = trim($worksheet->getCell('A' . $row)->getValue());
                $etiqueta = trim($worksheet->getCell('B' . $row)->getValue());
                $descripcion = trim($worksheet->getCell('C' . $row)->getValue());
                $ubicacion_id = trim($worksheet->getCell('D' . $row)->getValue());
                $categoria_id = trim($worksheet->getCell('E' . $row)->getValue());
                $id_estado = trim($worksheet->getCell('F' . $row)->getValue());

                if (
                    empty($numeroSerie) &&
                    empty($etiqueta) &&
                    empty($descripcion) &&
                    empty($ubicacion_id) &&
                    empty($categoria_id) &&
                    empty($id_estado)
                    
                    
                ) {
                    break;
                }

                if (empty($numeroSerie) || empty($etiqueta)  || empty($ubicacion_id) || empty($categoria_id) || empty($id_estado)) {
                    $equiposFallidos[] = [
                        "fila" => $row,
                        "numeroSerie" => $numeroSerie,
                        "error" => "Campos obligatorios faltantes (Serie, Etiqueta, Estado, Ubicación)."
                    ];
                    continue;
                }

                $ubicacionExiste = ModeloUbicaciones::mdlMostrarUbicaciones("ubicaciones", "ubicacion_id", $ubicacion_id);
                if (!$ubicacionExiste) {
                    $equiposFallidos[] = [
                        "fila" => $row,
                        "numeroSerie" => $numeroSerie,
                        "error" => "Ubicación no encontrada (ID $ubicacion_id)"
                    ];
                    continue;
                }

                $equipoExistente = ModeloEquipos::mdlMostrarEquipos("equipos", "numero_serie", $numeroSerie);
                if ($equipoExistente) {
                    $equiposFallidos[] = [
                        "fila" => $row,
                        "numeroSerie" => $numeroSerie,
                        "error" => "Número de serie duplicado"
                    ];
                    continue;
                }

                $datos = [
                    "numero_serie" => $numeroSerie,
                    "etiqueta" => $etiqueta,
                    "descripcion" => $descripcion,
                    "id_estado" => $id_estado,
                    "categoria_id" => $categoria_id,
                    "cuentadante_id" => $cuentadante_id,
                    "ubicacion_id" => $ubicacion_id
                ];

                $respuestaModelo = ModeloEquipos::mdlImportarEquipo("equipos", $datos);

                if ($respuestaModelo == "ok") {
                    $equiposImportados[] = [
                        "fila" => $row,
                        "numeroSerie" => $numeroSerie,
                        "etiqueta" => $etiqueta
                    ];
                } else {
                    $equiposFallidos[] = [
                        "fila" => $row,
                        "numeroSerie" => $numeroSerie,
                        "error" => "Error al guardar en BD: " . $respuestaModelo
                    ];
                }
            }

            $mensaje = "Importación completada. Exitosos: " . count($equiposImportados) . ". Fallidos: " . count($equiposFallidos) . ".";
            if (count($equiposFallidos) > 0) {
                $mensaje .= " Revise los detalles de los errores.";
            }

            $contenido = "=== REPORTE DE IMPORTACIÓN DE EQUIPOS ===\n";
            $contenido .= "Fecha: " . date('Y-m-d') . "\n\n";
            $contenido .= "EQUIPOS IMPORTADOS EXITOSAMENTE (" . count($equiposImportados) . "):\n";
            foreach ($equiposImportados as $eq) {
                $contenido .= "Fila: {$eq['fila']} | Serie: {$eq['numeroSerie']} | Etiqueta: {$eq['etiqueta']}\n";
            }

            $contenido .= "\nEQUIPOS CON ERRORES (" . count($equiposFallidos) . "):\n";
            foreach ($equiposFallidos as $eq) {
                $contenido .= "Fila: {$eq['fila']} | Serie: {$eq['numeroSerie']}\nError: {$eq['error']}\n\n";
            }

            ob_end_clean();
            return json_encode([
                "status" => "success",
                "message" => $mensaje,
                "exitosos" => $equiposImportados,
                "fallidos" => $equiposFallidos,
                "reporte" => base64_encode(mb_convert_encoding($contenido, 'UTF-8')),
                "nombreArchivo" => "importacion_equipos_" . date('Y-m-d') . ".txt"
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            ob_end_clean();
            return json_encode(["status" => "error", "message" => "Error procesando archivo: " . $e->getMessage()]);
        }

    } catch (Throwable $t) {
        ob_end_clean();
        return json_encode([
            "status" => "error",
            "message" => "Error crítico: " . $t->getMessage(),
            "exitosos" => [],
            "fallidos" => [],
            "reporte" => null,
            "nombreArchivo" => null
        ]);
    }
}
}