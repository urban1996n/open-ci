FROM redis/redis-stack-server:6.2.0

ARG REDIS_PASSWORD

COPY main.conf /etc/redis/redis.conf

RUN sed -i \
    -e "s/REDIS_PASSWORD/"$REDIS_PASSWORD"/" \
    /etc/redis/redis.conf

CMD ["redis-server", "/etc/redis/redis.conf"]
