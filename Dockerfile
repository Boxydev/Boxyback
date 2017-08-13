FROM php:7.1.8-cli
MAINTAINER Matthieu Mota <matthieumota@gmail.com>

RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y \
  mysql-client
