FROM php:7.1-fpm-alpine

RUN apk upgrade --update \
	&& apk add \
	   coreutils \
	   freetype-dev \
	   libjpeg-turbo-dev \
	   libltdl \
	   libmcrypt-dev\
	   libpng-dev \
	   ssmtp \
	&& docker-php-ext-install -j$(nproc) iconv mcrypt \
	&& docker-php-ext-configure gd --with-freetype-dir=/usr/include --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install -j$(nproc) gd \
	&& docker-php-ext-install pdo pdo_mysql
RUN mkdir /sock && chmod -R 777 /sock
RUN addgroup -g 50 camagru
RUN adduser -G camagru -D -u 1000 camagru

VOLUME /sock
RUN sed -i '/;listen.mode = 0660/c\listen.mode = 0777' /usr/local/etc/php-fpm.d/www.conf \
	&& sed -i '/group = www-data/c\group = camagru' /usr/local/etc/php-fpm.d/www.conf \
	&& sed -i '/user = www-data/c\user = camagru' /usr/local/etc/php-fpm.d/www.conf \
	&& sed -i '/listen = \[::\]:9000/c\listen = /sock/php.sock' /usr/local/etc/php-fpm.d/zz-docker.conf
	#	&& sed -i '/listen = 127.0.0.1:9000/c\listen = /sock/php.sock' /usr/local/etc/php-fpm.d/www.conf \

# Set ssmtp.conf :
RUN sed -i '/root=postmaster/c\root=contact@vmonteco.ninja' /etc/ssmtp/ssmtp.conf \
	&& echo "AuthUser=contact@vmonteco.ninja" >> /etc/ssmtp/ssmtp.conf \
	&& echo "AuthPass=42born2code" >> /etc/ssmtp/ssmtp.conf \
	&& echo "mailhub=auth.smtp.1and1.fr:25" >> /etc/ssmtp/ssmtp.conf \
	&& echo "rewriteDomain=vmonteco.ninja" >> /etc/ssmtp/ssmtp.conf \
	&& echo "#UseTLS=YES" >> /etc/ssmtp/ssmtp.conf

# Set revaliases :
RUN echo "root:contact@vmonteco.ninja:auth.smtp.1and1.fr:25" >> /etc/ssmtp/revaliases \
	&& echo "" >> /etc/ssmtp/revaliases \
	&& echo "" >> /etc/ssmtp/revaliases

#RUN echo "SMTP = auth.smtp.1and1.fr\nsmtp_port = 25\nsendmail_from = contact@vmonteco.ninja\n" >> /usr/local/etc/php-fpm.d/zz-docker.conf
#RUN sed -i '/#hostname="localhost"/c\hostname="auth.smtp.1and1.fr"' /etc/ssmtp/ssmtp.conf \


RUN echo "[mail function]" >> /usr/local/etc/php/php.ini \
	&& echo "SMTP = auth.smtp.1and1.fr" >> /usr/local/etc/php/php.ini \
	&& echo "smtp_port = 25" >> /usr/local/etc/php/php.ini \
	&& echo "sendmail_from = contact@vmonteco.ninja" >> /usr/local/etc/php/php.ini

RUN mkdir /var/www/media && chown -Rv 1042:1042 /var/www/media && chmod 700 /var/www/media
 
