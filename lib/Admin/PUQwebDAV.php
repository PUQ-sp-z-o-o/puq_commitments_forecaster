<?php
/*
 * This file is part of the WHMCS module. "Commitments forecaster"
 * System for management and planning of outside and inside commitments.
 *
 * Author: Ruslan Poloviy ruslan.polovyi@puq.pl
 * PUQ sp. z o.o. www.puq.pl
 * Poland
 */

namespace WHMCS\Module\Addon\puq_commitments_forecaster\Admin;

class PUQwebDAV
{
    private $url;
    private $username;
    private $password;


    function __construct($URL,$user,$password)
    {
        $this->url = $URL;
        $this->username = $user;
        $this->password = $password;
    }

    function Download($file){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://' . $this->url . $file);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            return curl_exec($curl);
        }

    function Upload($file, $file_path_str)
    {
        $curl = curl_init();
        $fh_res = fopen($file, 'r');
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->url . $file_path_str);
        curl_setopt($curl, CURLOPT_PUT, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($curl, CURLOPT_INFILE, $fh_res);
        curl_setopt($curl, CURLOPT_INFILESIZE, filesize($file));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, TRUE);
        $curl_response_res = curl_exec($curl);
        fclose($fh_res);
        return $curl_response_res;
    }

    function CreateFolder($dir)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->url . $dir);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'MKCOL');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        $curl_response_res = curl_exec($curl);
        return $curl_response_res;
    }

    function Move($file_old, $file_new)
    {
        $curl = curl_init();
        $fh_res = fopen($file, 'r');
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->url . $file_old);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Destination:https://'. $this->url . $file_new));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'MOVE');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        $curl_response_res = curl_exec($curl);
        fclose($fh_res);
        return $curl_response_res;
    }

    function PropFind($dir){
        if($dir[0] != '/'){
            $dir = '/'.$dir;
        }
        if(substr($dir, -1) != '/'){
            $dir = $dir.'/';
        }
        if($dir != '/'){
            $url = $this->url.$dir;
        }else{
            $url = $this->url;
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' .$url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Depth: 1"));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PROPFIND');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->username.':'.$this->password);
        $answer = curl_exec($curl);
        curl_close($curl);

        $nspaces = 'D';



        $xml = simplexml_load_string($answer);

        $namespaces = $xml->getNamespaces(FALSE);
        foreach ($namespaces as $key => $value){
            $nspaces = $key;
            break;
        }

        $list = [];
        $root = '';
        foreach ($xml->children($nspaces,TRUE) as $value) {
            $file = $value->children($nspaces,TRUE)->href->__toString();
            if($root == ''){
                $root = $file;
            }else{
                if(substr($file, -1) == '/'){
                    $list += [explode('/', $file)[count(explode('/', $file)) - 2 ] => ['type' => 'folder', 'root' => $dir]];
                }
                if(substr($file, -1) != '/'){
                    $list += [explode('/', $file)[count(explode('/', $file)) - 1 ] => ['type' => 'file', 'root' => $dir]];
                }
            }
        }
        return $list;
    }

}