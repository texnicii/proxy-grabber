<?php

declare(strict_types=1);

use Exceptions\SourcesDoesNotExists;
use Parser\UniversalProxiesParser;

class ProxyScraper
{
	const DEFAULT_SOURCES = __DIR__ . '/proxy_sources.json';
	private $sources = [];

	public function __construct(string $sourcesFilename = self::DEFAULT_SOURCES)
	{
		if (!file_exists($sourcesFilename))
			throw new SourcesDoesNotExists("[$sourcesFilename] does not exist");
		$this->sources = json_decode(file_get_contents($sourcesFilename), true);
	}

	public function get(string $url, string $parserClassName = UniversalProxiesParser::class): array
	{
		$response = $this->httpRequest($url);
		if ($response[0] != 200) return false;
		return $parserClassName::parse($response[1]);
	}

	public function all(): array
	{
		$result = [];
		foreach ($this->fetch() as $source) {
			$proxyList = current($source);
			$type = key($proxyList);
			if (!isset($result[$type])) $result[$type] = [];
			foreach (current($proxyList) as $ipPort) {
				$result[$type][$ipPort] = $ipPort;
			}
		}
		return $result;
	}

	public function fetch(): Generator
	{
		foreach ($this->sources as $key => $source) {
			if (empty($source['parser'])) $source['parser'] = UniversalProxiesParser::class;
			foreach ($this->get($source['url'], $source['parser']) as $proxy) {
				$result[$proxy] = $proxy;
			}
			yield [$key => [$source['type'] => $result]];
		}
	}

	public function add(string $url, string $type, string $parserClassName = UniversalProxiesParser::class) // TODO - unit test
	{
		$this->sources[] = [
			"url" => $url,
			"type" => $type,
			"parser" => $parserClassName
		];
	}

	/**
	 * HTTP request via CURL
	 *
	 * @param string $url
	 * @return array e.g. [200, 'response body']
	 */
	public function httpRequest(string $url): array
	{
		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_USERAGENT => "Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/40.0.2214.93 Safari/537.36",
			CURLOPT_TIMEOUT => 60
		]);
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return [$httpCode, $response];
	}

	/**
	 * Get the value of sources
	 */
	public function getSources()
	{
		return $this->sources;
	}
}
