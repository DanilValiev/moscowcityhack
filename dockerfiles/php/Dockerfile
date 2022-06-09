######################################################################
#######################  PHP-CLI  ####################################
######################################################################
FROM debian:stretch-20210927-slim as php-cli

ENV SURY_REPO_PATH "https://packages.sury.org/php/"

ENV PHP_GPG_PATH "${SURY_REPO_PATH}apt.gpg"

ENV PHP_VERSION "8.1"

ENV PECL_CHANNEL "pecl.php.net"

#> ENV

ENV BUILDTIME_DEPENDENCIES \
		autoconf \
		automake \
		dpkg-dev \
		file \
		g++ \
		gcc \
		libc-dev \
		make \
		pkg-config \
		re2c \
		bzip2 \
        file \
        patch \
        software-properties-common

ENV PERSISTENT_RUNTIME_DEPENDENCIES \
        apt-transport-https \
        ca-certificates \
        ssl-cert \
		curl \
        dirmngr \
        gnupg \
		xz-utils \
		openssl \
        lsb-release \
        apt-utils \
        curl

ENV PHP_DEPENDENCIES \
        apache2-bin \
        "libapache2-mod-php${PHP_VERSION}" \
        libapr1 \
        libaprutil1 \
        libaprutil1-dbd-sqlite3 \
        libaprutil1-ldap \
        liblua5.2-0 \
        libpcre2-8-0 \
        psmisc \
        ucf

ENV PHP_PACKAGES \
        "php${PHP_VERSION}" \
        "php${PHP_VERSION}-common" \
        "php${PHP_VERSION}-cli" \
        "php-pear" \
        "php${PHP_VERSION}-curl" \
        "php${PHP_VERSION}-opcache" \
        "php${PHP_VERSION}-readline" \
        "php${PHP_VERSION}-gd" \
        "php${PHP_VERSION}-pgsql" \
        "php${PHP_VERSION}-xml" \
        "php${PHP_VERSION}-ldap" \
        "php${PHP_VERSION}-zip" \
        "php${PHP_VERSION}-mbstring" \
        "php${PHP_VERSION}-gd" \
        "php${PHP_VERSION}-soap" \
        "php${PHP_VERSION}-imap" \
        "php${PHP_VERSION}-dev" \
        "php${PHP_VERSION}-intl" \
        "php${PHP_VERSION}-igbinary" \
        "php${PHP_VERSION}-msgpack" \
        "php${PHP_VERSION}-memcached" \
        "php${PHP_VERSION}-redis" \
        "php${PHP_VERSION}-amqp" \
        "php${PHP_VERSION}-bcmath"

ENV PECL_MODULES \
    "apcu-5.1.18" \
    "apcu_bc-1.0.5"

ENV PHP_MODS_ENABLED_ALL_SAPI \
    "amqp" \
    "apcu" \
    "apcu_bc" \
    "bcmath" \
    "calendar" \
    "ctype" \
    "curl" \
    "dom" \
    "exif" \
    "fileinfo" \
    "ftp" \
    "gd" \
    "gettext" \
    "iconv" \
    "igbinary" \
    "imap" \
    "intl" \
    "ldap" \
    "mbstring" \
    "msgpack" \
    "memcached" \
    "redis" \
    "pdo" \
    "pdo_pgsql" \
    "pgsql" \
    "phar" \
    "posix" \
    "rar" \
    "readline" \
    "shmop" \
    "simplexml" \
    "soap" \
    "sockets" \
    "sysvmsg" \
    "sysvsem" \
    "sysvshm" \
    "tokenizer" \
    "xml" \
    "xmlreader" \
    "xmlwriter" \
    "xsl" \
    "zip"

ENV PHP_MODS_ENABLED_CLI \
    ${PHP_MODS_ENABLED_ALL_SAPI}

ENV PHP_MEMORY_LIMIT -1
ENV PHP_TIMEZONE "Europe/Moscow"
ENV PHP_ERROR_LOG ""

#< ENV

#> Set timezone
RUN echo "${PHP_TIMEZONE}" > /etc/timezone \
    && ln -sf /usr/share/zoneinfo/${PHP_TIMEZONE} /etc/localtime \
    && dpkg-reconfigure -f noninteractive tzdata
#< Set timezone

#> Install basic dependenciesn
RUN apt update && apt install -y --no-install-recommends $BUILDTIME_DEPENDENCIES $PERSISTENT_RUNTIME_DEPENDENCIES \
    && apt clean -y && rm -rf /var/lib/{apt,dpkg}/
#< Install basic dependenciesn

#> Add cury php repo
RUN curl -fsSL $PHP_GPG_PATH | apt-key add - \
    && add-apt-repository "deb ${SURY_REPO_PATH} $(lsb_release -cs) main"
#< Add cury php repo \

#> Install php dependenciesn and php
RUN apt update && apt install -y --no-install-recommends $PHP_DEPENDENCIES \
    && apt clean -y && rm -rf /var/lib/{apt,dpkg}/ /var/cache/apt/* \
    && apt-mark auto '.*' > /dev/null \
    && apt-mark manual $PERSISTENT_RUNTIME_DEPENDENCIES $PHP_DEPENDENCIES > /dev/null
#< Install php dependenciesn and php \

#> Install php ext
RUN apt install -y --no-install-recommends $PHP_PACKAGES && apt clean -y && rm -rf /var/lib/{apt,dpkg}/; \
    pecl channel-update $PECL_CHANNEL && pecl install $PECL_MODULES && rm -rf /tmp/pear ~/.pearrc;
#< Install php ext \

#> Remove build dependencies
RUN apt remove -y $BUILDTIME_DEPENDENCIES
#< Remove build dependencies

#> Php setup
WORKDIR /etc/php/${PHP_VERSION}/cli/
COPY ./cli/php.ini ./
COPY ./cli/ldap/ldap.conf /etc/ldap/ldap.conf
COPY ./mods-available/ /etc/php/${PHP_VERSION}/mods-available/
RUN rm ./conf.d/* && phpenmod -s cli -v ${PHP_VERSION} ${PHP_MODS_ENABLED_CLI}
COPY ./cli/docker-entrypoint.sh /usr/local/bin/
#< Php setup

WORKDIR /var/www

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php"]

######################################################################
#######################  PHP-FPM  ####################################
######################################################################
FROM php-cli AS php-fpm

#> ENV

ENV PHP_MODULES_ENABLED_FPM \
    "${PHP_MODS_ENABLED_ALL_SAPI}" \
    "opcache"

ENV PHP_DISPLAY_ERRORS "Off"
ENV PHP_DISPLAY_STARTUP_ERRORS "Off"
ENV PHP_MEMORY_LIMIT "128M"
ENV PHP_MAX_EXECUTION_TIME 600
ENV PHP_MAX_INPUT_TIME 60
ENV PHP_POST_MAX_SIZE "8M"
ENV PHP_FILE_UPLOADS "Off"
ENV PHP_UPLOAD_MAX_FILESIZE "2M"
ENV PHP_MAX_FILE_UPLOADS 20
ENV PHP_OPCACHE_ENABLE 1
ENV PHP_OPCACHE_MEMORY_CONSUMPTION 128
ENV PHP_OPCACHE_INTERNED_STRINGS_BUFFER 8
ENV PHP_OPCACHE_MAX_ACCELERATED_FILES 10000
ENV PHP_OPCACHE_PRELOAD ""

ENV FPM_PM "dynamic"
ENV FPM_MAX_CHILDREN 100
ENV FPM_START_SERVERS 6
ENV FPM_MIN_SPARE_SERVERS 4
ENV FPM_MAX_SPARE_SERVERS 8
ENV FPM_REQUEST_TERMINATE_TIMEOUT "600s"
ENV FPM_MAX_REQUESTS "2048"
ENV FPM_PROCESS_IDLE_TIMEOUT "60s"
ENV FPM_STATUS_PATH "/status"

#< ENV

#> Install build dependenciesn
RUN apt update && apt install -y --no-install-recommends $BUILDTIME_DEPENDENCIES \
    && apt clean -y && rm -rf /var/lib/{apt,dpkg}/
#< Install build dependenciesn

#> Install php dependenciesn and php
RUN apt install -y --no-install-recommends "php${PHP_VERSION}-fpm" && apt-mark manual "php${PHP_VERSION}-fpm" \
    && apt clean -y && rm -rf /var/lib/{apt,dpkg}/ && rm -rf /var/cache/apt/*
#< Install php dependenciesn and php \

#> Remove build dependencies
RUN apt remove -y $BUILDTIME_DEPENDENCIES
#< Remove build dependencies

#> Php setup
WORKDIR /etc/php/${PHP_VERSION}/fpm/
RUN mkdir -p /var/log/php-fpm/ /etc/php/fpm/
COPY ./fpm/php.ini ./
COPY ./fpm/php-fpm.conf ./
RUN rm -f conf.d/* && phpenmod -s fpm -v ${PHP_VERSION} ${PHP_MODULES_ENABLED_FPM}
COPY ./fpm/pool.d/ pool.d/
RUN ln -s /etc/php/${PHP_VERSION}/fpm/pool.d/ /etc/php/fpm/pool.d
RUN ln -s "/usr/sbin/php-fpm${PHP_VERSION}" /usr/sbin/php-fpm
COPY ./fpm/docker-entrypoint.sh /usr/local/bin/
RUN php-fpm --test
#< Php setup

WORKDIR /var/www

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm", "--nodaemonize", "--force-stderr"]
STOPSIGNAL SIGQUIT
EXPOSE 9000

######################################################################
######################  DEV PHP-FPM VERSION ##########################
######################################################################
FROM php-fpm AS php-dev

#> ENV

ENV DEV_MODE_DEPENDENCIES \
    "supervisor" \
    "php${PHP_VERSION}-dev"

ENV DEV_MODE_PECL_MODULES \
    "xdebug"

ENV MODS_AVAILABLE_DEV \
    "xdebug"

ENV PHP_DISPLAY_ERRORS "On"
ENV PHP_DISPLAY_STARTUP_ERRORS "On"
ENV PHP_OPCACHE_ENABLE "Off"
ENV PHP_OPCACHE_PRELOAD ""

#< ENV

#> Install build dependenciesn
RUN apt update && apt install -y --no-install-recommends $BUILDTIME_DEPENDENCIES \
    && apt clean -y && rm -rf /var/lib/{apt,dpkg}/
#< Install build dependenciesn

#> Update php settings
WORKDIR /etc/php/${PHP_VERSION}
COPY ./dev/php-ini.overrides.ini ./dev/
RUN cat ./dev/php-ini.overrides.ini >> ./fpm/php.ini; \
    cat ./dev/php-ini.overrides.ini >> ./cli/php.ini
#< Update php settings

#> Install dev dependenciesn and php modules
RUN apt install -y --no-install-recommends ${DEV_MODE_DEPENDENCIES} \
    && apt clean -y && rm -rf /var/lib/{apt,dpkg}/ /var/cache/apt/*; \
    pecl channel-update $PECL_CHANNEL && pecl install $DEV_MODE_PECL_MODULES && rm -rf /tmp/pear ~/.pearrc;
#< Install dev dependenciesn and php modules

#> Setup php and add security checker
COPY ./dev/mods-available/ /etc/php/${PHP_VERSION}/mods-available/
RUN phpenmod -s ALL -v ${PHP_VERSION} ${MODS_AVAILABLE_DEV}
ADD ./dev/bin/local-php-security-checker /usr/local/bin
#< Setup php and add security checker

WORKDIR /var/www
