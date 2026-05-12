<?php

function notify(
    mysqli $conn,
    array $data
) {
    /*
    $data = [
        'to_user_uuid'   => '',
        'from_user_uuid' => '',
        'type'           => 'insert|update|delete',
        'module'         => 'surat_menyurat',
        'title'          => '',
        'message'        => '',
        'link'           => ''
    ];
    */

    $sql = "
        INSERT INTO notifications
        (user_uuid, from_user_uuid, type, type_notify, severity, module, title, message, link, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssssssss",
        $data['user_uuid'],
        $data['from_user_uuid'],
        $data['type'],
        $data['type_notify'],
        $data['severity'],
        $data['module'],
        $data['title'],
        $data['message'],
        $data['link'],
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    );

    return $stmt->execute();
}