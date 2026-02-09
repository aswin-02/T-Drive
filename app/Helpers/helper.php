<?php

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Storage;

function snakeCaseToTitleCase($string)
    {
        return ucwords(str_replace('_', ' ', $string));
    }

/**
 * @param $file_path
 * @return Application|string|UrlGenerator
 */
function generate_file_url($file_path): Application|string|UrlGenerator
{
    if ($file_path && Storage::disk('public')->exists($file_path)) {
        return url(Storage::url($file_path));
    }

    $extension = pathinfo($file_path, PATHINFO_EXTENSION) ?? 'png';

    $dummyFiles = [
        'jpg' => 'images/dummy.jpg',
        'jpeg' => 'images/dummy.jpg',
        'png' => 'images/dummy.png',
        'gif' => 'images/dummy.png',
        'pdf' => 'files/dummy.pdf',
        'doc' => 'files/dummy.doc',
        'docx' => 'files/dummy.docx',
        'xls' => 'files/dummy.xls',
        'xlsx' => 'files/dummy.xlsx',
        'txt' => 'files/dummy.txt',
        'mp4' => 'videos/dummy.mp4',
        'mp3' => 'audio/dummy.mp3',
    ];

    $dummyFile = $dummyFiles[$extension] ?? 'images/dummy.png';

    return false;
    // return url(Storage::url($dummyFile));
}

function EncryptDecrypt($action, $string){
    $output = false;$action=strtoupper($action);
    $encrypt_method = "AES-256-CBC";
    $secret_key = env('ENCRYPTION_KEY');
    $secret_iv = env('ENCRYPTION_IV');
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if($action=='ENCRYPT'){
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = strrev(base64_encode($output));
    }
    elseif($action=='DECRYPT'){
        $output = openssl_decrypt(base64_decode(strrev($string)), $encrypt_method, $key, 0, $iv);;
    }
    return $output;
}
