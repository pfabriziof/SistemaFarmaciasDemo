@php
    $document_number = 'N°'.str_pad($document->correlativo, 8, '0', STR_PAD_LEFT);
    $cliente = $document->cliente;
@endphp
<html>
<head>
    <title>Comprobante {{ $document_number }}</title>
    <link href="{{ asset('css/comprobante_pdf.css') }}" rel="stylesheet">
</head>
<body>
<table class="full-width">
    <tr>
        <td width="70%" class="pl-3">
            <div class="company_logo_box">
                @if ($document->sucursal->empresa->file_path)
                    <img src="{{url('..'.$document->sucursal->empresa->file_path->path .$document->sucursal->empresa->file_path->filename)}}" class="company_logo" style="max-width: 300px;">
                @else
                    <img src="{{asset("assets/images/logo.png")}}" class="company_logo" style="max-width: 300px;">
                @endif
            </div>
        </td>
        <td width="30%" class="border-box py-3 px-2 text-center">
            <h5 class="text-center">Comprobante</h5>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td width="15%">Cliente:</td>
        <td width="45%">{{ $document->nombreCliente }}</td>
        <td width="25%">Fecha de emisión:</td>
        <td width="15%">{{ $document->fecha_emision}}</td>
    </tr>
    <tr>
        <td>{{ $cliente->tipo_doc->tipo_documento }}:</td>
        <td>{{ $document->nroDocCliente }}</td>
    </tr>
    <tr>
        <td class="align-top">Dirección:</td>
        <td colspan="3">{{ $document->direccionCliente }}</td>
    </tr>
</table>

<table class="full-width mt-10 mb-10">
    <!--TABLA DE PRODUCTOS-->
    @if (!$document_detail_pr->isEmpty())
    <thead class="">
        <tr class="bg-grey">
            <th colspan="7" class="border-top-bottom text-center py-2 txt-white" >PRODUCTOS</th>
        </tr>
    </thead>
    <thead class="">
        <tr class="bg-grey">
            <th class="border-top-bottom text-left py-2 txt-white">DESCRIPCIÓN</th>
            <th class="border-top-bottom text-center py-2 txt-white">UNIDAD</th>
            <th class="border-top-bottom text-left py-2 txt-white">LOTE</th>
            <th class="border-top-bottom text-left py-2 txt-white">LISTA PRECIO</th>
            <th class="border-top-bottom text-right py-2 txt-white">P.UNIT</th>
            <th class="border-top-bottom text-right py-2 txt-white">CANT.</th>
            <th class="border-top-bottom text-right py-2 txt-white">P.TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document_detail_pr as $row)
            <tr>
                <td class="text-center align-top">{{ $row->nombreProducto }}</td>
                <td class="text-center align-top">{{ $row->und_simbolo }}</td>
                <td class="text-center align-top">{{ $row->lote->lote }}</td>
                <td class="text-center align-top">
                    {{ $row->lista_detalle->listaprecio->nombre }} ({{$row->lista_detalle->unidades}}) {{$row->lista_detalle->precio_venta}}
                </td>
                <td class="text-right align-top">{{ number_format($row->precio_unitario, 2) }}</td>
                <td class="text-center align-top">{{ $row->cantidad_visual }}</td>
                <td class="text-right align-top">{{ number_format($row->precio_total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="7" class="border-bottom"></td>
            </tr>
        @endforeach
    </tbody>
    @endif
    <!-- FIN -->
    <!-- TOTALES  -->
    <tbody>
        @if ($document->op_inafectas > 0)
        <tr>
            <td colspan="6" class="text-right font-bold">OP. INAFECTAS: {{ $document->moneda->simbolo }} </td>
            <td class="text-right font-bold">{{ number_format($document->op_inafectas, 2) }}</td>
        </tr>
        @endif
        @if ($document->op_exoneradas > 0)
        <tr>
            <td colspan="6" class="text-right font-bold">OP. EXONERADAS: {{ $document->moneda->simbolo }} </td>
            <td class="text-right font-bold">{{ number_format($document->op_exoneradas, 2) }}</td>
        </tr>
        @endif
        @if ($document->op_gravadas > 0)
        <tr>
            <td colspan="6" class="text-right font-bold">OP. GRAVADAS: {{ $document->moneda->simbolo }} </td>
            <td class="text-right font-bold">{{ number_format($document->op_gravadas, 2) }}</td>
        </tr>
        @endif
        @if ($document->icbper > 0)
        <tr>
            <td colspan="6" class="text-right font-bold">ICBPER: {{ $document->moneda->simbolo }} </td>
            <td class="text-right font-bold">{{ number_format($document->icbper, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td colspan="6" class="text-right font-bold">IGV: {{ $document->moneda->simbolo }}</td>
            <td class="text-right font-bold">{{ number_format($document->igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="text-right font-bold">TOTAL A PAGAR: {{ $document->moneda->simbolo }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
    <!-- FIN  -->
</table>
</body>
</html>