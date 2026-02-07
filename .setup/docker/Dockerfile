# syntax = docker/dockerfile:1

# ##########################################################
# GLOBAL ARGUMENTS
# ##########################################################
ARG BASE_STAGE=base
ARG PHP_ENV=production
# www-data non root user exists in php apache
ARG USERNAME=www-data
ARG ADMIN_USER=admin
ARG ADMIN_PASSWORD=admin
ARG WORKING_DIR=/var/www/html





# ##########################################################
# STAGE 1: BASE (The Runtime Environment)
# ##########################################################
FROM php:8.2-apache AS base
ARG WORKING_DIR

# System deps
RUN apt-get update && apt-get install -y \
    libzip-dev \
 && docker-php-ext-install mysqli opcache \
 && a2enmod rewrite \
 && rm -rf /var/lib/apt/lists/*

WORKDIR $WORKING_DIR







# ##########################################################
# STAGE 2: LITETOOLS (Basic Admin & Sync)
# ##########################################################
FROM base AS litetools
ARG ADMIN_USER
ARG ADMIN_PASSWORD

# Add admin user with sudo privileges
RUN apt-get update && apt-get install -y --no-install-recommends ca-certificates sudo nano tmux rsync curl git procps \
    && useradd -m -s /bin/bash ${ADMIN_USER} \
    && echo "${ADMIN_USER}:${ADMIN_PASSWORD}" | chpasswd \
    && echo "${ADMIN_USER} ALL=(ALL) ALL" >> /etc/sudoers \
    && rm -rf /var/lib/apt/lists/*






# ##########################################################
# STAGE 3: POWERTOOLS (The "Ultimate" Dev Environment) | *note:* persist $HOME dir
# https://dev.to/rahuldhole/docker-devtools-stage-powerlevel-ohmyzsh-tmux-fuzzy-finder-autocomplete-etc-8g1
# ##########################################################
FROM litetools AS powertools
# ARG USERNAME

# RUN apt-get update && apt-get install -y --no-install-recommends \
#     zsh fontconfig locales build-essential \
#     && rm -rf /var/lib/apt/lists/*

# RUN locale-gen en_US.UTF-8
# ENV LANG=en_US.UTF-8 LANGUAGE=en_US:en LC_ALL=en_US.UTF-8 TERM=xterm-256color

# USER $USERNAME
# WORKDIR /home/$USERNAME

# Install Oh My Zsh, Powerlevel10k, and Plugins
# RUN sh -c "$(curl -fsSL https://raw.github.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended \
#     && git clone --depth=1 https://github.com/romkatv/powerlevel10k.git ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/themes/powerlevel10k \
#     && git clone https://github.com/zsh-users/zsh-syntax-highlighting.git ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-syntax-highlighting \
#     && git clone https://github.com/zsh-users/zsh-autosuggestions ${ZSH_CUSTOM:-~/.oh-my-zsh/custom}/plugins/zsh-autosuggestions \
#     && sed -i 's/ZSH_THEME="robbyrussell"/ZSH_THEME="powerlevel10k\/powerlevel10k"/' ~/.zshrc \
#     && sed -i 's/plugins=(git)/plugins=(git zsh-syntax-highlighting zsh-autosuggestions tmux)/' ~/.zshrc \
#     && echo 'POWERLEVEL9K_DISABLE_CONFIGURATION_WIZARD=true' >> ~/.zshrc

# Reset to app directory for dev work
# WORKDIR ${WORKING_DIR}
# CMD ["/bin/zsh"]






# ##########################################################
# STAGE 4: FINAL / APP (The Lean Production/Test/Dev Stage)
# ##########################################################
FROM ${BASE_STAGE} AS final
ARG USERNAME
ARG WORKING_DIR


# PHP hardening
RUN if [ "${PHP_ENV}" = "production" ]; then \
  echo "opcache.enable=1"; \
  echo "opcache.validate_timestamps=0"; \
  echo "expose_php=0"; \
fi > /usr/local/etc/php/conf.d/prod.ini

# Copy app
COPY --chown=www-data:www-data app/ .

# Healthcheck
HEALTHCHECK CMD curl -f http://localhost/ || exit 1

# 3. Final cleanup and Entrypoint
USER root
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

USER $USERNAME

CMD ["apache2-foreground"]