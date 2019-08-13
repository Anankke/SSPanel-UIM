FROM base
ADD https://github.com/just-containers/s6-overlay/releases/download/v1.21.8.0/s6-overlay-amd64.tar.gz /tmp/
RUN tar xzf /tmp/s6-overlay-amd64.tar.gz -C /
RUN apt update &&  apt -y install php-xdebug
COPY --chown=www-data / docker_helper/*  /app/docker_helper/
RUN  echo  "/app/storage true www-data 0644 0775" >  /etc/fix-attrs.d/01-app-dir
ENTRYPOINT ["/init", "/app/docker_helper/setup.sh"]
CMD ["debug"]
EXPOSE 80



