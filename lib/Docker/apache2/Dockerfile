FROM httpd:2.4-alpine

ARG CALLBACK_ENDPOINT
ENV CALLBACK_ENDPOINT=$CALLBACK_ENDPOINT
COPY config/listener.conf /usr/local/apache2/conf/project.conf

RUN echo "Include conf/project.conf" >> conf/httpd.conf

# Remove defautl host configuration

# Turn on Apache extension
RUN sed -i \
    -e 's/^#\(LoadModule .*mod_deflate.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_proxy.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_proxy_fcgi.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_rewrite.so\)/\1/' \
    conf/httpd.conf

# SSL configuration
COPY ssl/apache-selfsigned.crt conf/server.crt
COPY ssl/apache-selfsigned.key conf/server.key
RUN sed -i \
    -e 's/^#\(Include .*httpd-ssl.conf\)/\1/' \
    -e 's/^#\(LoadModule .*mod_ssl.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_socache_shmcb.so\)/\1/' \
    conf/httpd.conf

RUN mkdir  -p /usr/local/apache2/htdocs/public

EXPOSE 80 8443

#[Run apache service]
CMD ["httpd","-DFOREGROUND"]
