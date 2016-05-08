<?php

/**
 * Copyright (c) 2016 David Prandzioch
 * https://github.com/dprandzioch/owncloud-podcasts
 *
 * This file is part of owncloud-podcasts.
 *
 * owncloud-podcasts is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Podcasts\Db;

use OCP\AppFramework\Db\Entity;

/**
 * Class Feed
 *
 * @package OCA\Podcasts\Db
 */
class Feed extends Entity
{
    /**
     * @var string
     */
    public $uid;

    /**
     * @var string
     */
    public $name = "";

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $cover = "";
}
