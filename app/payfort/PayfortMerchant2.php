<?php
/**
 * Payfort PHP library created by Conceptualize. This class calls
 * the merchant api. The api allows you to accept credit card
 * information onsite and customize the payment form.
 *
 * Mandatory injection of javascript for validation is required.
 *
 * This is the Merchant 2.0 version
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

class PayfortMerchant2 extends PayfortBase {

    /**
     * Client version
     * @var string
     */
    const VERSION = '1.1';

    /**
     * Default value for 3D Secure enable.
     *
     * @var string
     */
    protected $enable3DS = false;

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
     * Should a token be set as remember_me.
     *
     * @var string
     */
    protected static $rememberToken = FALSE;

    /**
     * primary parameters for signature calculation
     *
     * @var string
     */
    protected $primary_params = array('merchant_identifier','access_code','merchant_reference','language','return_url','service_command','card_holder_name','expiry_date','response_message','token_name','card_bin','status','response_code','card_number','check_3ds','remember_me');

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
     * Control the 3D secure parameter setting.
     *
     * @param $mode boolean
     * @return void
     */
    public function set3DSecureMode($mode = true)
    {
        $this->enable3DS = $mode;
    }

    /**
     * Check if the 3D Secure option is set to.
     *
     * @return boolean
     */
    public function get3DSecureMode()
    {
        return $this->enable3DS;
    }

    /**
     * Set the value of the token to be permanent or not
     *
     * @param $command string
     * @return void
     */
    public function setRememberToken($command)
    {
        self::$rememberToken = $command;
    }

    /**
     * get the current merchant identifier
     *
     * @return string
     */
    public function getRememberToken()
    {
        return self::$rememberToken;
    }

    /**
     * process a new request for a request on form submission
     *
     * @param string $method
     * @return json
     */
    function processRequest()
    {
        $merchantPageData = $this->generateMerchantPageData();
        $form = $this->generatePaymentForm($merchantPageData['url'], $merchantPageData['params']);
        return json_encode(array('form' => $form, 'url' => $merchantPageData['url'], 'params' => $merchantPageData['params']));
    }

    /**
     * function to generate the data for a merchant 2.0 page. This method only does a tokenization request.
     *
     * @return array
     */
    private function generateMerchantPageData()
    {
        $iframeParams = array();

        if(is_null($this->getMerchantIdentifier())) {
            throw new InvalidArgumentException('Error in processRequest: Merchant Identifier is not set.');
        }
        if(is_null($this->getMerchantAccessCode())) {
            throw new InvalidArgumentException('Error in processRequest: Merchant Access Code is not set.');
        }
        if(is_null($this->getMerchantReference())) {
            throw new InvalidArgumentException('Error in processRequest: Merchant Reference ID is not set.');
        }
        if(is_null($this->getReturnUrl())) {
            throw new InvalidArgumentException('Error in processRequest: The return uri is not set.');
        }

        $returnURL = $this->getReturnUrl();

        if(!$this->get3DSecureMode()) {
            $returnURL = $this->parseURL($returnURL,array('3ds'=>'no','t'=>time()));
        } else {
            $returnURL = $this->parseURL($returnURL,array('3ds'=>'yes','t'=>time()));
        }

        $iframeParams = array(
            'merchant_identifier' => $this->getMerchantIdentifier(),
            'access_code' => $this->getMerchantAccessCode(),
            'merchant_reference' => $this->getMerchantReference(),
            'language' => $this->language,
            'return_url' => $returnURL,
            'service_command' => 'TOKENIZATION'
        );
        // Calculate and strore the signature
        $iframeParams['signature'] = $this->calculateSignature($iframeParams, 'request');

        if($this->getRememberToken()) {
            $iframeParams['remember_me'] = "YES";
        } else {
            $iframeParams['remember_me'] = "NO";
        }

        $gatewayUrl = $this->getBaseURL().$this->endpoints['paymentPage'];

        // Add debug information for log purpose;
        $debugMsg = "Fort Redirect Request Parameters \n".print_r($iframeParams, 1);
        $this->log($debugMsg);

        return array('url'=>$gatewayUrl, 'params' => $iframeParams);
    }

    /**
     * function to create a form with all hidden parameters to be submitted
     * @return html
     */
    private function generatePaymentForm($url,$params)
    {
        $form = '<form style="display:none" name="payfort_final_payment_form" id="payfort_final_payment_form" method="post" action="' . $url . '">';
        foreach ($params as $k => $v) {
            $form .= '<input type="hidden" name="' . $k . '" value="' . $v . '">';
        }
        $form .= '<input type="submit" id="submit">';
        $form .= '</form>';
        return $form;
    }

    /**
     * Once a token is received, the merchant request is sent to complete a transaction
     *
     * @param $params array
     * @return array
     */
    public function processMerchantRequest($params = array())
    {
        $res = array('success'=>TRUE);
        $debugMsg = "Fort Merchant Page Response Parameters \n".print_r($params, 1);
        $this->log($debugMsg);

        if(is_null($params) || count($params)==0) {
            $res = array('success' => FALSE, 'message' => 'Transaction Failed');
            $res['response']['message'] = "Parameters are Null.";
            $this->log("Invalid response parameters received in processMerchantRequest. Parameters are null.");
        } else {
            $fortParams = $params;
            $responseSignature = $params['signature'];
            $merchantReference = $params['merchant_reference'];

            // remove non calculation based items
            foreach($fortParams as $k=>$v) {
                if(in_array($k,$this->primary_params)) {
                    continue;
                } else {
                    unset($fortParams[$k]);
                }
            }
            // now calculate the signature from the actual ones.
            $calculatedSignature = $this->calculateSignature($fortParams, 'response');
            if ($responseSignature != $calculatedSignature) {
                // issue!
                $res = array('success' => FALSE, 'message' => 'Transaction Failed');
                $res['response']['message'] = "Invalid signature calculated [section 1]";
                $debugMsg = sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $responseSignature, $calculatedSignature);
                $this->log($debugMsg);
            } else {
                // the signature matches. Now check the response code
                $response_code    = $params['response_code'];

                if (substr($response_code, 2) != '000') {
                    $res = array('success' => FALSE, 'message' => 'Transaction Failed');
                    $res['response']['message'] = $params['response_message'];
                    $this->log($params['response_message']);
                } else {
                    // send a host to host request
                    $host2hostParams = $this->sendHostTwoHostRequest($params);

                    if (!$host2hostParams) {
                        // meaning the API call was a failure and the issue could be in CURL;
                        $res = array('success' => FALSE, 'message' => 'Transaction Failed');
                        $res['response']['message'] = "API call in Host2Host transaction has failed.";
                        $this->log("API call in Host2Host transaction failed");
                    } else {
                        // The host2host parameter was a success.
                        $fortParams = $host2hostParams;
                        $responseSignature = $host2hostParams['signature'];
                        $merchantReference = $host2hostParams['merchant_reference'];
                        // remove the signature value
                        unset($fortParams['signature']);

                        // now calculate the signature
                        $calculatedSignature = $this->calculateSignature($fortParams, 'response');
                        if ($responseSignature != $calculatedSignature) {
                            // issue!
                            $res = array('success' => FALSE, 'message' => 'Transaction Failed');
                            $res['response']['message'] = "Invalid signature calculated [section 2]";
                            $debugMsg = sprintf('Invalid Signature. Calculated Signature: %1s, Response Signature: %2s', $responseSignature, $calculatedSignature);
                            $this->log($debugMsg);
                        } else {
                            $response_code = $host2hostParams['response_code'];
                            if (substr($response_code, 2) != '000') {
                                $res = array('success' => FALSE, 'message' => 'Transaction Failed');
                                $res['response']['message'] = $host2hostParams['response_message'];
                                $this->log($host2hostParams['response_message']);
                            } else {
                                $res = array('success' => TRUE, 'message' => 'Transaction Success');
                            }
                        }
                    }
                    $res['response']['params'] = $host2hostParams;
                }
            }
        }
        unset($params['signature']);
        unset($params['return_url']);
        $res['request']['params'] = $params;
        return $res;
    }

    /**
     * send a host 2 host request to process a transaction
     *
     * @param $params array
     * @return array
     */
    private function sendHostTwoHostRequest($params)
    {
        $gatewayUrl = $this->getBaseURL().$this->endpoints['paymentApi'];

        // get the customer information
        $cust = $this->getCustomerData();
        if(!isset($cust['email']) || $cust['email'] == '') {
            throw new InvalidArgumentException('Customer information is mandatory. Email address is missing for the HostTwoHostRequest Transaction.');
        }
        if(!isset($cust['name'])) {
            // default to card holders name
            $cust['name'] = $params['card_holder_name'];
        }

        $postData      = array(
            'merchant_reference'  => $params['merchant_reference'],
            'access_code'         => $this->getMerchantAccessCode(),
            'command'             => $this->getServiceCommand(),
            'merchant_identifier' => $this->getMerchantIdentifier(),
            'customer_ip'         => $_SERVER['REMOTE_ADDR'],
            'amount'              => $this->convertFortAmount($this->getAmount(), $this->getDefaultCurrency()),
            'currency'            => strtoupper($this->getDefaultCurrency()),
            'customer_email'      => trim($cust['email']),
            'customer_name'       => trim($cust['name']),
            'token_name'          => $params['token_name'],
            'language'            => $this->getLanguage(),
            'return_url'          => $this->getReturnUrl()
        );

        if($this->getRememberToken()) {
            $postData['remember_me'] = "YES";
        } else {
            $postData['remember_me'] = "NO";
        }

        if($this->get3DSecureMode()) {
            $postData['check_3ds'] = 'YES';
        } else {
            $postData['check_3ds'] = 'NO';
        }

        //calculate request signature
        $signature = $this->calculateSignature($postData, 'request');
        $postData['signature'] = $signature;

        $debugMsg = "Fort Host2Host Request Parameters \n".print_r($postData, 1);
        $this->log($debugMsg);

        // call the API
        $array_result = $this->callApi($postData, $gatewayUrl);

        $debugMsg = "Fort Host2Host Response Parameters \n".print_r($array_result, 1);
        $this->log($debugMsg);

        return $array_result;
    }
}