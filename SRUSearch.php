<?php 
/**
 * Very Simple PHP SRU Search Client
 * Author: Carsten Klee @ Zeitschriftendatenbank
 * Usage Example:
 * $search = new SRUSearch();
 * $search->setBaseURL("http://services.d-nb.de/sru/zdb");
 * $search->setVersion("1.1");
 * $search->setRecordSchema("MARC21-xml");
 * $response = $search->searchRetrieve(urlenocde("title=European journal of soil biology"));
 */

class SRUSearch {
	public $baseURL;
	public $version;
	public $recordSchema;
	public $recordPacking;
	public $startRecord;
	public $maximumRecords;
	public $query;
	public $proxy_url;
	public $proxy_port;
	public $searchURL;
	public $curluse = false;
	
	/*
	 * Set the base url
	 * @param $url string
	 */
	public function setBaseURL($url){
		$this->baseURL = $url;
	}
	/*
	* Set the SRU Version
	* @param $v string
	*/
	public function setVersion($v){
		$this->version = $v;
	}
	/*
	* Set the record schema to return
	* @param $schema string
	*/
	public function setRecordSchema($schema){
		$this->recordSchema = $schema;
	}	
	/*
	* Set the record packing
	* @param $packing string
	*/
	public function setRecordPacking($packing){
		$this->recordPacking = $packing;
	}	
	/*
	* Set the start record number
	* @param $start int
	*/
	public function setStartRecord($start){
		$this->startRecord = $start;
	}	
	/*
	* Set the maximum record number to return
	* @param $maximum int
	*/
	public function setMaximumRecords($maximum){
		$this->maximumRecords = $maximum;
	}
	/*
	* Set the url of a proxy if one
	* @param $purl string
	*/
	public function setProxyURL($purl){
		$this->proxy_url = $purl;
		if(isset($this->proxy_url)) $this->setCurlUse(true);
	}
	/*
	* Set the proxy port if one
	* @param $pp int
	*/
	public function setProxyPort($pp){
		$this->proxy_port = $pp;
	}	
	/*
	* Set the to true to use curluse
	*  @param $bool bool
	*/
	public function setCurlUse($bool){
		$this->curluse = $bool;
	}
	/*
	* internal method to build the url
	* @param $q string : the url encoded searchstring
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
	/*
	* Set all properties at once
	* @param $url string
	* @param $version string
	* @param $schema string
	* @param $purl string default is null
	* @param $pp string default is null
	*/
	public function init($url,$version,$schema,$purl=null,$pp=null,$packing=null,$start=null,$maximum=null){
		$this->setBaseURL($url);
		$this->setVersion($version);
		$this->setRecordSchema($schema);		
		$this->setRecordPacking($packing);		
		$this->setStartRecord($start);		
		$this->setMaximumRecords($maximum);
		$this->setProxyURL($purl);
		$this->setProxyPort($pp);		
	}
	/*
	* init the search
	* @param $query string : the query string url encoded
	*/	
	public function searchRetrieve($query){
		$this->buildSearchURL($query);
		return $this->_search();
	}
	/**
	 * internal method that does the search
	 * @return $output the SRU response
	 */
	private function _search(){
		if($this->curluse){
			try{
				// create curl resource
				$ch = curl_init();
				// set url
				curl_setopt($ch, CURLOPT_URL, $this->searchURL);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				if(isset($this->proxy_url) && isset($this->proxy_port)){
					// set Proxy
					curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
					curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy_port);
					curl_setopt($ch, CURLOPT_PROXY, $this->proxy_url);
				}
				// $output contains the output string
				$output = curl_exec($ch);
				if(curl_errno($ch))
				{
					throw new Exception(curl_error($ch));
				}
				// close curl resource to free up system resources
				curl_close($ch);
				// return output
				return $output;
			} catch(Exception $e){
				echo $e->getMessage();
				die;
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
?>