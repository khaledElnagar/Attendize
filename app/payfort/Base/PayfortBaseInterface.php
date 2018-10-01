<?php

/**
 * Core class for the Payfort PHP library created by Conceptualize.
 * THIS IS THE INTERFACE FOR THE CORE CLASS
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

interface PayfortBaseInterface
{
  /**
   *
   */
  public function setLanguage($lang = 'en');

  /**
   *
   */
  public function getLanguage();

  /**
   *
   */
  public function setHashType($hash);

  /**
   *
   */
  public function getHashType();

  /**
   *
   */
  public function setSandboxMode($mode = true);

  /**
   *
   */
  public function getSandboxMode();

  /**
   *
   */
  public function setServiceCommand($command);

  /**
   *
   */
  public function getServiceCommand();

  /**
   *
   */
  public function setMerchantIdentifier($identifier);

  /**
   *
   */
  public function getMerchantIdentifier();

  /**
   *
   */
  public function setMerchantAccessCode($access);

  /**
   *
   */
  public function getMerchantAccessCode();

  /**
   *
   */
  public function setRequestPhrase($phrase);

  /**
   *
   */
  public function getRequestPhrase();

  /**
   *
   */
  public function setResponsePhrase($phrase);

  /**
   *
   */
  public function getResponsePhrase();

  /**
   *
   */
  public function parseURL($url,$arr);

}
