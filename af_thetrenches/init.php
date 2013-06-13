<?php
class Af_TheTrenches extends Plugin {

	private $host;

	function about() {
		return array(1.0,
			"Embed Comic in The Trenches RSS Feeds", //Feed url http://trenchescomic.com/feed
			"antiKk");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "trenchescomic.com") !== FALSE && strpos($article["title"], "Comic:") !== FALSE) {
			if (strpos($article["plugin_data"], "thetrenches,$owner_uid:") === FALSE) {

				if ($debug_enabled) {
					_debug("af_thetrenches: Processing comic");
				}

				$doc = new DOMDocument();
				$doc->loadHTML(fetch_file_contents($article["link"]));

				$basenode = false;

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query("(//img[contains(@src,'art.penny-arcade.com')])");

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

					if ($basenode) {
						$article["content"] = $doc->saveXML($basenode);
						$article["plugin_data"] = "thetrenches,$owner_uid:" . $article["plugin_data"];
					}
				}
			} else if (isset($article["stored"]["content"])) {
				$article["content"] = $article["stored"]["content"];
			}
		}

		if (strpos($article["link"], "trenchescomic.com") !== FALSE && strpos($article["title"], "Tales From The Trenches:") !== FALSE) {
			if (strpos($article["plugin_data"], "thetrenches,$owner_uid:") === FALSE) {
				if ($debug_enabled) {
					_debug("af_thetrenches: Processing news post");
				}
				$doc = new DOMDocument();
				$doc->loadHTML(fetch_file_contents($article["link"]));

				if ($doc) {
					$xpath = new DOMXPath($doc);
					$entries = $xpath->query('(//div[@class="copy"])');

					$basenode = false;

					foreach ($entries as $entry) {
						$basenode = $entry;
					}

					if ($basenode){
						$article["content"] = $doc->saveXML($basenode);
						$article["plugin_data"] = "thetrenches,$owner_uid:" . $article["plugin_data"];
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
