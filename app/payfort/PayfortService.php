<?php

/**
 * Part of the Conceptlz - Payfort package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the MIT License.
 *
 * @package    Conceptlz
 * @version    1.1
 * @author     Conceptualize Web Design
 * @license    MIT
 * @copyright  (c) 2017, Conceptualize Web Design
 * @link       http://www.conceptualize.ae
 */

namespace App\Payfort;

use App\Payfort\Base\PayfortBase;

use InvalidArgumentException;

class PayfortService extends PayfortBase {

  /**
   * Client version
   * @var string
   */
  const VERSION = '1.1';

  /**
   * The default currency to be used.
   *
   * @var string
   */
  protected $defaultCurrency = 'AED';

  /**
   * The current amount to be used for the transaction.
   *
   * @var string
   */
  protected $currentAmount = 0;

  /**
   * The data about a customer.
   *
   * @var string
   */
  protected $customer = array();

  /**
   * The Token to be used
   *
   * @var string
   */
  protected $token = '';

  /**
   * The transaction description to be used.
   *
   * @var string
   */
  protected $transactionDescription = '';

  /**
   * Create a new Payfort instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Set the data about the customer.
   *
   * @param $customer_name string
   * @param $customer_email string
   * @param $customer_id int
   * @return void
   */
  public function setCustomerData($customer_id, $customer_name, $customer_email)
  {
    $this->customer = array('id'=>$customer_id,'name'=>$customer_name,'email'=>$customer_email);
  }

  /**
   * Get customer information.
   *
   * @return array
   */
  public function getCustomerData()
  {
    return $this->customer;
  }

  /**
   * Set the default currency for the transaction.
   *
   * @param $currency string
   * @return void
   */
  public function setDefaultCurrency($currency)
  {
    $this->defaultCurrency = $currency;
  }

  /**
   * Get the default currency currently set.
   *
   * @return string
   */
  public function getDefaultCurrency()
  {
    return $this->defaultCurrency;
  }

  /**
   * Set the current amount for the transaction.
   *
   * @param $amount float
   * @return void
   */
  public function setAmount($amount)
  {
    $this->currentAmount = $amount;
  }

  /**
   * Get the current set amount.
   *
   * @return string
   */
  public function getAmount()
  {
    return $this->currentAmount;
  }

  /**
   * Set the token to be used.
   *
   * @param $token string
   * @return void
   */
  public function setToken($token)
  {
    $this->token = $token;
  }

  /**
   * Get the current token used.
   *
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * Set charge description to identify the purchase.
   *
   * @param $desc string
   * @return void
   */
  public function setOrderDescription($desc)
  {
    $this->transactionDescription = $desc;
  }

  /**
   * Get the transaction description
   *
   * @return string
   */
  public function getOrderDescription()
  {
    return $this->transactionDescription;
  }

  /**
   *
   */
  public function processRecurringTransaction() {

    // get the customer information
    $cust = $this->getCustomerData();

    if(is_null($this->getMerchantIdentifier())) {
      throw new InvalidArgumentException('Error in processRecurringTransaction: Merchant Identifier is not set.');
    }
    if(is_null($this->getMerchantAccessCode())) {
      throw new InvalidArgumentException('Error in processRecurringTransaction: Merchant Access Code is not set.');
    }
    if(is_null($this->getMerchantReference())) {
      throw new InvalidArgumentException('Error in processRecurringTransaction: Merchant Reference ID is not set.');
    }
    if(is_null($this->getToken())) {
      throw new InvalidArgumentException('Error in processRecurringTransaction: A token is mandatory and must be set.');
    }
    if(is_null($this->getAmount())) {
      throw new InvalidArgumentException('Error in processRecurringTransaction: An amount must be included to process a transaction.');
    }
    if(!isset($cust['email']) || trim($cust['email']) == '') {
      throw new InvalidArgumentException('Customer information is mandatory. Email address is missing for the HostTwoHostRequest Transaction.');
    }

    $res = array();
    $res['success'] = TRUE;

    $postData      = array(
      'merchant_reference'  => $this->getMerchantReference(),
      'access_code'         => $this->getMerchantAccessCode(),
      'command'             => $this->getServiceCommand(),
      'merchant_identifier' => $this->getMerchantIdentifier(),
      'amount'              => $this->convertFortAmount($this->getAmount(), $this->getDefaultCurrency()),
      'currency'            => strtoupper($this->getDefaultCurrency()),
      'language'            => $this->getLanguage(),
      'customer_email'      => trim($cust['email']),
      'eci'                 => 'RECURRING',
      'token_name'          => $this->getToken()
    );

    if(trim($cust['name'])!='') {
      $postData['customer_name'] = trim($cust['name']);
    }
    if($this->getOrderDescription()!='') {
      $postData['order_description'] = $this->getOrderDescription();
    }

    $response = $this->generateRecurringRequest($postData);
    if(is_null($response) || count($response)==0) {
      $res['success'] = FALSE;
      $res['message'] = "Transaction Failed";
      $res['response']['message'] = "Parameters are Null.";
      $this->log("Invalid response parameters received in processRecurringTransaction");
    } else {
      $fortParams = $response;
      $responseSignature = $response['signature'];
      // remove signature value
      unset($fortParams['signature']);

      $calculatedSignature = $this->calculateSignature($fortParams, 'response');

      if ($responseSignature != $calculatedSignature) {
        $res['success'] = FALSE;
        $res['message'] = "Transaction Failed";
        $res['response']['message'] = "Invalid signature calculated [section 1].";
        $debugMsg = sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $responseSignature, $calculatedSignature);
        $this->log($debugMsg);
      } else {
        $response_code = $response['response_code'];
        if (substr($response_code, 2) != '000') {
          $res['success'] = FALSE;
          $res['message'] = "Transaction Failed";
          $res['response']['message'] = $response['response_message'];
          $this->log($response['response_message']);
        } else {
          $res['success'] = TRUE;
          $res['message'] = "Transaction Success";
        }
      }
    }
    $res['response']['params'] = $response;
    return $res;
  }

  /**
   *
   */
  private function generateRecurringRequest($postData) {

    $gatewayUrl = $this->getBaseServiceURL().$this->endpoints['paymentApi'];

    //calculate request signature
    $signature = $this->calculateSignature($postData, 'request');
    $postData['signature'] = $signature;

    $debugMsg = "Fort Service Request Parameters \n".print_r($postData, 1);
    $this->log($debugMsg);

    // call the API
    $array_result = $this->callApi($postData, $gatewayUrl);

    $debugMsg = "Fort Service Response Parameters \n".print_r($array_result, 1);
    $this->log($debugMsg);

    return $array_result;

  }

}
