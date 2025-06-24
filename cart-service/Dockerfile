# Estágio de build com o Composer
FROM composer:2 as vendor

WORKDIR /app

COPY database/ database/
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist


# Estágio final com Nginx e PHP-FPM
FROM webdevops/php-nginx:8.2-alpine

# Variáveis de ambiente
ENV WEB_DOCUMENT_ROOT /app/public
ENV APP_ENV=production

# Copia os arquivos da aplicação e as dependências
COPY . /app
COPY --from=vendor /app/vendor/ /app/vendor/

# Ajusta as permissões
RUN chown -R application:application /app