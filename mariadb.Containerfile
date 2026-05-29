FROM docker.io/library/mariadb:11

COPY init.sql /docker-entrypoint-initdb.d/init.sql
