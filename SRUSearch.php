<?php 
/**
 * Very Simple PHP SRU Search Client
 * 
 * @author  Carsten Klee <carsten.klee@sbb.spk-berlin.de>
 * @example 
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
	
	/**
	 * Set the base url
	 * @param	string	$url	the base URL of the SRU service
	 */
	public function setBaseURL($url){
		$this->baseURL = $url;
	}
	/**
	* Set the SRU Version
	* @param	string	$v	the value of param version
	*/
	public function setVersion($v){
		$this->version = $v;
	}
	/**
	* Set the record schema to return
	* @param	string	$schema	the value of param recordSchema
	*/
	public function setRecordSchema($schema){
		$this->recordSchema = $schema;
	}	
	/**
	* Set the record packing
	* @param	string	$packing	the value of param recordPacking
	*/
	public function setRecordPacking($packing){
		$this->recordPacking = $packing;
	}	
	/**
	* Set the start record number
	* @param	int	$start	the value of param startRecord
	*/
	public function setStartRecord($start){
		$this->startRecord = $start;
	}	
	/**
	* Set the maximum record number to return
	* @param	int	$maximum	the value of param maximumRecords
	*/
	public function setMaximumRecords($maximum){
		$this->maximumRecords = $maximum;
	}
	/**
	* Set the url of a proxy if one
	* @param	string	$purl	a proxy URL
	*/
	public function setProxyURL($purl){
		$this->proxy_url = $purl;
		if(isset($this->proxy_url)) $this->setCurlUse(true);
	}
	/**
	* Set the proxy port if one
	* @param	int	$pp	the proxy port
	*/
	public function setProxyPort($pp){
		$this->proxy_port = $pp;
	}	
	/**
	* Set the to true to use curluse
	*  @param	bool	$bool 	true or false to enable/disable curl use
	*/
	public function setCurlUse($bool){
		$this->curluse = $bool;
	}
	/**
	* internal method to build the url
	* @param	string	$q	the url-encoded searchstring
	*/	
	public function buildSearchURL($q){
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
	* Set all properties at once
	* @param	string	$url
	* @param	string	$version
	* @param	string	$schema
	* @param	string	$purl	default is null
	* @param	string	$pp	default is null
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
	/**
	* init the search
	* @param	string	$query	the query string url-encoded
	*/	
	public function searchRetrieve($query){
		$this->buildSearchURL($query);
		return $this->_search();
	}
	/**
	 * internal method that does the search
	 * 
	 * @return		$output	the SRU response
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