docker:
	docker build -t boxyback:docker .

test:
	docker run --rm -it -v ${PWD}:/app -w="/app" boxyback:docker php phpunit.phar

mysql:
	docker run --name boxyback-mysql -e MYSQL_ROOT_PASSWORD=root -d mysql:5.6

backup:
	docker run --rm -it -v ${PWD}:/app -w="/app" --link boxyback-mysql:mysql boxyback:docker ./boxyback backup example.yml

clean:
	docker rm -f boxyback-mysql
