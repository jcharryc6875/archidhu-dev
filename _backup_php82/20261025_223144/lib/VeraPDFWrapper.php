<?php
// Excepción personalizada para errores de VeraPDF
class VeraPDFException extends Exception {}

use AdamBrett\ShellWrapper\Command;
use AdamBrett\ShellWrapper\Command\Param;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\Runners\ShellExec;
use AdamBrett\ShellWrapper\Runners\System;

class VeraPDFWrapper
{
    private $verapdf_path;
    private $verapdf_format;

    // Constructor: recibe la ruta al ejecutable de VeraPDF
    public function __construct($verapdf_path = 'verapdf', $verapdf_format = 'json')
    {
        $this->verapdf_path = $verapdf_path;
        $this->verapdf_format = $verapdf_format;
    }

    // Método para validar un archivo PDF/A
    public function validatePDFA($file_path)
    {
        $response = [
            'success' => false,
            'message' => '',
            'pdf_type' => '',
            'pdfa_valid' => '',
            'data' => [],
        ];

        try {
            // Verificar si el archivo existe
            if (!file_exists($file_path)) {
                throw new VeraPDFException("El archivo PDF no existe: $file_path");
            }

            // Verificar si VeraPDF está instalado
            $this->checkVeraPDFInstalled();

            // Comando para validar el PDF
            $command = "{$this->verapdf_path} --format {$this->verapdf_format} \"{$file_path}\"";

            // Ejecutar el comando
            if($this->verapdf_format == 'json'){
                $output_text = shell_exec($command);

                // Verificar si el comando falló
                if ($output_text === null) {
                    throw new VeraPDFException("Error al ejecutar VeraPDF.");
                }

                // Parsear la salida
                $result = json_decode($output_text);
                $validation_profile = $result->report->jobs[0]->validationResult->profileName;
                $status_compliant = $result->report->jobs[0]->validationResult->compliant;
                // Construir la respuesta JSON
                $response['success'] = true;
                $response['pdfa_valid'] = (empty($validation_profile) !== false && $status_compliant !== false);
                $response['message'] = sprintf('Validación completada correctamente, %s.',$validation_profile);
                $response['pdf_type'] = !empty($validation_profile) ? trim(str_replace('validation profile','',$validation_profile)) : null;
                $response['data'] = $result;

                // Devolver la respuesta en formato JSON
                return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }else{
                exec($command, $output, $return_code);
                //Unir la salida en una sola cadena
                $output_text = implode("\n", $output);

                // Verificar si el comando falló
                if ($return_code !== 0 && $return_code !== 1) {
                    throw new VeraPDFException("Error al ejecutar VeraPDF. Código de salida: $return_code");
                }

                // Parsear la salida
                $result = $this->parseOutput($output_text);

                // Agregar el estado de validación
                $response['success'] = ($return_code === 1);
                $response['pdfa_valid'] = (strpos($output_text, 'Validation Profile:') !== false && strpos($output_text, 'Status: Compliant') !== false);
                $response['message'] = 'Validación realizada';
                $response['pdf_type'] = null;
                $response['data'] = $result;

                // Devolver la respuesta en formato JSON
                return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            }

        } catch (\Exception $e) {
            // Manejar errores
            $response['success'] = false;
            $response['message'] = $e->getMessage();
            $response['data'] = array();
            return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
    }    

    // Método para verificar si VeraPDF está instalado
    private function checkVeraPDFInstalledWrapperCmd()
    {
        $shell = new ShellExec();
        $command = new Command("{$this->verapdf_path} --version");
        $response_output = $shell->run($command);

        $output = passthru("{$this->verapdf_path} --version",$output);
        if ($response_output === null) {
            throw new Exception("VeraPDF no está instalado o no es accesible.");
        }
    }

    private function checkVeraPDFInstalled()
    {
        exec("{$this->verapdf_path} --version", $output, $return_code);
        if ($return_code !== 0) {
            throw new VeraPDFException("VeraPDF no está instalado o no es accesible.");
        }
    }

    // Método para parsear la salida de VeraPDF
    private function parseOutput($output_text)
    {
        $result = [
            'profile' => '',
            'status' => '',
            'details' => [],
            'summary' => [],
        ];

        // Extraer el perfil de validación (PDF/A-1B, PDF/A-2U, etc.)
        if (preg_match('/Validation Profile:\s*(PDF\/A-\w+)/', $output_text, $matches)) {
            $result['profile'] = $matches[1];
        }

        // Extraer el estado de validación (Compliant/Non-compliant)
        if (preg_match('/Status:\s*(\w+)/', $output_text, $matches)) {
            $result['status'] = $matches[1];
        }

        // Extraer detalles de los errores
        if (preg_match_all('/-\s*Error:\s*(.+)/', $output_text, $matches)) {
            $result['details'] = $matches[1];
        }

        // Extraer el resumen (Total rules checked, Passed, Failed)
        if (preg_match('/Total rules checked:\s*(\d+)/', $output_text, $matches)) {
            $result['summary']['total_rules'] = (int)$matches[1];
        }
        if (preg_match('/Passed:\s*(\d+)/', $output_text, $matches)) {
            $result['summary']['passed'] = (int)$matches[1];
        }
        if (preg_match('/Failed:\s*(\d+)/', $output_text, $matches)) {
            $result['summary']['failed'] = (int)$matches[1];
        }

        return $result;
    }
}
?>