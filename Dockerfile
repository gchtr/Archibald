FROM debian:jessie

MAINTAINER Joeri Verdeyen <info@jverdeyen.be>

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
ADD run.sh /archibald/
ADD src /archibald/src

WORKDIR /archibald

RUN composer install --prefer-source -o
RUN chmod +x run.sh

EXPOSE 80

CMD ["/archibald/run.sh"]