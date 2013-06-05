<?php
class Af_YellowPeril extends Plugin {

	private $host;

	function about() {
		return array(1.1,
			"Embed Comic in Yellow Peril RSS Feed", //Feed url http://feeds.feedburner.com/YellowPeril
			"antiKk");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}
	
	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];
		$originalarticle = $article["content"];

		if (strpos($article["link"], "ypcomic.com") !== FALSE && strpos($article["title"], "[Comic]") !== FALSE) {
			if (strpos($article["plugin_data"], "ypcomic,$owner_uid:") === FALSE) {
		
				$doc = new DOMDocument();
				@$doc->loadHTML(fetch_file_contents($article["link"]));
			
				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query("(//div[@id='comic'])");
					
					$matches = array();

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

				if ($basenode) {
						$article["content"] = $doc->saveXML($basenode, LIBXML_NOEMPTYTAG);
						$article["plugin_data"] = "ypcomic,$owner_uid:" . $article["plugin_data"];
					}
				}
			} else if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
				$article["content"] = $article["content"].$originalarticle;
			}
		}
		return $article;
	}
	
	function api_version() {
		return 2;
	}
}
?>
