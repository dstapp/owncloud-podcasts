app_name=podcasts
project_dir=$(CURDIR)/../$(app_name)
build_dir=$(CURDIR)/build/artifacts
appstore_dir=$(build_dir)/appstore
source_dir=$(build_dir)/source
package_name=$(app_name)

all: appstore

clean:
	rm -rf $(build_dir)

install-composer:
	curl -sS https://getcomposer.org/installer | php

install-deps: install-composer-deps

install-composer-deps: install-composer
	php composer.phar install

dev-setup: install-composer-deps

update-composer: install-composer
	rm -f composer.lock
	php composer.phar install --prefer-dist

watch-scss:
	sass --watch scss/:css/

compile-scss:
    sass --no-cache --update --style compressed --sourcemap=none --scss scss/:css/

appstore: clean install-deps compile-scss
	mkdir -p $(appstore_dir)
	tar cvzf $(appstore_dir)/$(package_name).tar.gz $(project_dir) \
	--exclude=$(project_dir)/.git \
	--exclude=$(project_dir)/build \
	--exclude=$(project_dir)/.travis.yml \
	--exclude=$(project_dir)/CONTRIBUTING.md \
	--exclude=$(project_dir)/composer.json \
	--exclude=$(project_dir)/composer.lock \
	--exclude=$(project_dir)/composer.phar \
	--exclude=$(project_dir)/package.json \
	--exclude=$(project_dir)/phpunit*xml \
	--exclude=$(project_dir)/Makefile \
	--exclude=$(project_dir)/tests \
	--exclude=$(project_dir)/scss \
	--exclude=.keep \
	--exclude=.gitkeep \
	--exclude=.gitignore \
	--exclude=.git \
	--exclude=.DS_Store