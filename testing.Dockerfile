FROM webdevops/php-nginx-dev:8.0-alpine

ENV PATH /root/.composer/vendor/bin/:$PATH

RUN composer global require "phpunit/phpunit"

