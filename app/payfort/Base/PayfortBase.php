<?php

/**
 * Core class for the Payfort PHP library created by Conceptualize.
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

namespace App\Payfort\Base;

use InvalidArgumentException;

class PayfortBase implements PayfortBaseInterface {

    /**
     * API Base url for production.
     *
     * @var string
     */
    protected static $gatewayHost = 'https://checkout.payfort.com/';

    /**
     * API Base url for sandbox.
     *
     * @var string
     */
    protected static $gatewaySandboxHost = 'https://sbcheckout.payfort.com/';

    /**
     * Services API Base url for production.
     *
     * @var string
     */
    protected static $servicesHost = 'https://paymentservices.payfort.com/';

    /**
     * Services API Base url for sandbox.
     *
     * @var string
     */
    protected static $servicesSandboxHost = 'https://sbpaymentservices.payfort.com/';

    /**
     * API endpoints
     * @var array
     */
    protected $endpoints = array(
        'paymentPage' => 'FortAPI/paymentPage/',
        'paymentApi' => 'FortAPI/paymentApi/'
    );

    /**
     * Merchant Identifier string
     *
     * @var string
     */
    protected static $merchantIdentifier;

    /**
     * Merchant Access Code string
     *
     * @var string
     */
    protected static $accessCode;

    /**
     * Merchant reference number. This is usually your internal order id
     *
     * @var string
     */
    protected static $merchantReference;

    /**
     * Merchant SHA request phrase to be use in signature calculation.
     *
     * @var string
     */
    protected static $shaRequestPhrase;

    /**
     * Merchant SHA response phrase to be use in signature calculation.
     *
     * @var string
     */
    protected static $shaResponsePhrase;

    /**
     * Default use of language
     *
     * @var string
     */
    protected $language = 'en';

    /**
     * Default hashing algorithm to use
     *
     * @var string SHA Type
     * expected Values ("sha1", "sha256", "sha512")
     */
    protected $hashType = 'sha256';

    /**
     * Should the sandbox method be used by default?
     *
     * @var boolen
     */
    protected $sandboxMode = true;

    /**
     * The service command to be used.
     *
     * @var string
     */
    protected static $serviceCommand = 'AUTHORIZATION';

    /**
     * default return url.
     *
     * @var string
     */
    protected $returnURL;

    /**
     * User agent to use.
     *
     * @var string
     */
    protected $userAgent;

    public function __construct() {
        $this->userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0';
    }

    /**
     * Set the default language.
     *
     * @param $lang string
     * @return void
     */
    public function setLanguage($lang = 'en')
    {
        $arrAllowed = array('en','ar');

        if(!in_array($lang,$arrAllowed)) {
            throw new InvalidArgumentException('You must provide a valid language code.');
        } else {
            $this->language = $lang;
        }
    }

    /**
     * Get the default language currently set.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the default SHA type.
     *
     * @param $hash string
     * @return void
     */
    public function setHashType($hash)
    {
        $arrAllowed = array('sha1','sha256','sha512');

        //check if the provided sha type is valid.
        if(!in_array($hash,$arrAllowed)) {
            throw new InvalidArgumentException('You must provide a valid SHA Type which must either be sha1, sha256 or sha512.');
        } else {
            $this->hashType = $hash;
        }
    }

    /**
     * Get the default language currently set.
     *
     * @return string
     */
    public function getHashType()
    {
        return $this->hashType;
    }

    /**
     * Control the use of the sandbox mode.
     *
     * @param $mode boolean
     * @return void
     */
    public function setSandboxMode($mode = true)
    {
        $this->sandboxMode = $mode;
    }

    /**
     * Check if the gateway is enabled to work for Sandbox mode or not.
     *
     * @return boolean
     */
    public function getSandboxMode()
    {
        return $this->sandboxMode;
    }

    /**
     * Set the service command. This is mandatory
     *
     * @param $command string
     * @return void
     */
    public function setServiceCommand($command)
    {
        $arrAllowed = array('AUTHORIZATION','PURCHASE');

        //check if the provided sha type is valid.
        if(!in_array($command,$arrAllowed)) {
            throw new InvalidArgumentException('You must provide a valid service command that can be either AUTHORIZATION or PURCHASE.');
        } else {
            self::$serviceCommand = $command;
        }
    }

    /**
     * get the current merchant identifier
     *
     * @return string
     */
    public function getServiceCommand()
    {
        return self::$serviceCommand;
    }

    /**
     * The the merchant reference number. This is mandatory
     * Use this to pass a reference of your cart order to Payfort
     *
     * @param $identifier string
     * @return void
     */
    public function setMerchantReference($id)
    {
        self::$merchantReference = $id;
    }

    /**
     * get the current set merchant reference id
     *
     * @return string
     */
    public function getMerchantReference()
    {
        return self::$merchantReference;
    }

    /**
     * The the merchant identifier. This is mandatory
     *
     * @param $identifier string
     * @return void
     */
    public function setMerchantIdentifier($identifier)
    {
        self::$merchantIdentifier = $identifier;
    }

    /**
     * get the current merchant identifier
     *
     * @return string
     */
    public function getMerchantIdentifier()
    {
        return self::$merchantIdentifier;
    }

    /**
     * The the merchant access code. This is mandatory
     *
     * @param $access string
     * @return void
     */
    public function setMerchantAccessCode($access)
    {
        self::$accessCode = $access;
    }

    /**
     * get the current merchant access code
     *
     * @return string
     */
    public function getMerchantAccessCode()
    {
        return self::$accessCode;
    }

    /**
     * Set the merchat request phrase
     *
     * @param $phrase string
     * @return void
     */
    public function setRequestPhrase($phrase)
    {
        self::$shaRequestPhrase = $phrase;
    }

    /**
     * get the request phrase set in the object
     *
     * @return string
     */
    public function getRequestPhrase()
    {
        return self::$shaRequestPhrase;
    }

    /**
     * Set the merchat response phrase
     *
     * @param $phrase string
     * @return void
     */
    public function setResponsePhrase($phrase)
    {
        self::$shaResponsePhrase = $phrase;
    }

    /**
     * Get the response pharese set in the system
     *
     * @return string
     */
    public function getResponsePhrase()
    {
        return self::$shaResponsePhrase;
    }

    /**
     * get the base url to use.
     *
     * @return boolean
     */
    protected function getBaseURL()
    {
        if($this->sandboxMode) {
            return self::$gatewaySandboxHost;
        } else {
            return self::$gatewayHost;
        }
    }

    /**
     * get the base url to use.
     *
     * @return boolean
     */
    protected function getBaseServiceURL()
    {
        if($this->sandboxMode) {
            return self::$servicesSandboxHost;
        } else {
            return self::$servicesHost;
        }
    }

    /**
     * Set the default return URL.
     *
     * @param $url string
     * @return void
     */
    public function setReturnUrl($url,$extra = array())
    {
        $final = $url;
        if(is_array($extra) && count($extra)>0) {
            $final = $this->parseURL($url,$extra);
        }
        $this->returnURL = $final;
    }

    /**
     * Get the current return url.
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnURL;
    }

    /**
     *
     * @param string $currency
     * @param integer
     */
    protected function getCurrencyDecimalPoints($currency)
    {
        $decimalPoint  = 2;
        $arrCurrencies = array(
            'JOD' => 3,
            'KWD' => 3,
            'OMR' => 3,
            'TND' => 3,
            'BHD' => 3,
            'LYD' => 3,
            'IQD' => 3,
        );
        if (isset($arrCurrencies[$currency])) {
            $decimalPoint = $arrCurrencies[$currency];
        }
        return $decimalPoint;
    }

    /**
     * Convert Amount with dicemal points
     *
     * @param float $amount
     * @param string  $currencyCode
     * @return integer
     */
    protected function convertFortAmount($amount, $currencyCode)
    {
        $new_amount = 0;
        $total = $amount;
        $decimalPoints    = $this->getCurrencyDecimalPoints($currencyCode);
        $new_amount = round($total, $decimalPoints) * (pow(10, $decimalPoints));
        // converting double to integer
        $new_amount = intval(strval($new_amount));
        return $new_amount;
    }

    /**
     * Type case the amount
     *
     * @param decimal $amount
     * @param string  $currencyCode
     * @return decimal
     */
    protected function castAmountFromFort($amount, $currencyCode)
    {
        $decimalPoints    = $this->getCurrencyDecimalPoints($currencyCode);
        $new_amount = round($amount, $decimalPoints) / (pow(10, $decimalPoints));
        return $new_amount;
    }

    /**
     * Trace the log of all transactions done.
     *
     * @var $message string
     * @return void
     */
    protected function log($messages) {
        $messages = "========================================================\n\n".$messages."\n\n";
        $file = getcwd().'/trace.log';
        if(file_exists($file)) {
            if (filesize($file) > 907200) {
                $fp = fopen($file, "r+");
                ftruncate($fp, 0);
                fclose($fp);
            }
        }
        $myfile = fopen($file, "a+");
        fwrite($myfile, $messages);
        fclose($myfile);
    }

    /**
     * DEPRECATED. This function is not required if setMerchantReference() is being used.
     */
    /*
    protected function generateMerchantReference() {
      return rand(0, 9999999999);
    }
    */

    /**
     * parse the url
     */
    public function parseURL($url,$arr) {
        $url_parts = parse_url($url);

        if(isset($url_parts['query']))
            parse_str($url_parts['query'], $params);

        foreach($arr as $k=>$v) {
            $params[$k]=$v;
        }

        // Note that this will url_encode all values
        $url_parts['query'] = http_build_query($params);

        // Add port if needed
        $port = ( isset($url_parts['port']) &&  $url_parts['port'] != 80) ? ':' . $url_parts['port'] : '';

        return $url_parts['scheme'] . '://' . $url_parts['host'] . $port . $url_parts['path'] . '?' . $url_parts['query'];

    }

    /**
     * calculate fort signature
     * @param array $arrData
     * @param string $signType request or response as the expected values
     * @return string fort signature
     */
    protected function calculateSignature($arrData, $signType = 'request')
    {
        $shaString = '';
        ksort($arrData);
        foreach ($arrData as $k => $v) {
            $shaString .= "$k=$v";
        }

        if ($signType == 'request') {
            $shaString = $this->getRequestPhrase() . $shaString . $this->getRequestPhrase();
        }
        else if($signType == 'response') {
            $shaString = $this->getResponsePhrase() . $shaString . $this->getResponsePhrase();
        } else {
            throw new InvalidArgumentException('Error in signature calculation : the possible sign type must be either response or request.');
        }

        $debugMsg = "Signature String to be calcuated : \n".$shaString;
        $this->log($debugMsg);

        $signature = hash($this->hashType, $shaString);

        return $signature;
    }

    /**
     * Send host to host request to the Fort utilizing the API call.
     * @param array $postData
     * @param string $gatewayUrl
     * @return mixed
     */
    protected function callApi($postData, $gatewayUrl)
    {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8'));

        curl_setopt($ch, CURLOPT_URL, $gatewayUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_ENCODING, "compress, gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); // The number of seconds to wait while trying to connect

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        //exec curl
        $response = curl_exec($ch);

        curl_close($ch);

        $array_result = json_decode($response, true);

        if (!$response || empty($array_result)) {
            return false;
        }
        return $array_result;

    }

}