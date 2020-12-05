<?php

namespace Parser;

use ParserInterface;

class JsonProxiesParser implements ParserInterface
{
	public static function parse(string $body): array
	{
		$result = [];
		foreach (explode("\n", $body) as $line) {
			$proxyData = json_decode($line);
			if (!preg_match('!\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d+!', $ipPort = "{$proxyData->host}:{$proxyData->port}"))
				continue;
			$result[] = $ipPort;
		}
		return $result;
	}
}
