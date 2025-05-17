<?php

function sendMailWithAttachment($to, $subject, $message, $filePath, $filename) {
    $fileContent = file_get_contents($filePath);
    $fileContent = chunk_split(base64_encode($fileContent));
    $separator = md5(time());

    $headers = "From: no-reply@yourdomain.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"\r\n\r\n";

    $body = "--" . $separator . "\r\n";
    $body .= "Content-Type: text/plain; charset=\"utf-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $message . "\r\n";

    $body .= "--" . $separator . "\r\n";
    $body .= "Content-Type: application/pdf; name=\"" . $filename . "\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n";
    $body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
    $body .= $fileContent . "\r\n";
    $body .= "--" . $separator . "--";

    mail($to, $subject, $body, $headers);
}
