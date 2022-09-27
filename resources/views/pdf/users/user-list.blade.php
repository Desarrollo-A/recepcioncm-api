@extends('pdf.layout.app')
@section('content')
    <table class="content-table">
        <thead>
        <tr>
            <th>N° colaborador</th>
            <th>Nombre completo</th>
            <th>Correo electrónico</th>
            <th>Teléfono personal</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{$user->no_employee}}</td>
                <td>{{$user->full_name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->personal_phone}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
