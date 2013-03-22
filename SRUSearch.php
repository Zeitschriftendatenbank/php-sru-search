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
	public $query;
	public $proxy_url;
	public $proxy_port;
	public $searchURL;
	
	/*
	 * Set the base url
	 * @param $url String
	 */
	public function setBaseURL($url){
		$this->baseURL = $url;
	}
	/*
	* Set the SRU Version
	* @param $v String
	*/
	public function setVersion($v){
		$this->version = $v;
	}
	/*
	* Set the record schema to return
	* @param $schema String
	*/
	public function setRecordSchema($schema){
		$this->recordSchema = $schema;
	}
	/*
	* Set the url of a proxy if one
	* @param $purl String
	*/
	public function setProxyURL($purl){
		$this->proxy_url = $purl;
	}
	/*
	* Set the proxy port if one
	* @param $pp String
	*/
	public function setProxyPort($pp){
		$this->proxy_port = $pp;
	}
	/*
	* internal method to build the url
	* @param $q String : the url encoded searchstring
	*/	
	private function buildSearchURL($q){
		$this->searchURL = $this->baseURL;
		$this->searchURL .= "?version=".$this->version;
		$this->searchURL .= "&recordSchema=".$this->recordSchema;
		$this->searchURL .=  "&query=".$q;
		$this->searchURL .=  "&operation=searchRetrieve";
	}
	/*
	* Set all properties at once
	* @param $url String
	* @param $version String
	* @param $schema String
	* @param $purl String default is null
	* @param $pp String default is null
	*/
	public function init($url,$version,$schema,$purl=null,$pp=null){
		$this->setBaseURL($url);
		$this->setVersion($version);
		$this->setRecordSchema($schema);		
		$this->setProxyURL($purl);		
		$this->setProxyPort($pp);		
	}
	/*
	* init the search
	* @param $query String : the query string url encoded
	*/	
	public function searchRetrieve($query){
		$this->buildSearchURL($query);
		$this->_search();
	}
	/**
	 * internal method that does the search
	 * @return $output the SRU response
	 */
	private function _search(){
		try{
			// create curl resource
			$ch = curl_init();
			// set url
			curl_setopt($ch, CURLOPT_URL, $this->searchURL);
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

}
?>