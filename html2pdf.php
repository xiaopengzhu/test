<?php
error_reporting(0);

$auth = $_GET['auth'];

if ($auth != 'qingtong_app') exit;

$url = $_GET['url'];

if (!$url) exit; 

$file_name = md5(time()).rand(123456, 654321).'.pdf';
$save_file = "./tmp/" . $file_name;

function my_exec($cmd, $input = '')
{
    $proc = proc_open($cmd, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
    fwrite($pipes[0], $input);
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $rtn = proc_close($proc);
    return array('stdout' => $stdout,
        'stderr' => $stderr,
        'return' => $rtn
    );
}

$cmd = "/root/wkhtmltox/bin/wkhtmltopdf -q '" . $url . "' " . $save_file;

$rt = my_exec($cmd);

if ($rt['return'] === 0) {
    $full = "http://" . $_SERVER["SERVER_NAME"] . "/tmp/" . urlencode($file_name);
    exit(json_encode(['code' => 200, 'src' => $full]));

} else {
    exit(json_encode(['code' => 500]));
}
