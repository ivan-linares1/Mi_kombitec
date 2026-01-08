<?php
namespace App\Http\Controllers;

use App\Models\Clientes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Archivos_Sincronizasores extends Controller
{
    public function ServicioWeb(Request $request, $servicio)
    {
        $ruta = $request->input('ruta');

        // Validación simple
        if (!$ruta) {
            return redirect()->back()->with('error', 'No se proporcionó la ruta del archivo XML.');
        }

        switch ($servicio) {
            case 'Clientes':
                return $this->Clientes($ruta);
            default:
                return redirect()->back()->with('error', 'Servicio no encontrado.');
        }
    }

    private function Clientes($rutaXml)
    {
        if (!file_exists($rutaXml)) {
            return redirect()->back()->with('error', 'Archivo XML no encontrado');
        }

        $xml = simplexml_load_file($rutaXml);

        if (!$xml || !isset($xml->Cliente)) {
            return redirect()->back()->with('warning', 'XML sin clientes');
        }

        if (!is_array($xml->Cliente) && !($xml->Cliente instanceof \Traversable)) {
            $xml->Cliente = [$xml->Cliente];
        }

        $total = count($xml->Cliente);
        $insertados = 0;
        $errores = 0;

        foreach ($xml->Cliente as $cliente) {
            try {
                $registro = Clientes::updateOrInsert(
                    ['CardCode' => (string) $cliente->CardCode],
                    [
                        'CardName' => (string) $cliente->CardName,
                        'GroupNum' => (int) $cliente->GroupNum,
                        'phone1'   => (string) $cliente->Phone1,
                        'e-mail'    => (string) $cliente->Email,
                        'Active'   => (string) $cliente->ValidFor,
                    ]
                );

                if ($registro) {
                    $insertados++;
                }
            } catch (\Throwable $e) {
                $errores++;
                Log::channel('sync')->error(
                    "Clientes XML ({$rutaXml}) - {$cliente->CardCode} => " . $e->getMessage()
                );
            }
        }

        if ($errores > 0) {
            return redirect()->back()->with('warning', "Proceso terminado con errores. Insertados: $insertados, Errores: $errores");
        }

        return redirect()->back()->with('success', "Proceso completado correctamente. Total: $total, Insertados: $insertados");
    }

}
