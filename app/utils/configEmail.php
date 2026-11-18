<?php
require_once __DIR__ . '/configLocal.php';
return [
    'host' => almaConfiguracao('SMTP_HOST'),
    'porta' => (int) almaConfiguracao('SMTP_PORT', '587'),
    'usuario' => almaConfiguracao('SMTP_USER'),
    'senha' => almaConfiguracao('SMTP_PASS'),
    'remetente' => almaConfiguracao('SMTP_FROM'),
    'nome_remetente' => almaConfiguracao('SMTP_FROM_NAME', 'Alma Pantaneira'),
];
