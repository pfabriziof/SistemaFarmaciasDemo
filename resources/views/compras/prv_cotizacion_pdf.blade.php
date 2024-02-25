@php
    $proveedor = $document->proveedor;
    $document_number = 'N°'.str_pad($document->numeracion, 8, '0', STR_PAD_LEFT);
@endphp
<html>
<head>
    <title>{{ $document_number }}</title>
    <link href="{{ asset('css/comprobante_pdf.css') }}" rel="stylesheet">
</head>
<body>
<table class="full-width">
    <tr>
        <td width="70%" class="pl-3">
            <div class="company_logo_box">
                @if ($proveedor->sucursal->empresa->file_path)
                    <img src="{{url('..'.$proveedor->sucursal->empresa->file_path->path . $proveedor->sucursal->empresa->file_path->filename)}}" class="company_logo" style="max-width: 250px;">
                @endif
            </div>
        </td>
        <td width="30%" class="border-box py-3 px-2 text-center">
            <h5 class="text-center">Cotización</h5>
            <h3 class="text-center">{{ $document_number }}</h3>
        </td>
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td width="15%">Cliente:</td>
        <td width="45%">{{ $proveedor->nombre }}</td>
        <td width="25%">Fecha de emisión:</td>
        <td width="15%">{{ $document->fecha_emision}}</td>
    </tr>
    <tr>
        <td>{{ $proveedor->tipo_doc->tipo_documento }}:</td>
        <td>{{ $proveedor->nro_doc }}</td>
    </tr>
    @if ($proveedor->direccion !== '')
        <tr>
            <td class="align-top">Dirección:</td>
            <td colspan="3">{{ $proveedor->direccion }}</td>
        </tr>
    @endif
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
            <th class="border-top-bottom text-center py-2 txt-white" style="width:20%;">CANT.</th>
            <th class="border-top-bottom text-center py-2 txt-white" style="width:25%;">UNIDAD</th>
            <th class="border-top-bottom text-left py-2 txt-white" style="width:55%;">DESCRIPCIÓN</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document_detail_pr as $row)
            <tr>
                <td class="text-center align-top">{{ $row->cantidad }}</td>
                <td class="text-center align-top">{{ $row->und_simbolo }}</td>
                <td class="text-left">{!! $row->producto->nombreProducto !!}</td>
            </tr>
            <tr>
                <td colspan="6" class="border-bottom"></td>
            </tr>
        @endforeach
    </tbody>
    @endif
    <!-- FIN -->
    <!-- TABLA DE SERVICIOS -->
    @if (!$document_detail_sv->isEmpty())
    <thead class="">
        <tr class="bg-grey">
            <th colspan="7" class="border-top-bottom text-center py-2 txt-white">SERVICIOS</th>
        </tr>
        <tr class="bg-grey">
            <th class="border-top-bottom text-center py-2 txt-white">CANT.</th>
            <th class="border-top-bottom text-center py-2 txt-white">UNIDAD</th>
            <th class="border-top-bottom text-left py-2 txt-white">DESCRIPCIÓN</th>
            <th class="border-top-bottom text-right py-2 txt-white">P.UNIT</th>
            <th class="border-top-bottom text-right py-2 txt-white">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($document_detail_sv as $row)
            <tr>
                <td class="text-center align-top">{{ $row->cantidad }}</td>
                <td class="text-center align-top">
                    {{ $row->und_simbolo }}
                </td>
                <td class="text-left">
                    {!! $row->producto->nombreProducto !!}
                </td>
                <td class="text-right align-top">{{ number_format($row->precioUnitario, 2) }}</td>
                <td class="text-right align-top">{{ number_format($row->precioTotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="6" class="border-bottom"></td>
            </tr>
        @endforeach
    </tbody>
    @endif
    <!-- FIN -->   
</table>
<table class="full-width">
    <tr>
        <td width="65%">
            @if ($document->comentario)
                <strong>Información adicional</strong>
                <p>{{ $document->comentario }}</p>
            @endif
        </td>
    </tr>
</table>
</body>
</html>