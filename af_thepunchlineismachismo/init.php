<?php
class Af_Thepunchlineismachismo extends Plugin {

	private $host;

	function about() {
		return array(1.2,
			"Embed Comic in Manly Guys Doing Manly Things RSS Feed",
			"antiKk");
	}

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_FILTER, $this);
	}

	function hook_article_filter($article) {
		$owner_uid = $article["owner_uid"];

		if (strpos($article["link"], "thepunchlineismachismo") !== FALSE) {
		
			$article['content'] = str_replace('img width="150" height="150"', 'img', $article['content']);
			$article['content'] = str_replace('-150x150.', '.', $article['content']);
		
		}

		return $article;
	}
	
	function api_version() {
		return 2;
	}
}
?>
