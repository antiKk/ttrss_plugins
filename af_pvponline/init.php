<?php
class Af_pvponline extends Plugin {

	private $host;

	function about() {
		return array(1.0,
			"Embed Comic in PVP Online RSS Feeds", //Feed url http://www.pvponline.com/feed/
			"antiKk");
	}

	function init($host) {
		$this->host = $host;
		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}
	
	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "pvponline.com") !== FALSE && strpos($article["title"], "Comic:") !== FALSE) {
			if (strpos($article["plugin_data"], "pvponline,$owner_uid:") === FALSE) {
		
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));
			
				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query("(//div[@class='post'])");
					
					$matches = array();

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

				if ($basenode) {
						$article["content"] = $doc->saveXML($basenode, LIBXML_NOEMPTYTAG);
						$article["plugin_data"] = "pvponline,$owner_uid:" . $article["plugin_data"];
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