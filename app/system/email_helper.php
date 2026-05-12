<?php
// app/system/email_helper.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function kirim_email($email_penerima, $nama_penerima, $judul_email, $isi_email, $email_form = null, $nama_form = null) {
    $email_pengirim = "pengelola.ehub.bkdsulteng@gmail.com";
    $nama_pengirim  = "E-Hub BKD Prov. Sulteng";

    require_once __DIR__ . '/../../public/assets/vendor/phpmailer/autoload.php';

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $email_pengirim;
        $mail->Password   = 'gszuezxqydtewwea'; // ganti dengan App Password gmail kamu
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        //Recipients
        $mail->setFrom($email_pengirim, $nama_pengirim);
        $mail->addAddress($email_penerima, $nama_penerima);

        // 👇 baris ini agar Gmail menampilkan nama pengirim form
        if ($email_form && $nama_form) {
            $mail->addReplyTo($email_form, $nama_form);
        }

        //Content
        $mail->isHTML(true);
        $mail->Subject = $judul_email;
        $mail->Body    = $isi_email;

        $mail->send();
        return 'Sukses';
    } catch (Exception $e) {
        return "Gagal: {$mail->ErrorInfo}";
    }
}