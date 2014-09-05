<?php
/*
* LearningStudio HelloWorld Application & API Explorer 
* 
* Need Help or Have Questions? 
* Please use the PDN Developer Community at https://community.pdn.pearson.com
*
* @category   LearningStudio HelloWorld
* @author     Wes Williams <wes.williams@pearson.com>
* @author     Pearson Developer Services Team <apisupport@pearson.com>
* @copyright  2014 Pearson Education Inc.
* @license    http://www.apache.org/licenses/LICENSE-2.0  Apache 2.0
* @version    1.0
* 
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
* 
*     http://www.apache.org/licenses/LICENSE-2.0
* 
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
namespace Utility;

class Response {
  private $_status_code, $_reason, $_content_type, $_content;
  public function __construct($status_code = 200, $content_type = 'application/json', $content = NULL) {
    $this->_status_code = $status_code;
    $this->_content_type = $content_type;
    $this->_content = $content;
  }

  public function statusCode () {
    return $this->_status_code;
  }
  public function contentType () {
    return $this->_content_type;
  }
  public function content () {
    return $this->_content;
  }
}

class LearningStudioUtility {
  public function doGet($uri, $oauthHeaders) {
    return $this->_doMethod('GET', $uri, $oauthHeaders, NULL);
  }

  public function doPost($uri, $oauthHeaders, $body = NULL) {
    return $this->_doMethod('POST', $uri, $oauthHeaders, $body);
  }

  public function doPut($uri, $oauthHeaders, $body = NULL) {
    return $this->_doMethod('PUT', $uri, $oauthHeaders, $body);
  }

  public function doDelete($uri, $oauthHeaders) {
    return $this->_doMethod('DELETE', $uri, $oauthHeaders, NULL);
  }

  private function _doMethod($method, $uri, $oauthHeaders, $body) {
    $is_xml = substr(strtolower($uri), strlen('.xml')) === ".xml";
    $m = strtoupper($method);
    $byte_array = NULL;
    $has_content = ($m == 'POST' || $m == 'PUT') && ($body != NULL);
    $httpRequest = curl_init();
    if ($has_content) {
      array_push($oauthHeaders, 'Content-Type: '.(($is_xml) ? 'application/xml' : 'application/json'));
      array_push($oauthHeaders, 'Content-length: '.(String)(strlen($body)));
      @curl_setopt($httpRequest, CURLOPT_POSTFIELDS, $body);
    }
    @curl_setopt($httpRequest, CURLOPT_URL, $uri);
    @curl_setopt($httpRequest, CURLOPT_RETURNTRANSFER, true);
    @curl_setopt($httpRequest, CURLOPT_CUSTOMREQUEST, $method);
    @curl_setopt($httpRequest, CURLOPT_HTTPHEADER, $oauthHeaders);
    @curl_setopt($httpRequest, CURLOPT_SSL_VERIFYPEER, false);
    
    $httpResponse = curl_exec($httpRequest);
    $info = curl_getinfo($httpRequest);
    $response_code = $info['http_code'];
    $content_type = 'application/json';
    if ($info['content_type'] != NULL && strlen($info['content_type']) > 0) {
      $content_type = $info['content_type'];
    }
    return new Response($response_code, $content_type, $httpResponse);
  }
}
