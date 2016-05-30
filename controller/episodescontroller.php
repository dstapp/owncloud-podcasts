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

namespace OCA\Podcasts\Controller;

use \OCP\IRequest;
use \OCP\AppFramework\ApiController;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;
use \OCA\Podcasts\Db\EpisodeMapper;
use \OCA\Podcasts\Db\FeedMapper;

/**
 * Class EpisodesController
 *
 * @package OCA\Podcasts\Controller
 */
class EpisodesController extends ApiController
{

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var EpisodeMapper
     */
    protected $episodeMapper;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    /**
     * @var IRequest
     */
    protected $request;

    /**
     * EpisodesController constructor.
     *
     * @param string        $appName
     * @param IRequest      $request
     * @param string        $userId
     * @param EpisodeMapper $episodeMapper
     * @param FeedMapper    $feedMapper
     */
    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        EpisodeMapper $episodeMapper,
        FeedMapper $feedMapper
    ) {
        parent::__construct($appName, $request);

        $this->userId = $userId;
        $this->request = $request;
        $this->episodeMapper = $episodeMapper;
        $this->feedMapper = $feedMapper;
    }

    /**
     * AJAX / Returns all episodes (for a feed, if ID is supplied)
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return JSONResponse
     */
    public function getEpisodes()
    {
        $feedId = $this->request->getParam("feedId", null);

        return new JSONResponse([
            "data"    => $this->episodeMapper->getEpisodes($this->userId, $feedId, 1000),
            "success" => true,
        ]);
    }

    /**
     * AJAX / Returns a single episode
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return JSONResponse
     */
    public function getEpisode($id)
    {
        $episode = (array) $this->episodeMapper->getEpisode((int) $id, $this->userId);
        $feed = $this->feedMapper->getFeed((int) $episode["feedId"], $this->userId);

        $ext = pathinfo($episode["url"], PATHINFO_EXTENSION);

        switch ($ext) {
            case "m4a":
            case "mp4":
                $mimeType = "audio/mp4";
                break;

            case "ogg":
                $mimeType = "audio/ogg; codecs=vorbis";
                break;

            case "opus":
                $mimeType = "audio/ogg; codecs=opus";
                break;

            default:
                $mimeType = "audio/mpeg";
                break;
        }

        $episode = array_merge($episode, [
            "mimeType" => $mimeType,
            "cover" => $feed->getCover()
        ]);

        return new JSONResponse([
            "data"    => $episode,
            "success" => true,
        ]);
    }

    /**
     * Updates the playback position for the current episode
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @param int $id
     *
     * @return JSONResponse
     */
    public function updatePosition($id)
    {
        $id = (int)$id;

        $second = (int)$this->request->getParam("second");
        $duration = (int)$this->request->getParam("duration");

        $playedUpdateResult = true;

        if (($second + 60) > $duration) {
            // remainingPlaytime < 60 sec -> played -> reset position
            $playedUpdateResult = (bool)$this->episodeMapper->updatePlayedStatus($this->userId, $id, true);
            $second = 0;
        }

        $result = (bool)$this->episodeMapper->updatePosition($this->userId, $id, $second, $duration);

        return new JSONResponse(
            [
                "success" => (true === $result && true === $playedUpdateResult),
            ],
            (true === $result && true === $playedUpdateResult) ? Http::STATUS_OK : Http::STATUS_BAD_REQUEST
        );
    }

    /**
     * Marks all episodes as played
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return JSONResponse
     */
    public function markPlayed()
    {
        $result = (bool)$this->episodeMapper->markAllAsPlayed($this->userId);

        return new JSONResponse(
            [
                "success" => (true === $result),
            ],
            (true === $result) ? Http::STATUS_OK : Http::STATUS_BAD_REQUEST
        );
    }
}
