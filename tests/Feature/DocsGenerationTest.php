<?php

namespace Tests\Feature;

use App\Exports\Reports\CuentasCobrarExport;
use App\Exports\Reports\CuentasPagarExport;
use App\Http\Controllers\Api\Common\DocGenerationController as DocGenerationController;
use App\Http\Controllers\Api\Common\ExportsController as ExportsController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;


class DocsGenerationTest extends TestCase
{
    public function test_proveedor_cotizacion_pdf(){
        $mockMpdf = $this->createMock(Mpdf::class);
        $mockMpdf->method('WriteHTML');
        $mockMpdf->expects($this->once())->method('Output')->with($this->anything(), 'I');

        $controller = new DocGenerationController();
        $controller->generarProveedorCotizacionPDF(1, $mockMpdf);
    }

    public function test_orden_compra_pdf(){
        $mockMpdf = $this->createMock(Mpdf::class);
        $mockMpdf->method('WriteHTML');
        $mockMpdf->expects($this->once())->method('Output')->with($this->anything(), 'I');

        $controller = new DocGenerationController();
        $controller->generarOrdenCompraPDF(1, $mockMpdf);
    }

    public function test_compra_pdf(){
        $mockMpdf = $this->createMock(Mpdf::class);
        $mockMpdf->method('WriteHTML');
        $mockMpdf->expects($this->once())->method('Output')->with($this->anything(), 'I');

        $controller = new DocGenerationController();
        $controller->generarCompraPDF(1, $mockMpdf);
    }

    public function test_caja_pdf(){
        $user = User::find(1);
        $this->actingAs($user);
        
        $mockMpdf = $this->createMock(Mpdf::class);
        $mockMpdf->method('WriteHTML');
        $mockMpdf->expects($this->once())->method('Output')->with($this->anything(), 'I');

        $controller = new DocGenerationController();
        $controller->generarCajaPDF(1, $mockMpdf);
    }

    public function test_kardex_export(){
        $user = User::find(1);
        $this->actingAs($user);

        $mockWriter = $this->createMock(Xlsx::class);
        $mockWriter->expects($this->once())
            ->method('save')
            ->with('php://output');

        $params = '{"fechaInicio":"2024-01-01","fechaFin":"2024-03-03","searchTerm":""}';
        $controller = new ExportsController();
        $controller->exportarKardexValorizado($params, $mockWriter);
    }

    public function test_reporte_almacen_export(){
        $user = User::find(1);
        $this->actingAs($user);

        $mockWriter = $this->createMock(Xlsx::class);
        $mockWriter->expects($this->once())
            ->method('save')
            ->with('php://output');

        $params = '{"fecha_inicio":"2024-01-01","fecha_fin":"2024-03-03","search_query":""}';
        $controller = new ExportsController();
        $controller->exportarReporteAlmacen($params, $mockWriter);
    }

    public function test_reporte_compra_formato_export(){
        $user = User::find(1);
        $this->actingAs($user);

        $mockWriter = $this->createMock(Xlsx::class);
        $mockWriter->expects($this->once())
            ->method('save')
            ->with('php://output');

        $params = '{"fechaInicio":"2024-01-01","fechaFin":"2024-03-03","searchTerm":""}';
        $controller = new ExportsController();
        $controller->exportarReporteCompraFormat($params, $mockWriter);
    }

    public function test_reporte_comprobante_general_export(){
        $user = User::find(1);
        $this->actingAs($user);

        $mockWriter = $this->createMock(Xlsx::class);
        $mockWriter->expects($this->once())
            ->method('save')
            ->with('php://output');

        $params = '{"fechaInicio":"2024-01-01","fechaFin":"2024-03-03","searchTerm":"", "id_tipo_comprobante":"", "id_estado":""}';
        $controller = new ExportsController();
        $controller->exportarReporteComprobanteGeneral($params, $mockWriter);
    }

    public function test_cuentas_cobrar_export(){
        Excel::shouldReceive('download')
            ->once()
            ->with(Mockery::type(CuentasCobrarExport::class), Mockery::any())
            ->andReturn('success');

        $params = '{"fechaInicio":"2024-01-01","fechaFin":"2024-03-03","searchTerm":""}';
        $controller = new ExportsController();
        $controller->exportarCuentasCobrar($params, app(Excel::class));
    }

    public function test_cuentas_pagar_export(){
        Excel::shouldReceive('download')
            ->once()
            ->with(Mockery::type(CuentasPagarExport::class), Mockery::any())
            ->andReturn('success');

        $params = '{"fechaInicio":"2024-01-01","fechaFin":"2024-03-03","searchTerm":""}';
        $controller = new ExportsController();
        $controller->exportarCuentasPagar($params, app(Excel::class));
    }
}
