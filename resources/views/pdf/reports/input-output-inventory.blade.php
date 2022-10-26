@extends('pdf.layout.app')
@section('content')
    <table class="content-table">
        <thead>
        <tr>
            <th>Clave</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Costo</th>
            <th>Fecha movimiento</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{$item->code}}</td>
                <td>{{$item->name}}</td>
                <td>{{$item->type}}</td>
                <td>{{$item->sum_quantity}}</td>
                <td>{{$item->sum_cost ? '$'.number_format($item->sum_cost) : 'No aplica'}}</td>
                <td>{{$item->move_date->format('d-m-Y')}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
