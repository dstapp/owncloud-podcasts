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
 * Class EpisodeMapper
 *
 * @package OCA\Podcasts\Db
 */
class EpisodeMapper extends Mapper
{

    /**
     * Constructor
     *
     * @param IDBConnection $db
     */
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, "podcasts_episodes");
    }

    /**
     * Marks all episodes for a user as played
     *
     * @param string $uid
     *
     * @return \PDOStatement
     */
    public function markAllAsPlayed($uid)
    {
        return $this->execute(
            "UPDATE *PREFIX*podcasts_episodes SET played = ? WHERE uid = ?",
            [true, $uid]
        );
    }

    /**
     * Checks if an episode exists
     *
     * @param string $uid
     * @param string $url
     *
     * @return bool
     */
    public function episodeExists($uid, $url)
    {
        $sql = "SELECT * FROM *PREFIX*podcasts_episodes WHERE uid = ? AND url = ? LIMIT 1";

        $stmt = $this->execute($sql, [$uid, $url]);
        $exists = count($stmt->fetchAll()) > 0;
        $stmt->closeCursor();

        return $exists;
    }

    /**
     * Gets a list of episodes for the current user (limited by feed if ID is supplied)
     *
     * @param string $uid
     * @param int    $feedId
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function getEpisodes($uid, $feedId = null, $limit = null, $offset = null)
    {
        $params = [$uid];

        $sql = "SELECT *PREFIX*podcasts_episodes.*, *PREFIX*podcasts_feeds.cover 
                FROM *PREFIX*podcasts_episodes 
                INNER JOIN *PREFIX*podcasts_feeds ON *PREFIX*podcasts_episodes.feed_id = *PREFIX*podcasts_feeds.id 
                WHERE *PREFIX*podcasts_episodes.uid = ?";

        if (false === is_null($feedId)) {
            $sql .= " AND *PREFIX*podcasts_episodes.feed_id = ?";

            $params[] = (int)$feedId;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->execute($sql, $params);
        $results = $stmt->fetchAll();

        return $results;
    }

    /**
     * Updates the playback position of an episode
     *
     * @param string $uid
     * @param int    $id
     * @param int    $second
     * @param int    $duration
     *
     * @return \PDOStatement
     */
    public function updatePosition($uid, $id, $second, $duration)
    {
        $id = (int)$id;
        $second = (int)$second;
        $duration = (int)$duration;

        return $this->execute(
            "UPDATE *PREFIX*podcasts_episodes SET current_second = ?, duration = ? WHERE id = ? AND uid = ?",
            [$second, $duration, $id, $uid]
        );
    }

    /**
     * Deletes all episodes that belong to a feed by it's ID
     *
     * @param int    $feedId
     * @param string $uid
     *
     * @return \PDOStatement
     */
    public function deleteByFeedId($feedId, $uid)
    {
        $feedId = (int)$feedId;

        return $this->execute(
            "DELETE FROM *PREFIX*podcasts_episodes WHERE feed_id = ? AND uid = ?",
            [$feedId, $uid]
        );
    }

    /**
     * Loads an episode by it's ID
     *
     * @param int    $id
     * @param string $uid
     *
     * @return \OCP\AppFramework\Db\Entity
     */
    public function getEpisode($id, $uid)
    {
        $id = (int)$id;

        $sql = "SELECT * FROM *PREFIX*podcasts_episodes WHERE id = ? AND uid = ?";

        return $this->findEntity($sql, [$id, $uid]);
    }
}
