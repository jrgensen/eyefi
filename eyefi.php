<?php
$credentials = array(
    'user1' => 'password',
    'user2' => 'drowssap',
    'admin' => 'password',
);

// fetching user token
$headers = apache_request_headers();
$userToken = isset($headers['X-Gallery-Request-Key']) ? $headers['X-Gallery-Request-Key'] : ':';
if (isset($_POST['user'], $_POST['password'])) {
    $userToken = $_POST['user'] . ':' . md5($_POST['password']);
}

// validating user
list($user, $checksum) = explode(':', $userToken);
if (empty($credentials[$user]) || md5($credentials[$user]) != $checksum) {
    header('HTTP/1.1 403 Forbidden');
    die('[]');
}

$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
$scriptUrl = "$protocol://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";
$command = substr($_SERVER['PHP_SELF'], strlen($_SERVER['SCRIPT_NAME']));

$data = '';
if ($command == '/index.php/rest/') {
    $data = $userToken;
}
if ($command == '/index.php/rest/item/1') {
    $data = array('url' => '', 'entity' => array(), 'relationships' => array(), 'members' => array());
}
if ($command == '/index.php/rest//item/1') {
    $data = array('url' => "$scriptUrl/index.php/rest/item/12");
    header("HTTP/1.1 201 Created");
}
if ($command == '/index.php/rest/item/12') {
    $photo = isset($_FILES['file']) ? $_FILES['file'] : false;
    if ($photo && is_uploaded_file($photo['tmp_name'])) {
        $content = file_get_contents($photo['tmp_name']);
        if (imagecreatefromstring($content)) {
            
            // handle $content
            
            if ($success = true) {
                $data = array('url' => "$scriptUrl/index.php/rest/item/" . time());
                header("HTTP/1.1 201 Created");
            }
        }
    }
}

$json = json_encode($data);

header('Content-Type: application/json');
header('Content-Length: ' . mb_strlen($json));
print $json;

?>
