<?php
class Af_tabletitans extends Plugin {

	private $host;

	function about() {
		return array(1.0,
			"Embed Comic in Table Titans RSS Feeds", //Feed url http://www.tabletitans.com/feed
			"antiKk");
	}

	function init($host) {
		$this->host = $host;
		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}
	
	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "tabletitans.com") !== FALSE && strpos($article["title"], "Latest Adventure:") !== FALSE) {
			if (strpos($article["plugin_data"], "tabletitans,$owner_uid:") === FALSE) {
		
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));
			
				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query("(//section[@class='comic row'])");
					
					$matches = array();

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

				if ($basenode) {
						$article["content"] = $doc->saveXML($basenode, LIBXML_NOEMPTYTAG);
						$article["plugin_data"] = "tabletitans,$owner_uid:" . $article["plugin_data"];
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