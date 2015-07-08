FROM debian:jessie

ENV SLASHCOMMAND_TOKEN **TOKEN**
ENV WEBHOOK_URL **URL**

RUN \
  apt-get update && \
  apt-get install -y \
  curl \
  wget \
  git \
  php5-cli \
  php5-curl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    echo Europe/Brussels > /etc/timezone && dpkg-reconfigure --frontend noninteractive tzdata && \
    echo "date.timezone = Europe/Brussels" > /etc/php5/cli/conf.d/php.ini && \
    mkdir /archibald

ADD api.php /archibald/
ADD composer.json /archibald/
ADD src /archibald/src
WORKDIR /archibald

RUN composer install --prefer-source -o

RUN echo "<?php\ndefine('SLASHCOMMAND_TOKEN', '$SLASHCOMMAND_TOKEN');\ndefine('WEBHOOK_URL', '$WEBHOOK_URL');" > /archibald/config.php

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80"]