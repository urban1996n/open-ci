FROM httpd:2.4-alpine

ARG CALLBACK_ENDPOINT
ARG MAIN_SERVERNAME

COPY config/listener.conf /usr/local/apache2/conf/project.conf

RUN echo Include conf/project.conf >> /usr/local/apache2/conf/httpd.conf
# Remove defautl host configuration
# Change default endpoint for webhook redirection
RUN sed -i \
 -e "s#{main_server_name}#$MAIN_SERVERNAME#g" \
 -e "s#{webhook}#$CALLBACK_ENDPOINT#g" \
 /usr/local/apache2/conf/project.conf

# Turn on Apache extension
RUN sed -i \
 -e 's/^#\(LoadModule .*mod_deflate.so\)/\1/' \
 -e 's/^#\(LoadModule .*mod_proxy.so\)/\1/' \
 -e 's/^#\(LoadModule .*mod_proxy_fcgi.so\)/\1/' \
 -e 's/^#\(LoadModule .*mod_rewrite.so\)/\1/' \
 /usr/local/apache2/conf/httpd.conf

# SSL key and certificate files.
RUN apk add openssl
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /usr/local/apache2/conf/server.key -out /usr/local/apache2/conf/server.crt -subj "/CN=$MAIN_SERVERNAME"
COPY ssl/openci.dev/certificate.crt  /usr/local/apache2/conf/server.crt
COPY ssl/openci.dev/private.key /usr/local/apache2/conf/server.key
COPY ssl/openci.dev/ca_bundle.crt /usr/local/apache2/conf

# Include ssl config in main apache2 configuration
RUN sed -i \
 -e 's/^#\(Include .*httpd-ssl.conf\)/\1/' \
 -e 's/^#\(LoadModule .*mod_ssl.so\)/\1/' \
 -e 's/^#\(LoadModule .*mod_socache_shmcb.so\)/\1/' \
 conf/httpd.conf

# Comment out all ServerName, or ServerAlias in all files beside the custom configuration file.
RUN grep -rlE '^([^#])?(\s{0,})(ServerName|ServerAlias)' /usr/local/apache2/conf \
 | xargs sed -i -E "s/(ServerName|ServerAlias) (.*)/\1 $MAIN_SERVERNAME/g"

RUN mkdir  -p /usr/local/apache2/htdocs/public

#[Run apache service]
CMD ["httpd","-DFOREGROUND"]
