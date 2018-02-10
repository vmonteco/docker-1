FROM nginx

#RUN apt-get update && apt-get install -y ssmtp
RUN groupadd -g 1042 camagru
RUN useradd -u 1042 -g camagru camagru

RUN mkdir /var/www && mkdir /var/www/media && chown -Rv camagru:camagru /var/www && chmod -Rv 700 /var/www
RUN mkdir /etc/nginx/certs
COPY camagru.key camagru.crt /etc/nginx/certs/

# # Set ssmtp.conf :
# RUN sed -i '/root=postmaster/c\root=contact@vmonteco.ninja' /etc/ssmtp/ssmtp.conf \
# 	&& echo "AuthUser=contact@vmonteco.ninja" >> /etc/ssmtp/ssmtp.conf \
# 	&& echo "AuthPass=" >> /etc/ssmtp/ssmtp.conf \
# 	&& echo "mailhub=auth.smtp.1and1.fr:25" >> /etc/ssmtp/ssmtp.conf \
# 	&& echo "#UseTLS=YES" >> /etc/ssmtp/ssmtp.conf

# # Set revaliases :
# RUN echo "root:contact@vmonteco.ninja:auth.smtp.1and1.fr:25" >> /etc/ssmtp/revaliases \
# 	&& echo "" >> /etc/ssmtp/revaliases \
# 	&& echo "" >> /etc/ssmtp/revaliases

# #RUN echo "SMTP = auth.smtp.1and1.fr\nsmtp_port = 25\nsendmail_from = contact@vmonteco.ninja\n" >> /usr/local/etc/php-fpm.d/zz-docker.conf
# #RUN sed -i '/#hostname="localhost"/c\hostname="auth.smtp.1and1.fr"' /etc/ssmtp/ssmtp.conf \


# RUN echo "[mail function]" >> /usr/local/etc/php/php.ini \
# 	&& echo "SMTP = auth.smtp.1and1.fr" >> /usr/local/etc/php/php.ini \
# 	&& echo "smtp_port = 25" >> /usr/local/etc/php/php.ini \
# 	&& echo "sendmail_from = contact@vmonteco.ninja" >> /usr/local/etc/php/php.ini
