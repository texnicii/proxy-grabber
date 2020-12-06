<?php

namespace Texnicii;

interface ParserInterface
{
	public static function parse(string $body): array;
}
