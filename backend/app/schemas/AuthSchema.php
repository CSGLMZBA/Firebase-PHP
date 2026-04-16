<?php
class AuthSchema
{
    public static function validateLogin(array $data): array
    {
        $usuario = trim($data['usuario'] ?? '');
        $password = trim($data['password'] ?? '');

        if ($usuario === '')
        {
            return [
                'ok' => false,
                'message' => 'El usuario es obiligatorio'
            ];
        }
        if($password === '')
        {
            return [
                'ok' => false,
                'message' => 'El password es obiligatorio'
            ];
        }
        return [
            'ok' => true
        ];

    }
}
?>