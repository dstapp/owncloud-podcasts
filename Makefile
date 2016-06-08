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

install-grunt-deps:
	npm install

install-deps: install-composer-deps install-grunt-deps
	bower install

install-composer-deps: install-composer
	php composer.phar install

dev-setup: install-composer-deps

update-composer: install-composer
	rm -f composer.lock
	php composer.phar install --prefer-dist

build-assets:
	grunt dist

appstore: clean install-deps build-assets
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
	--exclude=$(project_dir)/src \
	--exclude=node_modules \
	--exclude=.codeclimate.yml \
	--exclude=.sass-cache \
	--exclude=coffeelint.json \
	--exclude=Gruntfile.js \
	--exclude=bower.json \
	--exclude=.bowerrc \
	--exclude=*.css.map \
	--exclude=.keep \
	--exclude=.gitkeep \
	--exclude=.gitignore \
	--exclude=.git \
	--exclude=.DS_Store
