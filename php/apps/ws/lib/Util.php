<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Util{
    
   
    
    public function setLog($modulo="",$archivo="frontend"){
        
        //LOG
        $logFechaNombre = $archivo."_".date("Ymd").".log";
        $logPath = sfConfig::get('sf_log_dir').'/'.$logFechaNombre;
        $log = new sfFileLogger(new sfEventDispatcher(), array('level' => sfFileLogger::DEBUG,'file' => $logPath,'type' => $modulo)); 
        return $log;
        
    }
    
    
    public function setCache($url,$data){
        $util = new Util();
        $url = $util->setCleanUrl($url);
        $path = sfConfig::get("sf_cache_dir").DIRECTORY_SEPARATOR."curl".DIRECTORY_SEPARATOR;  
        $util->verificarCarpeta($path);
        $path_archivo = $path.$url;
        file_put_contents($path_archivo, $data);
        return true;        
    }
    
    public function getCache($url,$tiempo=false){
        if(!$tiempo){
            $tiempo_curl_cache = 999999999;
        }else{
            $tiempo_curl_cache = $tiempo;
        }
        
        $tiempo_now = date("U");
        $util = new Util();
        $url = $util->setCleanUrl($url);
        $path = sfConfig::get("sf_cache_dir").DIRECTORY_SEPARATOR."curl".DIRECTORY_SEPARATOR;  
        //$util->verificarCarpeta($path);
        $path_archivo = $path.$url;
        if(file_exists($path_archivo)){
            $tiempo_archivo = filemtime($path_archivo);
            $tiempo_resta = $tiempo_now - $tiempo_archivo;
            if($tiempo_resta > $tiempo_curl_cache){
                unlink($path_archivo);
                return false;
            }else{
                return file_get_contents($path_archivo);
            }
        }else{
            return false;
        }
    }
    
    public function setCleanUrl($string)
    {
        return strtolower(trim(preg_replace('~[^0-9a-z]+~i', '-', html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8')), ENT_QUOTES, 'UTF-8')), '-'));
    }
    
    public function verificarCarpeta($path,$intento=1){
        try{
            if(is_dir($path)){
                $permisosCarpeta = substr(sprintf('%o', fileperms($path)), -4);
                if($permisosCarpeta == 0777){
                    return true;
                }else{
                    $old = umask(0);
                    chmod($path, 0777);
                    umask($old);
                    $nuevosPermisosCarpeta = substr(sprintf('%o', fileperms($path)), -4);
                    return true;
                }
            }else{
                $old = umask(0);
                mkdir($path, 0777,true);
                umask($old);
                if($intento == 1){
                    $this->verificarCarpeta($path, 2);
                }else{
                    return false;
                }
            }
        }  catch (Exception $e){
            return $e->getMessage();
        }
    }
    
    
}

class tempCurl{
    
    private $resultado, $codigo, $info, $log;

    public function __construct() {
        $util = new Util();
        $this->log = $util->setLog("classCurl");
    }
    
    public function post($url,$data=null,$header=null){
        
        $this->log->debug("post | url=$url");
        
        $CONNECTTIMEOUT = 100;
        $TIMEOUT = 100;
        
        $this->log->debug("post | CONNECTTIMEOUT=$CONNECTTIMEOUT | TIMEOUT=$TIMEOUT");
        
        $ch = curl_init();        
        curl_setopt($ch,CURLOPT_URL, $url);
        if(!is_null($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        }
        //if(!is_null($data) && is_array($data)){
            curl_setopt($ch,CURLOPT_POST, count($data));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
        //}
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $CONNECTTIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, $TIMEOUT);

        
        $this->resultado = curl_exec($ch);
        $this->codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->info = curl_getinfo($ch);
        curl_close($ch);
    }
    public function get($url){
        
        $this->log->debug("get | url=$url");
        
        $CONNECTTIMEOUT = 100;
        $TIMEOUT = 100;
        
        
        $this->log->debug("get | CONNECTTIMEOUT=$CONNECTTIMEOUT | TIMEOUT=$TIMEOUT");
        
        $ch = curl_init();        
        curl_setopt($ch,CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        //curl_setopt($ch,CURLOPT_POST, count($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $CONNECTTIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, $TIMEOUT);
        //curl_setopt($ch,CURLOPT_POSTFIELDS, $data);

        
        $this->resultado = curl_exec($ch);
        $this->codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->info = curl_getinfo($ch);
        curl_close($ch);
    }
    public function getResponseText(){
        return $this->resultado;
    }
    public function getResponseCode(){
        return $this->codigo;
    }
    public function getInfo(){
        return $this->info;
    }
    
    
}