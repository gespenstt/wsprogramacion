<?php
use Parse\ParseObject;
use Parse\ParseQuery;
use Parse\ParseACL;
use Parse\ParsePush;
use Parse\ParseUser;
use Parse\ParseInstallation;
use Parse\ParseException;
use Parse\ParseAnalytics;
use Parse\ParseFile;
use Parse\ParseCloud;

define('HDOM_TYPE_ELEMENT', 1);
define('HDOM_TYPE_COMMENT', 2);
define('HDOM_TYPE_TEXT',    3);
define('HDOM_TYPE_ENDTAG',  4);
define('HDOM_TYPE_ROOT',    5);
define('HDOM_TYPE_UNKNOWN', 6);
define('HDOM_QUOTE_DOUBLE', 0);
define('HDOM_QUOTE_SINGLE', 1);
define('HDOM_QUOTE_NO',     3);
define('HDOM_INFO_BEGIN',   0);
define('HDOM_INFO_END',     1);
define('HDOM_INFO_QUOTE',   2);
define('HDOM_INFO_SPACE',   3);
define('HDOM_INFO_TEXT',    4);
define('HDOM_INFO_INNER',   5);
define('HDOM_INFO_OUTER',   6);
define('HDOM_INFO_ENDSPACE',7);
define('DEFAULT_TARGET_CHARSET', 'UTF-8');
define('DEFAULT_BR_TEXT', "\r\n");
define('DEFAULT_SPAN_TEXT', " ");
define('MAX_FILE_SIZE', 600000);
/**
 * api actions.
 *
 * @package    WSProgramacion
 * @subpackage api
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class apiActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
//      //Parse\ParseClient::initialize($app_id, $rest_key, $master_key)
//      Parse\ParseClient::initialize( "YOpjIC8Zy0Pp3yMTJNa8iKG8PhpPFpQ3gfRDzBoB", "0u4TuZ7mCC49bGCv1Y89QELpmwYJXx8NlsuQJna7", "4VnN9ADUyDTVBpOLWzBQCNSFHVeQ2ACL7tSKg1mH" );
//      
//      $object = Parse\ParseObject::create("TestObject");
//      // Set values:
//$object->set("elephant", "php");
//$object->set("today", new DateTime());
//$object->setArray("mylist", [1, 2, 3]);
//$object->setAssociativeArray(
//  "languageTypes", array("php" => "awesome", "ruby" => "wtf")
//);
//
//// Save:
//$object->save();
        $tv_url = "http://televisionvtr.cl/index.php?obt=grilla&comuna=Santiago&canal_inicio=0&canal_cantidad=200&canal_tipo=cate";
      
        $util = new Util();
        $log = $util->setLog("index");
        $data_cache = $util->getCache($tv_url);
        if($data_cache !== FALSE){
            $respuesta = $data_cache;
            $log->debug("CACHE | respuesta=".($respuesta));
        }else{
            $curl = new tempCurl();
            $curl->get($tv_url);
            $respuesta = $curl->getResponseText();
            $codigo = $curl->getResponseCode();
            $log->debug("http_code=$codigo | respuesta=".($respuesta));
            $log->debug("info | ".  json_encode($curl->getInfo()));
            $util->setCache($tv_url, $respuesta);
        }      
      
        $tv_url_contenido = $respuesta;
        $tv_array = json_decode($tv_url_contenido,true);
        //print_r($tv_array["grilla"]); exit;
        $tv_fix = str_replace("data-", "data_", $tv_array["canales"]);
        $html = $this->str_get_html(utf8_decode($tv_fix));
        $li = $html->find("li");
        $array_canales = array();
        foreach($li as $l){
            //echo $l->data_chn."<br>";
            $id = $l->id;
            $src = "";
            $img_title = "";
            foreach($l->find("img") as $img){
                $src = $img->src;
                $img_title = $img->alt;
            }
            $canal = "";
            foreach($l->find("strong") as $strong){
                $canal = $strong->plaintext;
            }
            $arr = array(
                "id" => $id,
                "imagen" => $src,
                "nombre" => utf8_encode(ucwords($img_title)),
                "canal" => $canal,
            );
            array_push($array_canales, $arr);
        }
//        echo "<pre>";
//        print_r($array_canales);
//        echo "</pre>";
        echo json_encode($array_canales,JSON_PRETTY_PRINT);
      
      exit;
      
  }
  // get html dom from string
    function str_get_html($str, $lowercase=true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=true, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT)
    {
        $dom = new simple_html_dom(null, $lowercase, $forceTagsClosed, $target_charset, $stripRN, $defaultBRText, $defaultSpanText);
        if (empty($str) || strlen($str) > MAX_FILE_SIZE)
        {
            $dom->clear();
            return false;
        }
        $dom->load($str, $lowercase, $stripRN);
        return $dom;
    }
}
