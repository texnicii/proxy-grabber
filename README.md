## Basic usage

Grab and parse from all available sources (*see proxy_sources.json*)
```PHP
$ProxyScraper = new ProxyScraper();
print_r($ps->all());
```

Add new source to the sources list
```PHP
$ProxyScraper->add("SOURCE_URL", "type (http|socks4|socks5)");
```

Grab and parse from some source
```PHP
$ProxyScraper->get("SOURCE_URL");
```