<?php

class PdfTkWrapper {
    private $path;

    public function __construct($path = '/usr/bin/pdftk') {
        $this->path = $path;  // Ruta a pdftk, ajusta según sea necesario
    }

    // Método privado para ejecutar comandos pdftk
    private function executeCommand($command) {
        // Ejecutar el comando
        $output = shell_exec($command);
        
        // Verificar si se produjo algún error
        if ($output === null) {
            // Si no hay salida, asumimos que algo falló
            throw new Exception("Error al ejecutar el comando: $command");
        }

        // Comprobamos si hay errores en la salida
        if (strpos($output, 'Error') !== false) {
            throw new Exception("Error en la ejecución del comando: $command. Detalles: $output");
        }

        return $output;
    }

    // Método para fusionar archivos PDF
    public function merge($inputFiles, $outputFile) {
        // Verificar que los archivos de entrada existen
        foreach ($inputFiles as $file) {
            if (!file_exists($file)) {
                throw new Exception("El archivo de entrada no existe: $file");
            }
        }

        // Crear el comando pdftk para fusionar archivos
        $input = implode(' ', $inputFiles);
        $command = "{$this->path} $input cat output $outputFile";

        try {
            $output = $this->executeCommand($command);
            return $output;
        } catch (Exception $e) {
            // Capturar el error y devolverlo
            return "Error al fusionar PDFs: " . $e->getMessage();
        }
    }

    // Método para extraer campos de formulario de un PDF y retornarlos en formato JSON
    public function extractFields($inputFile) {
        // Verificar que el archivo de entrada existe
        if (!file_exists($inputFile)) {
            throw new Exception("El archivo PDF no existe: $inputFile");
        }

        // Crear el comando pdftk para extraer los campos
        $command = "{$this->path} $inputFile dump_data_fields";

        try {
            $output = $this->executeCommand($command);

            // Procesar la salida y convertirla a un formato adecuado para JSON
            $fields = [];
            $lines = explode("\n", $output);

            // Extraer campos
            $field = [];
            foreach ($lines as $line) {
                $line = trim($line);
                
                if (empty($line)) {
                    continue;
                }

                // Detectar el comienzo de un campo
                if (strpos($line, "FieldName") !== false) {
                    // Si ya tenemos un campo, guardamos el anterior
                    if (!empty($field)) {
                        $fields[] = $field;
                    }
                    $field = []; // Reiniciar el campo
                    $field['FieldName'] = trim(str_replace("FieldName: ", "", $line));
                }

                // Capturar otros valores relacionados con el campo
                if (strpos($line, "FieldType") !== false) {
                    $field['FieldType'] = trim(str_replace("FieldType: ", "", $line));
                }
                if (strpos($line, "FieldValue") !== false) {
                    $field['FieldValue'] = trim(str_replace("FieldValue: ", "", $line));
                }
            }

            // Asegurarse de agregar el último campo procesado
            if (!empty($field)) {
                $fields[] = $field;
            }

            // Convertir el resultado a JSON
            return json_encode($fields, JSON_PRETTY_PRINT);

        } catch (Exception $e) {
            // Capturar el error y devolverlo
            return json_encode(["error" => "Error al extraer los campos: " . $e->getMessage()]);
        }
    }

    // Método para verificar si el PDF está firmado digitalmente
    public function isDigitallySigned($inputFile) {
        if (!file_exists($inputFile)) {
            return json_encode(["error" => true, "signed" =>null, "message" => "El archivo PDF no existe: $inputFile"]);
            //throw new Exception("El archivo PDF no existe: $inputFile");
        }

        $command = "{$this->path} \"{$inputFile}\" dump_data";
        try {
            $output = $this->executeCommand($command);

            // Buscar indicadores de firma digital
            if (strpos($output, 'Signature') !== false || strpos($output, 'SigFlags') !== false) {
                return json_encode(["error" => false, "signed" => true, "message" => "El documento está firmado digitalmente."], JSON_PRETTY_PRINT);
            } else {
                return json_encode(["error" => false, "signed" => false, "message" => "El documento NO está firmado digitalmente."], JSON_PRETTY_PRINT);
            }

        } catch (Exception $e) {
            return json_encode(["error" => true, "signed" =>null, "message" => "Error al verificar la firma digital: " . $e->getMessage()]);
        }
    }

    // Método para dividir un PDF
    public function split($inputFile, $outputDir) {
        // Verificar que el archivo de entrada existe
        if (!file_exists($inputFile)) {
            throw new Exception("El archivo PDF no existe: $inputFile");
        }

        // Verificar que el directorio de salida existe
        if (!is_dir($outputDir)) {
            throw new Exception("El directorio de salida no existe: $outputDir");
        }

        // Crear el comando pdftk para dividir el PDF
        $command = "{$this->path} $inputFile burst output $outputDir/pg_%02d.pdf";

        try {
            $output = $this->executeCommand($command);
            return $output;
        } catch (Exception $e) {
            // Capturar el error y devolverlo
            return "Error al dividir el PDF: " . $e->getMessage();
        }
    }
}