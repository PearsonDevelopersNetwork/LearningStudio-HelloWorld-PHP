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
namespace HelloWorld;
require_once(dirname(__FILE__).'/bootstrap.php');
require_once(dirname(__FILE__).'/LearningStudioUtility.php');
use Auth as OAuthService;
use Auth\Config as OAuthConfig;
use Utility as LearningUtility;

class HelloWorldService {
  private $oauthConfig, $learningStudioUtility;
  public $LS_API_URL = 'https://api.learningstudio.com';
  public $TEACHER_USERNAME, $oauthHeaders;

  public function __construct () {
    $this->learningStudioUtility = new LearningUtility\LearningStudioUtility;
  }

  public function get_factory() {
    $authServiceFact = parse_ini_file(dirname(__FILE__)."/Resources/HelloWorldConfig.ini");
    $this->TEACHER_USERNAME = $authServiceFact['userName'];
    $oauthConfig = new OAuthConfig\OAuthConfig;
    $oauthConfig->setApplicationId($authServiceFact['applicationId']);
    $oauthConfig->setApplicationName($authServiceFact['applicationName']);
    $oauthConfig->setClientString($authServiceFact['clientString']);
    $oauthConfig->setConsumerKey($authServiceFact['consumerKey']);
    $oauthConfig->setConsumerSecret($authServiceFact['consumerSecret']);
    $factory = new OAuthService\OAuthServiceFactory($oauthConfig);
    return $factory;
  }

  public function doGet ($path) {
    return $this->doMethod('GET', $path);
  }

  public function doPost ($path, $body) {
    return $this->doMethod('POST', $path, $body);
  }

  public function doPut ($path, $body) {
    return $this->doMethod('PUT', $path, $body);
  }

  public function doDelete($path) {
    return $this->doMethod('DELETE', $path);
  }

  private function doMethod($method, $path, $body = NULL) {
    if ($body != NULL and !is_string($body)) {
      $body = json_decode($body, true);
    }
    $response = NULL;
    $url = $this->LS_API_URL . $path;

    $oauthHeaders = is_array($this->oauthHeaders) ? $this->oauthHeaders : array($this->oauthHeaders);
    if ($method == 'GET') {
      $response = $this->learningStudioUtility->doGet($url, $oauthHeaders);
    } else if ($method == 'POST') {
      $response = $this->learningStudioUtility->doPost($url, $oauthHeaders, $body);
    } else if ($method == 'PUT') {
      $response = $this->learningStudioUtility->doPut($url, $oauthHeaders, $body);
    } else if ($method == 'DELETE') {
      $response = $this->learningStudioUtility->doDelete($url, $oauthHeaders);
    }

    if ($response == NULL) {
      $response = '{ "error": "Invalid Request" }';
    }
    return $response;
  }
}

class OAuth2AssertionService extends HelloWorldService {
  public function getOAuthHeaders () {
    $service = $this->get_factory();
    $serviceBuild = $service->build('OAUTH2_ASSERTION');
    $headerBulid = $serviceBuild->generateOAuth2AssertionRequest($this->TEACHER_USERNAME);
    $headers = $headerBulid->getHeaders();
    return $headers;
  }
}

class OAuth1SignatureService extends HelloWorldService {
  public function getOAuthHeaders ($url, $method, $body = NULL) {
    $service = $this->get_factory();
    $serviceBuild = $service->build('OAUTH1_SIGNATURE');
    $headerBulid = $serviceBuild->generateOAuth1Request($method, $this->LS_API_URL.$url, $body, $url);
    $headers = $headerBulid->getHeaders();
    return $headers;
  }
}

/* Format JSON - make it pretty for browser display */
function format_json($json, $html = false, $tabspaces = null){
  $tabcount = 0;
  $result = '';
  $inquote = false;
  $ignorenext = false;

  if ($html) {
    $tab = str_repeat("&nbsp;", ($tabspaces == null ? 4 : $tabspaces));
    $newline = "<br/>";
  } else {
    $tab = ($tabspaces == null ? "  " : str_repeat("  ", $tabspaces));
    $newline = "\n";
  }

  for($i = 0; $i < strlen($json); $i++) {
    $char = $json[$i];
    if ($ignorenext) {
      $result .= $char;
      $ignorenext = false;
    } else {
      switch($char) {
        case ':':
          $result .= $char . (!$inquote ? " " : "");
          break;
        case '{':
          if (!$inquote) { $tabcount++; $result .= $char . $newline . str_repeat($tab, $tabcount);
          } else { $result .= $char; }
          break;
        case '}':
          if (!$inquote) { $tabcount--; $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
          } else { $result .= $char; }
          break;
        case ',':
          if (!$inquote) $result .= $char . $newline . str_repeat($tab, $tabcount);
          else $result .= $char;
          break;
        case '"':
          $inquote = !$inquote;
          $result .= $char;
          break;
        case '\\':
          if ($inquote) $ignorenext = true;
          $result .= $char;
          break;
        default:
          $result .= $char;
          break;
      }
    }
  }
  return $result;
}

//Should not conflict with testcases
if (isset($_SERVER) && isset($_SERVER['HTTP_HOST'])) {
  header("Content-type:application/json");
  function getOauthAndPath ($url) {
    $URL_ARRAY = preg_split("/(oauth[1-2])/", $url, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    return array($URL_ARRAY[1], $URL_ARRAY[2]);
  }

  $REQUEST_BODY = file_get_contents('php://input');
  $REQUEST_METHOD =  $_SERVER['REQUEST_METHOD'];
  $REQUEST_PATH = $_SERVER['REQUEST_URI'];
  $REQUEST_QUERY = $_SERVER['QUERY_STRING'];
  $USER_AGENT = "LS-HelloWorld";
  list($REQUEST_OAUTH_TYPE, $REQUEST_URI) = getOauthAndPath($REQUEST_PATH);

  if ($REQUEST_OAUTH_TYPE == 'oauth2') {
    $oauth2Service = new OAuth2AssertionService;
    $oauthHeaders = $oauth2Service->getOAuthHeaders();
    $oauth2Service->oauthHeaders = array($oauthHeaders, "User-Agent: ".$USER_AGENT);
    if ($REQUEST_METHOD == 'GET') {
      $response = $oauth2Service->doGet($REQUEST_URI);
    } else if ($REQUEST_METHOD == 'POST') {
      $response = $oauth2Service->doPost($REQUEST_URI, $REQUEST_BODY);
    } else if ($REQUEST_METHOD == 'PUT') {
      $response = $oauth2Service->doPut($REQUEST_URI, $REQUEST_BODY);
    } else if ($REQUEST_METHOD == 'DELETE') {
      $response = $oauth2Service->doDelete($REQUEST_URI);
    }

    // checkin if response contains any content or not
    if (strlen($response->content()) != 0 && $response->content() != NULL) {
      echo format_json($response->content());
    } else {
      header("Content-type: text/plain");
      echo $response->statusCode();
    }
  } else if ($REQUEST_OAUTH_TYPE == 'oauth1') {
    if ($REQUEST_METHOD == 'GET') {
      $oauth1Service = new OAuth1SignatureService;
      $oauthHeaders = $oauth1Service->getOAuthHeaders($REQUEST_URI, "GET");
      $oauth1Service->oauthHeaders = array($oauthHeaders, "User-Agent: ".$USER_AGENT);
      $response = $oauth1Service->doGet($REQUEST_URI);
    } else if ($REQUEST_METHOD == 'POST') {
      $oauth1Service = new OAuth1SignatureService;
      $oauthHeaders = $oauth1Service->getOAuthHeaders($REQUEST_URI, "POST", $REQUEST_BODY);
      $oauth1Service->oauthHeaders = array($oauthHeaders, "User-Agent: ".$USER_AGENT);
      $response = $oauth1Service->doPost($REQUEST_URI, $REQUEST_BODY);
    } else if ($REQUEST_METHOD == 'PUT') {
      $oauth1Service = new OAuth1SignatureService;
      $oauthHeaders = $oauth1Service->getOAuthHeaders($REQUEST_URI, "PUT", $REQUEST_BODY);
      $oauth1Service->oauthHeaders = array($oauthHeaders, "User-Agent: ".$USER_AGENT);
      $response = $oauth1Service->doPut($REQUEST_URI, $REQUEST_BODY);
    } else if ($REQUEST_METHOD == 'DELETE') {
      $oauth1Service = new OAuth1SignatureService;
      $oauthHeaders = $oauth1Service->getOAuthHeaders($REQUEST_URI, "DELETE");
      $oauth1Service->oauthHeaders = array($oauthHeaders, "User-Agent: ".$USER_AGENT);
      $response = $oauth1Service->doDelete($REQUEST_URI);
    }

    // checkin if response contains any content or not
    if (strlen($response->content()) != 0 && $response->content() != NULL) {
      echo format_json($response->content());
    } else {
      header("Content-type: text/plain");
      echo $response->statusCode();
    }
  } else {
    echo  '{ "response": "Invalid URL" }';
  }
}
?>
