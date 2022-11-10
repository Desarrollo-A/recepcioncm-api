<?php

namespace App\Helpers\Enum;

class Message
{
    // Mensajes de validaciones
    const CREDENTIALS_INVALID = 'Credenciales inválidas.';
    const PASSWORD_INVALID = 'La contraseña actual no es la registrada en el sistema.';
    const USER_INACTIVE = 'El usuario está inactivo.';
    const USER_BLOCKED = 'El usuario está bloqueado.';
    const INVALID_QUERY_PARAMETER = 'Parámetro de consulta inválido.';
    const INVALID_ID_PARAMETER_WITH_ID_BODY = 'El id es diferente al id del parámetro de ruta.';
    const LIMIT_REQUEST_BY_DAY = 'Se ha excedido el límite para agendar reuniones para los días ';

    // Mensajes de excepciones
    const AUTHENTICATION_EXCEPTION = 'No autenticado.';
    const MODEL_NOT_FOUND_EXCEPTION = 'No existe el registro.';
    const AUTHORIZATION_EXCEPTION = 'No tiene permisos para ejecutar esta acción.';
    const NOT_FOUND_HTTP_EXCEPTION = 'No se encontró la URL especificada.';
    const METHOD_NOT_ALLOWED_HTTP_EXCEPTION = 'Método no válido.';
    const QUERY_EXCEPTION_1451 = 'No se puede eliminar el registro porque está relacionado con algún otro.';
    const INTERNAL_SERVER_ERROR = 'Ocurrió algo inesperado. Consulte al administrador.';
    const THROTTLE_REQUESTS_EXCEPTION = 'Muchos intentos realizados.';

    // Mensajes de asunto de correo electrónico
    const RESTORE_PASSWORD = 'Nueva contraseña';

    public static function getMessageHasNotAllowedSorts(string $class): string
    {
        return "Establezca la propiedad pública allowedSorts dentro de $class";
    }

    public static function getMessageNotFieldClass(string $field, string $class): string
    {
        return "No existe la propiedad ${field} en ${class}";
    }
}