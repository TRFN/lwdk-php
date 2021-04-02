<?php
function ctrl_util_email($args){
    $instance = new class extends APPControls {
        function initMailer(){
            // Inicia a classe PHPMailer
            $mail = $this->loadPlugin("PHPMailer-5.2-stable@PHPMailer");

            // return ($this->email = $mail);

            // Define os dados do servidor e tipo de conexão
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->IsSMTP(); // Define que a mensagem será SMTP
            $mail->Host = "smtp.uni5.net"; // Endereço do servidor SMTP (caso queira utilizar a autenticação, utilize o host smtp.seudomínio.com.br)
            $mail->SMTPAuth = true; // Usar autenticação SMTP (obrigatório para smtp.seudomínio.com.br)
            $mail->Username = 'mario@hetsi.com.br'; // Usuário do servidor SMTP (endereço de email)
            $mail->Password = 'bi310309'; // Senha do servidor SMTP (senha do email usado)

            // Define o remetente
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->From = "contato@totalalimentacao.com.br"; // Seu e-mail
            $mail->Sender = "hetsi@hetsi.com.br"; // Seu e-mail
            $mail->FromName = "Pesquisa Total Alimentacao"; // Seu nome


            // Define os dados técnicos da Mensagem
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
            //$mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)
            $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)

            return($this->email = $mail);
        }

        function from(String $form_name, String $from_email){
            // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
            $this->email->From = $form_email; // Seu e-mail
            $this->email->FromName = $form_name; // Seu nome
        }

        function add(String $email){
            $this->email->AddAddress($email, $email);
        }

        function addFile(String $file){
            $this->email->AddAttachment($file);
        }

        function send(String $title, String $content){
            $this->email->Subject  = utf8_decode($title); // Assunto da mensagem
            $this->email->Body = $content;
            $this->email->AltBody = ' ';
            $this->email->Send();
        }
    };

    $instance->args = $args;
    $instance->initMailer();

    return $instance;
}
