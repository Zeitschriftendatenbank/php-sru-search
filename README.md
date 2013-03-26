php-sru-search
==============

A very simple PHP SRU search client

##Examples

    <?php
     require_once("SRUSearch.php");
     $search = new SRUSearch();
     $search->setBaseURL("http://services.d-nb.de/sru/zdb");
     $search->setVersion("1.1");
     $search->setRecordSchema("MARC21-xml");
     $search->setProxyURL("http://my.http-proxy.de"); // curl is automatically turned on 
     $search->setProxyPort("3333");
     $response = $search->searchRetrieve(urlencode("title=European journal of soil biology"));
     echo $response;
    ?>


    <?php
     require_once("SRUSearch.php");
     $search = new SRUSearch();
     $search->init("http://services.d-nb.de/sru/zdb","1.1","MARC21-xml");
     $response = $search->searchRetrieve(urlencode("title=European journal of soil biology"));
     echo $response;
    ?>
	
    <?php
     require_once("SRUSearch.php");
     $search = new SRUSearch();
     $search->setBaseURL("http://services.d-nb.de/sru/zdb");
     $search->setVersion("1.1");
     $search->setRecordSchema("MARC21-xml");
	 $search->setRecordPacking("xml");		
	 $search->setStartRecord(11);		
	 $search->setMaximumRecords(10);
     $response = $search->searchRetrieve(urlencode("title=European journal of soil biology"));
     echo $response;
    ?>
