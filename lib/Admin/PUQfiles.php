<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 */

namespace WHMCS\Module\Addon\puq_commitments_forecaster\Admin;
use WHMCS\Database\Capsule;
use WHMCS\Module\Addon\puq_commitments_forecaster\Admin\PUQwebDAV;



class PUQfiles
{
    private $dav;

    function __construct()
    {
        global $vars;
        $this->dav = new PUQwebDAV($vars['webdav URL'],$vars['webdav user'],$vars['webdav password']);
    }

    function Add($dir,$file){
        global $file_md5;
        global $file_puq;
        $file_md5 = md5_file($file);

        if($this->Availability($file_md5)){
            $_SESSION['PUQmessage'] = ['error', 'The file is already on the system.'];
            return null;
        }

        $dir_a = explode("/", $dir);
        $create = '';
        foreach ($dir_a as $folder) {
            $create .= $folder.'/';
            $this->dav->CreateFolder($create);
        }
        $file_puq = $create . $file_md5 . '.pdf';
        $upload = $this->dav->Upload($file, $file_puq);

        if ($upload == '') {
            try {
                Capsule::connection()->transaction(
                    function ($connectionManager) {
                        global $file_puq;
                        global $file_md5;
                        $connectionManager->table('puq_commitments_forecaster_files')->insert(
                            [
                                'file' => $file_puq,
                                'md5' => $file_md5,
                            ]
                        );
                    }
                );
            } catch (\Exception $e) {
                $_SESSION['PUQmessage'] = ['error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}"];
                return null;
            }
        } else {
            $_SESSION['PUQmessage'] = ['error', $upload];
            return null;
        }
        foreach (Capsule::table('puq_commitments_forecaster_files')
                     ->where('file', $file_puq)
                     ->where('md5', $file_md5)
                     ->get('id') as $value) {
            $id = json_decode(json_encode($value), true);
            return $id['id'];
        }
    }

    function Get($id)
    {
        foreach (Capsule::table('puq_commitments_forecaster_files')->where('id', $id)->get('file') as $value) {
            $file = json_decode(json_encode($value), true)['file'];
        }
        if(!$file){
            echo 'File not found in database';
            exit();
        }
        $fileName = explode('/', $file)[count(explode('/', $file)) - 1];
        $fileDir = explode($fileName, $file)[0];

        if(!array_key_exists($fileName,$this->dav->PropFind($fileDir))){
            echo 'File not found on WebDAV server: '.$file;
            exit();
        }
        header('Content-type: application/pdf');
        header('Content-Disposition: inline; filename="PUQ.pdf"');
        echo $this->dav->Download($file);
    }

    function GetContent($id)
    {
        foreach (Capsule::table('puq_commitments_forecaster_files')->where('id', $id)->get('file') as $value) {
            $file = json_decode(json_encode($value), true)['file'];
        }
        if(!$file){
            echo 'File not found in database';
            exit();
        }
        $fileName = explode('/', $file)[count(explode('/', $file)) - 1];
        $fileDir = explode($fileName, $file)[0];

        if(!array_key_exists($fileName,$this->dav->PropFind($fileDir))){
            echo 'File not found on WebDAV server: '.$file;
            exit();
        }
        return $this->dav->Download($file);
    }



    function Move($id,$dir){
        foreach (Capsule::table('puq_commitments_forecaster_files')
                     ->where('id', $id)
                     ->get() as $value) {
            $file = json_decode(json_encode($value), true);

            $dir_a = explode("/", $dir);
            $create = '';
            foreach ($dir_a as $folder) {
                $create .= $folder.'/';
                $this->dav->CreateFolder($create);
            }
            $file_new = $create . $file['md5'] . '.pdf';
            $this->dav->Move($file['file'], $file_new);

            try {
                Capsule::table('puq_commitments_forecaster_files')->where('id', $id)
                    ->update(
                        [
                            'file'=> $file_new,
                        ]
                    );
            } catch (\Exception $e) {
                $this->message('error', "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}");
            }
        }
    }

    function Availability($md5)
    {
        foreach (Capsule::table('puq_commitments_forecaster_files')->where('md5', $md5)->get('id') as $value) {
            return json_decode(json_encode($value), true)['id'];
        }
    }




}
