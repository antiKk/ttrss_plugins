<?php
class Af_cad extends Plugin {

	private $host;

	function about() {
		return array(1.2,
			"Embed Comic in Ctrl+Alt+Del RSS Feeds", //Feed url http://www.cad-comic.com/rss/rss.xml
			"antiKk");
	}

	function init($host) {
		$this->host = $host;
		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}
	
	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "cad-comic.com") !== FALSE && strpos($article["title"], "News:") === FALSE) {
			if (strpos($article["plugin_data"], "cad-comic,$owner_uid:") === FALSE) {
		
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));
			
				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query("(//img[contains(@src,'/comics')])");
					
					$matches = array();

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

				if ($basenode) {
						$article["content"] = $doc->saveXML($basenode, LIBXML_NOEMPTYTAG);
						$article["plugin_data"] = "cad-comic,$owner_uid:" . $article["plugin_data"];
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