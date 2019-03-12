<?php

namespace PhpCoda\LaravelCoda\Api;

use Carbon\Carbon;
use PhpJunior\Laravel2C2P\Encryption\Encryption;

include('InquiryPaymentResult.php');
include('InquiryPaymentRequest.php');
include('InitTxnResult.php');
include('ItemInfo.php');
include('Obj2xml.php');

class PaymentGatewayApi
{
    private $config;

    /**
     * PaymentGatewayApi constructor.
     *
     * @param $config
     * @param $encryption
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param array $input
     *
     * @return mixed|string
     */
    public function CodaRequest(array $input)
    {

        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);

        $request = new InitTxnRequest;
        $request->country = $this->config->get('laravel-coda.country');
        $request->currency = $this->config->get('laravel-coda.currency');
        $request->orderId = (string) round(microtime(true) * 1000);
        $request->apiKey = $this->config->get('laravel-coda.apiKey');
        $request->payType = $this->config->get('laravel-coda.txnType');
        $request->items = $this->getItems ($input);
        /*$arrProfile = array(
          "entry" => array(
            "key" => "need_mno_id",
            "value" => "Yes"
            )
          );
        $request->profile = $arrProfile;*/

        $result = $this->initTxn($request);

        echo $result->txnId;

    }

    public function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public function getItems($httpRequest) {
        $itemList = array();

        foreach ($_REQUEST as $key=>$value) {

            if ($this->startsWith( $key, "Item") ) {
                $vals = explode ("_", $value);

                $code = $vals[0];
                $price = $vals[1];

                $item = new ItemInfo;
                $item->name = $key;
                $item->code = $code;
                $item->price = (double) $price;
                $item->type = '';

                array_push ($itemList, $item);
            }
        }

        return $itemList;
    }

    public function inquiryPayment ($txnId) {
        if ($this->config->get('laravel-coda.requestType') == 'xml') {
            return self::inquiryPaymentXML($txnId);
        } else {
            return self::inquiryPaymentJSON($txnId);
        }
    }

    public function inquiryPaymentJSON ($txnId) {
        $WebServiceURL = $this->config->get('laravel-coda.airtimeRestURL') . "/inquiryPaymentResult/";
        $headers = array("Content-Type: application/json","Accept: application/json");

        $request = new InquiryPaymentRequest;
        $request->txnId = $txnId;
        $request->apiKey = $this->config->get('laravel-coda.apiKey');

        $json = json_encode(  array("inquiryPaymentRequest"=> $request) );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $WebServiceURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $responseText = curl_exec($ch);
        $reader = json_decode($responseText, false, 512, JSON_BIGINT_AS_STRING);

        $response = new InquiryPaymentResult;
        $response->resultCode = $reader->paymentResult->{'resultCode'};
        $response->txnId = $reader->paymentResult->{'txnId'};
        $response->orderId = $reader->paymentResult->{'orderId'};
        $response->resultDesc = $reader->paymentResult->{'resultDesc'};
        $response->totalPrice = $reader->paymentResult->{'totalPrice'};

        return $response;
    }

    public function inquiryPaymentXML ($txnId) {
        $WebServiceURL = $this->config->get('laravel-coda.airtimeRestURL') . "/inquiryPaymentResult/";
        $headers = array("Content-Type: application/xml","Accept: application/xml");

        $request = new InquiryPaymentRequest;
        $request->txnId = $txnId;
        $request->apiKey = $this->config->get('laravel-coda.apiKey');

        $converter=new Obj2xml("inquiryPaymentRequest");
        $xml = $converter->toXml($request);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $WebServiceURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $responseText = curl_exec($ch);

        $xml = simplexml_load_string($responseText);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        $response = new InquiryPaymentResult;
        $response->orderId = $array['orderId'];
        $response->txnId = $array['txnId'];
        $response->resultCode = $array['resultCode'];
        $response->resultDesc = $array['resultDesc'];
        $response->totalPrice = $array['totalPrice'];

        return $response;
    }

    public function initTxn ($txnId) {
        if ($this->config->get('laravel-coda.requestType') == 'xml') {
            return self::initTxnXML($txnId);
        } else {
            return self::initTxnJSON($txnId);
        }
    }

    public function initTxnJSON ($request) {
        $WebServiceURL = $this->config->get('laravel-coda.airtimeRestURL') . "/init/";
        $headers = array("Content-Type: application/json","Accept: application/json");

        $json = json_encode(  array("initRequest"=> $request) );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $WebServiceURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $responseText = curl_exec($ch);

        $reader = json_decode($responseText, false, 512, JSON_BIGINT_AS_STRING);

        $response = new InitTxnResult;
        $response->resultCode = $reader->initResult->{'resultCode'};
        $response->txnId = $reader->initResult->{'txnId'};

        if ( (int) $response->resultCode > 0) {
            $response->resultDesc = $result->initResult->{'resultDesc'};
        }

        return $response;
    }

    public static function validateChecksum ($httpRequest) {
        try {
            $txnId = $httpRequest["TxnId"];
            $apiKey = ""; // Merchant APIKey
            $orderId = $httpRequest["OrderId"];
            $resultCode = $httpRequest["ResultCode"];
            $checksum = $httpRequest["Checksum"];

            $values = $txnId . $apiKey . $orderId . $resultCode;

            $sum = md5($values);

            return ($sum == $checksum);
        } catch (Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }

        return false;
    }

    public static function strToHex($string)
    {
        $hex='';
        for ($i=0; $i < strlen($string); $i++)
        {
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }

    public function initTxnXML ($request) {
        $WebServiceURL = $this->config->get('laravel-coda.airtimeRestURL') . "/init/";
        $headers = array("Content-Type: application/xml","Accept: application/xml");

        $converter=new Obj2xml("initRequest");
        $xml = $converter->toXml($request);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $WebServiceURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $responseText = curl_exec($ch);


        $xml = simplexml_load_string($responseText);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        $response = new InitTxnResult;
        $response->txnId = $array['txnId'];
        $response->resultCode = $array['resultCode'];

        if ( (int) $response->resultCode > 0) {
            $response->resultDesc = $array['resultDesc'];
        }

        return $response;
    }

}
