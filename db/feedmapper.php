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

use OCP\IDBConnection;
use OCP\AppFramework\Db\Mapper;

/**
 * Class FeedMapper
 *
 * @package OCA\Podcasts\Db
 */
class FeedMapper extends Mapper
{

    /**
     * Constructor
     *
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, "podcasts_feeds");
    }

    /**
     * Lists all feeds for a user
     *
     * @param string $uid
     *
     * @return array
     */
    public function getFeeds($uid)
    {
        $sql = "SELECT * FROM *PREFIX*podcasts_feeds WHERE uid = ? ORDER BY `name`";

        return $this->findEntities($sql, [$uid]);
    }

    /**
     * Lists all feeds without user context
     *
     * @return array
     */
    public function getAllFeeds()
    {
        $sql = "SELECT * FROM *PREFIX*podcasts_feeds ORDER BY `name`";

        return $this->findEntities($sql, []);
    }

    /**
     * Gets a specific feed for a user
     *
     * @param int    $id
     * @param string $uid
     *
     * @return \OCP\AppFramework\Db\Entity
     */
    public function getFeed($id, $uid)
    {
        $id = (int)$id;

        $sql = "SELECT * FROM *PREFIX*podcasts_feeds WHERE id = ? AND uid = ?";

        return $this->findEntity($sql, [$id, $uid]);
    }

    /**
     * Checks wheather a feed exists or not
     *
     * @param string $uid
     * @param string $url
     *
     * @return bool
     */
    public function feedExists($uid, $url)
    {
        $sql = "SELECT * FROM *PREFIX*podcasts_feeds WHERE uid = ? AND url = ? LIMIT 1";

        $stmt = $this->execute($sql, [$uid, $url]);
        $exists = $stmt->rowCount() > 0;
        $stmt->closeCursor();

        return $exists;
    }
}
