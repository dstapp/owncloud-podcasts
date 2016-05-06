# ownCloud Podcasts app

The ownCloud Podcasts app consolidates podcast episodes of all your feeds and shows them within the ownCloud web
interface. Double-clicking a cover starts playback in a separate window so you can continue working in ownCloud while
listening to your favorite episode. It also keeps track of your playback position you so can continue anytime right
where you left off.

Place this app in **owncloud/apps/** and run `composer install`

## Todo

* Volume control
* Full translation
* Beautify UI
* Unit tests
* Fix CLRF

## License

```
ownCloud Podcasts app
Copyright (C) 2016 David Prandzioch

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
```

## Publish to App Store

First get an account for the [App Store](http://apps.owncloud.com/) then run:

    make appstore_package

The archive is located in build/artifacts/appstore and can then be uploaded to the App Store.

## Running tests
After [Installing PHPUnit](http://phpunit.de/getting-started.html) run:

    phpunit -c phpunit.xml
