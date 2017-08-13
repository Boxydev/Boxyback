test:
	docker run --rm -it -v ${PWD}:/app -w="/app" php:7.1.8-cli php phpunit.phar