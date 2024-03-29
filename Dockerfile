FROM kiboaleph/php:8.2-apache-ubuntu

# copy file php.ini
COPY os/php.ini /usr/local/etc/php/php.ini

# expose port
EXPOSE 80

# start supervisor
CMD /usr/sbin/apache2ctl -D FOREGROUND