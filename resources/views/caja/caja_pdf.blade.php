@php
    $empresa = $sucursal->empresa;
    $fecha_reporte = date("d-m-Y");
    $estado_caja = $document->fecha_cierre == null ? 'Abierta' : 'Cerrada';
    $vendedor = $document->usuario;
@endphp
<html>
<head>
    <link href="{{ asset('css/comprobante_pdf.css') }}" rel="stylesheet">
</head>
<body>
    <table class="full-width">
        <tr>
            <td width="70%" class="pl-3">
                <div class="company_logo_box">
                    <img src="{{url('..'.$empresa->file_path->path . $empresa->file_path->filename)}}" class="company_logo" style="max-width: 250px;">
                </div>
            </td>
        </tr>
    </table>
    <table class="full-width mt-5">
        <tr>
            <td width="20%" style="height:40px"><b>Empresa:</b> {{$empresa->nombre}}</td>
            <td width="15%"><b>Fecha Reporte:</b> {{$fecha_reporte}}</td>
        </tr>
        <tr>
            <td width="15%" style="height:40px"><b>Ruc:</b> {{$empresa->ruc}}</td>
            <td width="15%"><b>Establecimiento:</b> {{$sucursal->nombre_sucursal}}</td>
        </tr>
        <tr>
            <td width="20%" style="height:40px"><b>Vendedor:</b> {{$vendedor->name}}</td>
            <td width="15%"><b>Fecha y hora de apertura:</b> {{$document->fecha_apertura}}</td>
        </tr>
        <tr>
            <td width="20%" style="height:40px"><b>Estado de caja:</b> {{$estado_caja}}</td>
            <td width="15%"><b>Fecha y hora de cierre:</b> {{$document->fecha_cierre}}</td>
        </tr>
        <tr>
            <td width="20%" style="height:50px"><b>Montos de Operación:</b></td>
        </tr>
        <tr>
            <td width="20%"style="height:50px"><b>Saldo Inicial:</b> {{$document->monto_apertura}}</td>
            <td width="15%"><b>Ingreso:</b> {{$sum_sales}}</td>
        </tr>
        <tr>
            <td width="20%" style="height:50px"><b>Saldo Final:</b> {{$document->monto_cierre}}</td>
            <td width="15%"><b>Egreso:</b> {{$sum_purchases}}</td>
        </tr>
    </table>
    <table class="full-width mt-10 mb-10">
        <thead>
            <tr class="bg-grey">
                <th class="border-top-bottom text-center py-2 txt-white">Descripción</th>
                <th class="border-top-bottom text-center py-2 txt-white">Suma</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document_detail as $key=>$row)
            <tr>
                <td class="text-center">{{$row->medio_pago}}</td>
                <td class="text-center">{{$row->monto}}</td>
            </tr>
            <tr>
                <td colspan="2" class="border-bottom"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table class="full-width mt-10 mb-10">
        <thead class="">
            <tr class="bg-grey">
                <th class="border-top-bottom text-center py-2 txt-white">#</th>
                <th class="border-top-bottom text-center py-2 txt-white">Tipo Transacción</th>
                <th class="border-top-bottom text-center py-2 txt-white">Tipo de Documento</th>
                <th class="border-top-bottom text-center py-2 txt-white">Documento</th>
                <th class="border-top-bottom text-center py-2 txt-white" width="10%">Fecha Emisión</th>
                <th class="border-top-bottom text-center py-2 txt-white">Cliente / Proveedor</th>
                <th class="border-top-bottom text-center py-2 txt-white">N° Documento</th>
                <th class="border-top-bottom text-center py-2 txt-white">Moneda</th>
                <th class="border-top-bottom text-center py-2 txt-white">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($document_sales as $key=>$row)
            <tr>
                <td class="text-center align-top">{{ ++$key }}</td>
                <td class="text-center align-top">Venta</td>
                <td class="text-center align-top">{{ $row->cliente->tipo_doc->tipo_documento}}</td>
                <td class="text-center align-top">{{ $row->tipo_comprobante->tipo_comprobante }}</td>
                <td class="text-center align-top">{{ $row->fecha_emision }}</td>
                <td class="text-center align-top">{{ $row->nombreCliente }}</td>
                <td class="text-center align-top">{{ $row->serie->serie}}-{{$row->correlativo}}</td>
                <td class="text-center align-top">{{ $row->moneda->moneda }}</td>
                <td class="text-center align-top">{{ $row->total }}</td>
            </tr>
            <tr>
                <td colspan="9" class="border-bottom"></td>
            </tr>
            @endforeach
            @foreach($document_purchases as $key=>$row)
            <tr>
                <td class="text-center align-top">{{ ++$key }}</td>
                <td class="text-center align-top">Compra</td>
                <td class="text-center align-top">{{ $row->proveedor->tipo_doc->tipo_documento}}</td>
                <td class="text-center align-top"></td>
                <td class="text-center align-top">{{ $row->fecha_emision }}</td>
                <td class="text-center align-top">{{ $row->proveedor->nombre }}</td>
                <td class="text-center align-top">{{ $row->nro_factura }}</td>
                <td class="text-center align-top">{{ $row->moneda->moneda }}</td>
                <td class="text-center align-top">{{ $row->total }}</td>
            </tr>
            <tr>
                <td colspan="9" class="border-bottom"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>