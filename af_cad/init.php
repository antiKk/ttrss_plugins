<?php
class Af_CAD extends Plugin {

	private $host;

	function about() {
		return array(1.1,
			"Strip unnecessary stuff from all Ctrl+Alt+Del feeds",
			"Phranx");
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
					$entries = $xpath->query('(//img[@src])'); // we might also check for img[@class='strip'] I guess...

					$matches = array();

					foreach ($entries as $entry) {

						if (preg_match("/(http:\/\/v.cdn.cad-comic.com\/comics\/.*)/i", $entry->getAttribute("src"), $matches)) {
							
							$basenode = $entry;
							break;
						}
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