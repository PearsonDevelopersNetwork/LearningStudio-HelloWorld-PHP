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
namespace Auth\Config;

/**
 * Auth Service Config
 * OAuth 1 Config functionality to store and retrieve,
 * applicationId, consumerKey, consumerSecret, requestUrl, apiRoute
 * @author: Pradeep Patro <pradeep.patro@happiestminds.com>
 * @version: v1.0
 * @copyright: Pearson 2014
 * @package: Auth
 * @since: 12th Jun 2014
 */
class OAuth1SignatureConfig {

  private $applicationId;
  private $consumerKey;
  private $consumerSecret;
  private $requestUrl;
  private $apiRoute;

  /**
   * Accessor method for application id
   * @access public
   * @return string $applicationId Id of Application
   */
  public function getApplicationId () {
    return $this->applicationId;
  }

  /**
   * Mutator method for application id
   * @access public
   * @param string $applicationId the application id value to be set
   */
  public function setApplicationId ($applicationId) {
    $this->applicationId = $applicationId;
  }

  /**
   * Accessor method for consumer key
   * @access public
   * @return string $consumerKey Returns Consumer Key
   */
  public function getConsumerKey () {
    return $this->consumerKey;
  }

  /**
   * Mutator method for consumer key
   * @access public
   * @param string $consumerKey the consumer key value to be set.
   */
  public function setConsumerKey ($consumerKey) {
    $this->consumerKey = $consumerKey;
  }

  /**
   * Accessor method for consumer secret.
   * @access public
   * @return string $consumerSecret Consumer Secret
   */
  public function getConsumerSecret () {
    return $this->consumerSecret;
  }

  /**
   * Mutator method for consumer secret
   * @access public
   * @param string $consumerSecret the consumer secret value to be set.
   */
  public function setConsumerSecret ($consumerSecret) {
    $this->consumerSecret = $consumerSecret;
  }

  /**
   * Accessor method for request URL
   * @access public
   * @return string $url Url of the current request while using oauth2
   */
  public function getUrl () {
    return $this->requestUrl;
  }

  /**
   * Mutator method for request url
   * @access public
   * @param string $requestUrl the request url value to be set
   */
  public function setUrl ($requestUrl) {
    $this->requestUrl = $requestUrl;
  }

  /**
   * Accessor method for api route
   * @access public
   * @return string $apiRoute Api Route value
   */
  public function getApiRoute () {
    return $this->apiRoute;
  }

  /**
   * Mutator method for api route
   * @access public
   * @param string $apiRoute api route value to be set.
   */
  public function setApiRoute ($apiRoute) {
    $this->apiRoute = $apiRoute;
  }
}
