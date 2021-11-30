<?php
// Файлы phpmailer
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

sleep(3);

$body = '';

$customTitle = 'Узнать стоимость';
foreach ($_POST as $key => $value) {
    $keyTranslated = $key;
    if (empty($value)) {
        continue;
    }

    if ($key === 'name') {
        $keyTranslated = 'Имя';
    }
    if ($key === 'product-sell') {
        $keyTranslated = 'Концентратор';
        $customTitle = 'Заявка на покупку';
    }
    if ($key === 'product-rent') {
        $keyTranslated = 'Концентратор';
        $customTitle = 'Заявка на аренду';
    }
    if ($key === 'product-notify') {
        $keyTranslated = 'Концентратор';
    }
    if ($key === 'cost') {
        $keyTranslated = 'Стоимость';
    }
    if ($key === 'phone') {
        $keyTranslated = 'Телефон';
    }
    if ($key === 'email') {
        $keyTranslated = 'Электронная почта';
    }
    if ($key === 'message') {
        $keyTranslated = 'Сообщение';
    }
    if ($key === 'renew-lease') {
        $keyTranslated = 'Продлить аренду';
    }
    if ($key === 'notify-name') {
        $keyTranslated = 'Имя';
        $customTitle = 'Уведомить о поступлении';
    }
    $body .= "
        <tr style='background-color: #f8f8f8'>
            <td style='padding: 10px; border: 1px solid #e9e9e9'><b>$keyTranslated</b></td>
            <td style='padding: 10px; border: 1px solid #e9e9e9'><b>$value</b></td>
        </tr>";
}

$body = "
        <h1>$customTitle</h1>
        <table style='width: 100%;'>$body</table>";

// Формирование самого письма
$title = 'Кислородные концентраторы';

// Настройки PHPMailer
$mail = new PHPMailer\PHPMailer\PHPMailer();
try {
    $mail->isSMTP();   
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth   = true;
    // $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

    // Настройки вашей почты
    $mail->Host       = ''; // SMTP сервера вашей почты
    $mail->Username   = ''; // Логин на почте
    $mail->Password   = ''; // Пароль на почте
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->setFrom('dorojnye.znaki@yandex.ru', 'Кислородные Концентраторы'); // Адрес самой почты и имя отправителя

    // Получатель письма

    // Прикрипление файлов к письму
if (!empty($file['name'][0])) {
    for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
        $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name'][$ct]));
        $filename = $file['name'][$ct];
        if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
            $mail->addAttachment($uploadfile, $filename);
            $rfile[] = "Файл $filename прикреплён";
        } else {
            $rfile[] = "Не удалось прикрепить файл $filename";
        }
    }   
}
// Отправка сообщения
$mail->isHTML(true);
$mail->Subject = $title;
$mail->Body = $body;    

// Проверяем отравленность сообщения
if ($mail->send()) {$result = "success";} 
else {$result = "error";}

echo json_encode($body);

} catch (Exception $e) {
    $result = "error";
    $status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
}

// // Отображение результата
// echo json_encode(["result" => $result, "resultfile" => $rfile, "status" => $status]);