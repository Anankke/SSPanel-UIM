FROM base
COPY --chown=www-data . .
ENTRYPOINT ["/app/docker_helper/setup.sh"]
CMD ["prod"]
EXPOSE 80

