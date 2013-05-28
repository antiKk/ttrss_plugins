<?php
class Af_Superosity extends Plugin {

	private $host;

	function about() {
		return array(1.2,
			"Embed Comic in Superosity feeds", //Feed url http://superosity.keenspot.com/comic.rss
			"antiKk");
	}

	function init($host) {
		$this->host = $host;
		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}
	
	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "superosity.keenspot.com/d") !== FALSE) {
						
			if (strpos($article["plugin_data"], "keenspot,$owner_uid:") === FALSE) {
		
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));
			
				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query("(//img[@class='ksc'])");
					
					$matches = array();

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

				if ($basenode) {
						$article["content"] = $doc->saveXML($basenode, LIBXML_NOEMPTYTAG);
						$article["plugin_data"] = "keenspot,$owner_uid:" . $article["plugin_data"];
					}
				}
			} else if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
			}
		}
		return $article;
	}
	
	function api_version() {
		return 2;
	}
}
?>