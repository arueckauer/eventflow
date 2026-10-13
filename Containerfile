# Pinned to the exact PHP version used in the workshop so that everyone runs
# "composer ci" in an identical environment, regardless of their local setup.
FROM docker.io/library/php:8.5.11-cli

# Infection needs a coverage driver, which is not part of the base image.
# Everything else the tools need (mbstring, dom, tokenizer, ...) already is.
#
# Xdebug (the coverage driver) is installed with PIE, the PHP Foundation's
# official extension installer that supersedes PECL. PIE downloads the
# extension's source as a ZIP archive and builds it, so it needs the usual
# build tools plus libtool and unzip. The version is pinned to match the host.
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        curl \
        libtool \
        unzip; \
    curl -fsSL https://github.com/php/pie/releases/download/1.5.1/pie.phar -o /usr/local/bin/pie; \
    chmod +x /usr/local/bin/pie; \
    pie install xdebug/xdebug:3.5.3; \
    rm -rf /var/lib/apt/lists/* /root/.pie /tmp/pear

# Restrict Xdebug to coverage collection, which is all Infection needs. Without
# this, Xdebug would default to a mode that provides no code coverage at all.
ENV XDEBUG_MODE=coverage

WORKDIR /app

COPY tools/container-aliases.bash /etc/eventflow-aliases.bash
RUN printf '%s\n' '. /etc/eventflow-aliases.bash' >> /etc/bash.bashrc

# The project is mounted into /app at run time. We make sure the autoloader is
# generated (this needs no network, as all tools are committed PHARs) and then
# run the requested composer script. Without arguments the complete quality
# gate ("ci") runs; any other script can be passed on the command line:
#
#   podman run --rm -v "$PWD":/app:Z eventflow-ci refactoring
#
# The "--" placeholder becomes $0 of the shell so that the arguments appended
# by CMD (or the command line) land in "$@" unchanged.
ENTRYPOINT ["sh", "-c", "php tools/composer install --no-interaction --no-progress && exec php tools/composer \"$@\"", "--"]
CMD ["ci"]
