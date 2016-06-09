# ownCloud Podcasts app

Build status: ![Travis CI build status](https://travis-ci.org/dprandzioch/owncloud-podcasts.svg?branch=master)
Travis build URL: [https://travis-ci.org/dprandzioch/owncloud-podcasts](https://travis-ci.org/dprandzioch/owncloud-podcasts)

Code Climate: [![Code Climate](https://codeclimate.com/github/dprandzioch/owncloud-podcasts/badges/gpa.svg)](https://codeclimate.com/github/dprandzioch/owncloud-podcasts)


The ownCloud Podcasts app consolidates podcast episodes of all your feeds and
shows them within the ownCloud web interface. Double-clicking a cover starts
playback in a separate window so you can continue working in ownCloud while
listening to your favorite episode. It also keeps track of your playback
position you so can continue anytime right where you left off.

## Todo

* Full translation
* Beautify UI
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

## Installing

owncloud-podcasts is available from the ownCloud app store, just enable
experimental apps. You can find it in the multimedia category.

### Installing from GitHub

Clone the current `master` branch and run `make`. Copy the resulting
podcasts.tar.gz into your apps/ directory and extract it. You will need to
have PHP >= 5.4, curl and scss, coffeescript, npm, node and bower installed.
Creating the tarball is known to fail on OS X & BSD due to the different tar
syntax - so be sure you are on a linux machine or edit the Makefile to match
your needs.

## Running tests

After [Installing PHPUnit](http://phpunit.de/getting-started.html) run:

    phpunit -c phpunit.xml
