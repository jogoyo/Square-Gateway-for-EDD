<?php
/**
 * Money
 *
 * PHP version 5
 *
 * @category Class
 * @package  SquareConnect
 * @author   http://github.com/swagger-api/swagger-codegen
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link     https://github.com/swagger-api/swagger-codegen
 */
/**
 *  Copyright 2016 Square, Inc.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */
/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace SquareConnect\Model;

use \ArrayAccess;
/**
 * Money Class Doc Comment
 *
 * @category    Class
 * @description Represents an amount of money.  __Important:__ Unlike version 1 of the Connect API, __all monetary amounts returned by v2 endpoints are positive.__ (In v1, monetary amounts are negative if they represent money being paid _by_ a merchant, instead of money being paid _to_ a merchant.)
 * @package     SquareConnect
 * @author      http://github.com/swagger-api/swagger-codegen
 * @license     http://www.apache.org/licenses/LICENSE-2.0 Apache Licene v2
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Money implements ArrayAccess
{
    /**
      * Array of property to type mappings. Used for (de)serialization 
      * @var string[]
      */
    static $swaggerTypes = array(
        'amount' => 'int',
        'currency' => 'string'
    );
  
    /** 
      * Array of attributes where the key is the local name, and the value is the original name
      * @var string[] 
      */
    static $attributeMap = array(
        'amount' => 'amount',
        'currency' => 'currency'
    );
  
    /**
      * Array of attributes to setter functions (for deserialization of responses)
      * @var string[]
      */
    static $setters = array(
        'amount' => 'setAmount',
        'currency' => 'setCurrency'
    );
  
    /**
      * Array of attributes to getter functions (for serialization of requests)
      * @var string[]
      */
    static $getters = array(
        'amount' => 'getAmount',
        'currency' => 'getCurrency'
    );
  
    /**
      * $amount The amount of money, in the smallest denomination of the currency indicated by `currency`. For example, when `currency` is `USD`, `amount` is in cents.
      * @var int
      */
    protected $amount;
    /**
      * $currency The type of currency, in __ISO 4217 format__. For example, the currency code for US dollars is `USD`.  See [Currency](#type-currency) for possible values.
      * @var string
      */
    protected $currency;

    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {
        if ($data != null) {
            $this->amount = $data["amount"];
            $this->currency = $data["currency"];
        }
    }
    /**
     * Gets amount
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }
  
    /**
     * Sets amount
     * @param int $amount The amount of money, in the smallest denomination of the currency indicated by `currency`. For example, when `currency` is `USD`, `amount` is in cents.
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
    /**
     * Gets currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
  
    /**
     * Sets currency
     * @param string $currency The type of currency, in __ISO 4217 format__. For example, the currency code for US dollars is `USD`.  See [Currency](#type-currency) for possible values.
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     * @param  integer $offset Offset 
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }
  
    /**
     * Gets offset.
     * @param  integer $offset Offset 
     * @return mixed 
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }
  
    /**
     * Sets value based on offset.
     * @param  integer $offset Offset 
     * @param  mixed   $value  Value to be set
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }
  
    /**
     * Unsets offset.
     * @param  integer $offset Offset 
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
  
    /**
     * Gets the string presentation of the object
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) {
            return json_encode(\SquareConnect\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        } else {
            return json_encode(\SquareConnect\ObjectSerializer::sanitizeForSerialization($this));
        }
    }
}
