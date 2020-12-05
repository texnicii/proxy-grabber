<?php

namespace Parser;

use ParserInterface;

class UniversalProxiesParser implements ParserInterface
{
	public static function parse(string $body): array
	{
		preg_match_all('!\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d+!', $body, $matched);
		return count($matched[0]) ? array_map(function ($a) {
			return trim($a);
		}, $matched[0]) : [];
	}
}
