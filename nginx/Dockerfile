FROM nginx:alpine

COPY ./nginx.conf /etc/nginx
RUN adduser -D -H -u 1000 -s /bin/bash www-data -G www-data