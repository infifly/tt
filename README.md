简单php框架

　简单，０学习成功．

composer.json 如下

{
	"name": "test",
    "minimum-stability": "stable",
    "require": {
    	"php": ">=5.4.0",
        "infifly/tt":"*"
    },
    "autoload": {
    	"psr-4": {"app\\":"tt"}
    },
    "config": {
        "secure-http": false
    },

    "repositories": [
        {"type": "composer", "url": "http://packagist.phpcomposer.com"},
        {"packagist": false}
    ]
}
