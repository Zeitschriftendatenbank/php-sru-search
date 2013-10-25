<?php 
/*
 * Very Simple PHP SRU Search Client
 * 
 * by Carsten Klee <carsten.klee@sbb.spk-berlin.de>
 * 
 * Example:
 * $search = new SRUSearch("http://services.d-nb.de/sru/zdb","1.1","MARC21-xml");
 * $response = $search->searchRetrieve(urlenocde("title=European journal of soil biology"));
 */

namespace CK\SRUSearch;

class SRUSearch {
	private $baseURL;
	private $version;
	private $recordSchema;
	private $recordPacking;
	private $startRecord;
	private $maximumRecords;
	private $proxy_url;
	private $proxy_port;
	public $searchURL;
	public $curluse = false;
	
	/**
	 * Set the base url
	 * @param string $url The base URL of the SRU service
	 */
	private function setBaseURL($url){
		$this->baseURL = $url;
	}
	/**
	* Set the SRU Version
	* @param	string $v The value for SRU param version
	*/
	private function setVersion($v){
		$this->version = $v;
	}
	/**
	* Set the record schema to return
	* @param string $schema The value for SRU param recordSchema
	*/
	private function setRecordSchema($schema){
		$this->recordSchema = $schema;
	}	
	/**
	* Set the record packing
	* @param string $packing The value for SRU param recordPacking
	*/
	private function setRecordPacking($packing){
		$this->recordPacking = $packing;
	}	
	/**
	* Set the start record number
	* @param int $start The value for SRU param startRecord
	*/
	private function setStartRecord($start){
		$this->startRecord = $start;
	}	
	/**
	* Set the maximum record number to return
	* @param int $maximum The value for SRU param maximumRecords
	*/
	private function setMaximumRecords($maximum){
		$this->maximumRecords = $maximum;
	}
	/**
	* Set the url of a proxy if one
	* @param string $purl The proxy URL
	*/
	private function setProxyURL($purl){
		$this->proxy_url = $purl;
		if(isset($this->proxy_url)) $this->setCurlUse(true);
	}
	/**
	* Set the proxy port if one
	* @param int $pp The proxy port
	*/
	private function setProxyPort($pp){
		$this->proxy_port = $pp;
	}
	/**
	* Set the to true to use curluse
	*  @param bool $bool TRUE or FALSE to enable/disable curl use
	*/
	private function setCurlUse(bool $bool){
		$this->curluse = $bool;
	}
	/**
	* internal method to build the url
	* @param string $q The url-encoded searchstring
	*/	
	private function buildSearchURL($q){
		$this->searchURL = $this->baseURL;
		$this->searchURL .= "?version=".$this->version;
		$this->searchURL .= "&recordSchema=".$this->recordSchema;
		$this->searchURL .=  "&query=".$q;
		$this->searchURL .=  "&operation=searchRetrieve";
		// optinal parameters
		if(isset($this->recordPacking)) $this->searchURL .= "&recordPacking=".$this->recordPacking;
		if(isset($this->startRecord)) $this->searchURL .= "&startRecord=".$this->startRecord;
		if(isset($this->maximumRecords)) $this->searchURL .= "&maximumRecords=".$this->maximumRecords;
	}
	/**
	* Set all properties when class is initiated
	* @param string $url
	* @param string $version
	* @param string $schema
	* @param null|string $purl
	* @param null|int $pp default
	* @param null|string $packing
	* @param null|int $start
	* @param null|int $maximum
	*/
	public function __construct($url,$version,$schema,$purl=null,$pp=null,$packing=null,$start=null,$maximum=null){
		$this->setBaseURL($url);
		$this->setVersion($version);
		$this->setRecordSchema($schema);
		$this->setRecordPacking($packing);
		$this->setStartRecord($start);
		$this->setMaximumRecords($maximum);
		$this->setProxyURL($purl);
		$this->setProxyPort($pp);
	}
	/**
	* init the search
	* @param string $query The query string url-encoded
	*/
	public function searchRetrieve($query){
		$this->buildSearchURL($query);
		return $this->_search();
	}
	/**
	 * internal method that does the search
	 * 
	 * @return stream $output The SRU response
	 */
	private function _search(){
		if($this->curluse){
			try{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->searchURL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				if(isset($this->proxy_url) && isset($this->proxy_port)){
					curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
					curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
					curl_setopt($ch, CURLOPT_PROXY, $this->proxy_url);
				}
				$output = curl_exec($ch);
				if(curl_errno($ch))
				{
					throw new \Exception(curl_error($ch));
				}
				curl_close($ch);
				return $output;
			} catch(Exception $e){
				echo $e->getMessage();
			}
		}
		else {
			if ($stream = fopen($this->searchURL, 'r')) {
				return stream_get_contents($stream);
				fclose($stream);
			}
		}
	}
}