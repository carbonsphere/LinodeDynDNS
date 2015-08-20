<?php

/**
 * User: CarbonSphere
 * Email: CarbonSphere@gmail.com
 * Date: 8/20/15
 * Time: 12:34 PM
 * Simple Script for updating DNS records
 * Usage: 
 *   php LinodeDynDns.php {domain id} {resource id}
 *
 * If Domain ID or Resource ID is empty, then it will walk you through on obtaining your IDs by displaying
 * All Domain ID and All Resource ID. Once you have obtained your Domain ID & Resource ID, you can run the
 * command again to update your IP.
 * You can also modify this script to use a static DOMAINID and RESOURCEID.
 * Application KEY is Also required!
 */
class linodeAPI
{

    const DOMAINID = '';
    const RESOURCEID = '';
    const KEY = '';

    const IP_URL = "http://echoip.com";

    const API_URL_DOMAINID = "https://api.linode.com/?api_key=%s&api_action=domain.list";
    const API_URL = "https://api.linode.com/api/?api_key=%s&resultFormat=JSON&action=domainResourceGet&%s";
    const API_URL_UPDATE = "https://api.linode.com/?api_key=%s&api_action=domain.resource.update&domainid=%d&resourceid=%d&target=%s";
    const API_URL_RESOURCE = "https://api.linode.com/?api_key=%s&api_action=domain.resource.list&domainid=%s";
    const GET_IP_URL = "http://echoip.com";

    private $_api_url_domainid;
    private $_api_url_domainresource;

    private $_api_url;
    private $_domainid;
    private $_url_update;

    private $_post_data_template = array(
        "authenticity_token" => '',
        "type" => 'a',
        "name" => '',
        "target" => '',
        "ttl_sec" => 0
    );
    private $_post_data;

    function __construct($domainid, $resourceid)
    {
        if (!self::KEY) {
            echo "Error: Please enter your Application key into this script.\n";
            exit;
        }
        if (!$domainid) {
            $domainid = self::DOMAINID;
        }
        if (!$resourceid) {
            $resourceid = self::RESOURCEID;
        }

        # Get Domain ID
        if (!$domainid) {
            echo "Error: Please obtain your Domain ID first\nListing Domains:\n";
            # Get Domain ID
            $this->_api_url_domainid = sprintf(self::API_URL_DOMAINID, self::KEY);
            $domIDout = $this->_getURL($this->_api_url_domainid);
            $domIDout = $this->_parseDomainID($domIDout);
//            var_dump($domIDout);
            echo "Run the command again with your domain ID\n";
            exit;
        }

        # Get Resource ID
        if (!$resourceid) {
            echo "Error: Please obtain your resource ID first\nListing resources";
            $this->_api_url_domainresource = sprintf(self::API_URL_RESOURCE, self::KEY, $domainid);
            $domRes = $this->_getURL($this->_api_url_domainresource);
            $domRes = $this->_parseResourceID($domRes);
//            var_dump($domRes);
            echo "Run the command again with your Resource ID\n";
            exit;
        }
        $ip = $this->_getip();
        # Display public IP
        print "Your public IP = $ip\n";

        # Set IP
        $this->_url_update = sprintf(self::API_URL_UPDATE, self::KEY, $domainid, $resourceid, $ip);

        $o = $this->_getURL($this->_url_update);
        $this->_parseUpdate($o);
    }

    private function _parseDomainID($output)
    {
        $json = json_decode($output);
        $outArray = array();
        foreach ($json->DATA as $data) {
            $a = array('domain' => $data->DOMAIN, 'id' => $data->DOMAINID);
            print("Domain: $data->DOMAIN\nDomain ID: $data->DOMAINID\n");
            array_push($outArray, $a);
        }
        return $outArray;
    }

    private function _getip()
    {
        return $this->_getURL(self::IP_URL);
    }

    private function _parseResourceID($output)
    {
        $json = json_decode($output);
        $outArray = array();
        foreach ($json->DATA as $data) {
            $n = $data->NAME;
            $a = array('Name' => $n, 'id' => $data->RESOURCEID);
            print("\nName: $n\nResource ID: $data->RESOURCEID\n");
            array_push($outArray, $a);
        }
        return $outArray;
    }

    private function _getURL($url = null)
    {
        if (!$url) {
            echo "Error: URL is empty";
            exit;
        }

        $c = curl_init($url);
        //return the transfer as a string
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        // $output contains the output string
        $output = curl_exec($c);
        // close curl resource to free up system resources

        curl_close($c);
        return $output;
    }

    private function _parseUpdate($result)
    {
        $json = json_decode($result);

        foreach ( $json->ERRORARRAY as $err ) {
            print "\nError Message: $err->ERRORMESSAGE\n";
        }

        if(!count($json->ERRORARRAY)) {
            print "Update Complete\n";
        }

    }

}

$a = new linodeAPI(@$argv[1], @$argv[2]);

