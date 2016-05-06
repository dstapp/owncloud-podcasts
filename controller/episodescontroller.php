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
     * @var IRequest
     */
    protected $request;

    /**
     * EpisodesController constructor.
     *
     * @param string        $appName
     * @param IRequest      $request
     * @param string        $userId
     * @param EpisodeMapper $mapper
     */
    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        EpisodeMapper $mapper
    ) {
        parent::__construct($appName, $request);

        $this->userId = $userId;
        $this->request = $request;
        $this->episodeMapper = $mapper;
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
            "data"   => $this->episodeMapper->getEpisodes($this->userId, $feedId, 1000),
            "status" => "success",
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

        $result = $this->episodeMapper->updatePosition($this->userId, $id, $second, $duration);

        return new JSONResponse([
            "success" => (true === $result),
        ]);
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
        $result = $this->episodeMapper->markAllAsPlayed($this->userId);

        return new JSONResponse([
            "success" => (true === $result),
        ]);
    }
}