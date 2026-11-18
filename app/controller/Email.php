<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

class Email
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../utils/configEmail.php';
    }

    /**
     * Cria o PHPMailer já configurado com o SMTP.
     */
    private function criarMailer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPAuth   = true;
        $mail->Host       = $this->config['host'];
        $mail->Port       = $this->config['porta'];
        $mail->Username   = $this->config['usuario'];
        $mail->Password   = $this->config['senha'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->setFrom($this->config['remetente'], $this->config['nome_remetente']);

        return $mail;
    }

    /**
     * Envia o código de recuperação de senha.
     * Retorna true se enviou, false se deu erro.
     */
    public function enviarCodigoRecuperacao($email, $codigo, $minutosValidade = 15)
    {
        try {
            $mail = $this->criarMailer();
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Código de recuperação de senha - Alma Pantaneira';
            $mail->Body    = '
                <div style="font-family: Arial, Helvetica, sans-serif; max-width: 480px; margin: 0 auto; padding: 24px; background: #fdf9f9; border-radius: 12px; text-align: center; color: #333;">
                    <h2 style="color: green; margin: 0 0 8px;">Alma Pantaneira</h2>
                    <p style="margin: 0 0 16px;">Recebemos uma solicitação para redefinir a sua senha.</p>
                    <p style="margin: 0 0 8px;">Seu código de recuperação é:</p>
                    <div style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #fff; background: #2f6b3e; border-radius: 12px; padding: 12px 0; margin: 0 0 16px;">'
                        . htmlspecialchars($codigo) .
                    '</div>
                    <p style="font-size: 13px; color: #666; margin: 0 0 4px;">O código é válido por ' . (int) $minutosValidade . ' minutos.</p>
                    <p style="font-size: 13px; color: #666; margin: 0;">Se você não solicitou a troca de senha, ignore este e-mail. Sua senha continuará a mesma.</p>
                </div>';
            $mail->AltBody = "Seu código de recuperação de senha é: {$codigo}. "
                           . "Ele é válido por {$minutosValidade} minutos. "
                           . "Se você não solicitou, ignore este e-mail.";

            return $mail->send();
        } catch (Exception $e) {
            error_log('Erro ao enviar e-mail de recuperação: ' . $e->getMessage());
            return false;
        }
    }


    public function enviarSenhaPrimeiroAcesso($email, $senha)
    {
        try {
            $mail = $this->criarMailer();

            $mail->addAddress($email);
            $mail->isHTML(true);

            $mail->Subject = 'Senha de primeiro acesso - Alma Pantaneira';

            $senhaHtml = htmlspecialchars($senha, ENT_QUOTES, 'UTF-8');

            $mail->Body = '
                <div style="font-family: Arial, Helvetica, sans-serif;
                            max-width: 480px; margin: 0 auto; padding: 24px;
                            background: #fdf9f9; border-radius: 12px;
                            text-align: center; color: #333;">

                    <h2 style="color: green; margin: 0 0 16px;">
                        Alma Pantaneira
                    </h2>

                    <p>Seu cadastro foi realizado com sucesso!</p>

                    <p>Utilize a senha temporária abaixo para seu primeiro acesso:</p>

                    <div style="font-size: 32px; font-weight: bold;
                                letter-spacing: 8px; color: #fff;
                                background: #2f6b3e; border-radius: 12px;
                                padding: 12px 0; margin: 16px 0;">
                        ' . $senhaHtml . '
                    </div>

                    <p style="font-size: 14px; color: #666;">
                        Por segurança, altere sua senha após entrar no sistema.
                    </p>

                    <p style="font-size: 12px; color: #666;">
                        Se você não realizou este cadastro, entre em contato
                        com o responsável pelo sistema.
                    </p>
                </div>';

            $mail->AltBody =
                "Seu cadastro no Alma Pantaneira foi realizado com sucesso. " .
                "Sua senha temporária de primeiro acesso é: {$senha}. " .
                "Altere sua senha após entrar no sistema.";

            return $mail->send();

        } catch (\Throwable $e) {
            error_log(
                'Erro ao enviar senha de primeiro acesso: ' .
                $e->getMessage()
            );

            return false;
        }
    }
}
