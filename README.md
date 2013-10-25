# php-sru-search

A very simple PHP SRU search client

## Usage

```php
require_once("SRUSearch.php");
use CK\SRUSearch;
$search = new CK\SRUSearch\SRUSearch("http://services.d-nb.de/sru/zdb","1.1","MARC21-xml","http://my.http-proxy.de",3333,null,1,10);
echo $search->searchRetrieve(urlencode("title=European journal of soil biology"));
```

## params for SRUSearch
* @param string $url The base URL of the SRU service
* @param string $version The value for SRU param version
* @param string $schema he value for SRU param recordSchema
* @param null|string $purl The proxy URL
* @param null|int $pp default The proxy port
* @param null|string $packing The value for SRU param recordPacking
* @param null|int $start The value for SRU param startRecord
* @param null|int $maximum The value for SRU param maximumRecords
