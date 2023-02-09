@extends('pdf.layout.app')
@section('content')
    <div class="title-report">
        <h3>Reporte Entregados Paquetería</h3>
    </div>
    <table class="content-table">
        <thead>
        <tr>
            <th>Clave</th>
            <th>Fecha entrega</th>
            <th>Lugar salida</th>
            <th>Lugar llegada</th>
            <th>Recibió</th>
            <th>Firma electrónica</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{$item->code}}</td>
                <td>{{$item->end_date->format('d-m-Y, h:i a')}}</td>
                <td>{{$item->state_pickup}}</td>
                <td>{{$item->state_arrival}}</td>
                <td>{{$item->name_receive}}</td>
                <td>
                    <img src= {{asset($path.$item->signature)}}  style="width: 150px;">
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
