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

use OCA\Podcasts\Db\Feed;
use OCA\Podcasts\Db\FeedMapper;
use OCA\Podcasts\Feed\FeedUpdater;
use \OCP\IRequest;
use \OCP\AppFramework\ApiController;
use \OCP\AppFramework\Http\JSONResponse;
use \OCP\AppFramework\Http;
use \OCA\Podcasts\Db\EpisodeMapper;

/**
 * Class FeedsController
 *
 * @package OCA\Podcasts\Controller
 */
class FeedsController extends ApiController
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
     * @var FeedUpdater
     */
    protected $feedUpdater;

    /**
     * @var IRequest
     */
    protected $request;

    /**
     * FeedsController constructor.
     *
     * @param string        $appName
     * @param IRequest      $request
     * @param string        $userId
     * @param EpisodeMapper $mapper
     * @param FeedMapper    $feedMapper
     * @param FeedUpdater   $feedUpdater
     */
    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        EpisodeMapper $mapper,
        FeedMapper $feedMapper,
        FeedUpdater $feedUpdater
    ) {
        parent::__construct($appName, $request);

        $this->userId = $userId;
        $this->request = $request;
        $this->episodeMapper = $mapper;
        $this->feedMapper = $feedMapper;
        $this->feedUpdater = $feedUpdater;
    }

    /**
     * Returns all feeds for the current user
     *
     * @NoAdminRequired
     *
     * @return JSONResponse
     */
    public function getFeeds()
    {
        return new JSONResponse([
            "data"    => $this->feedMapper->getFeeds($this->userId),
            "success" => true,
        ]);
    }

    /**
     * Deletes a feed
     *
     * @NoAdminRequired
     *
     * @param int $feedId
     *
     * @return JSONResponse
     */
    public function deleteFeed($feedId = null)
    {
        $feed = new Feed();
        $feed->setId($feedId);

        $this->feedMapper->delete($feed);
        $this->episodeMapper->deleteByFeedId($feedId, $this->userId);

        return new JSONResponse([
            "success" => true,
        ]);
    }

    /**
     * Adds a feed
     *
     * @NoAdminRequired
     *
     * @return JSONResponse
     */
    public function addFeed()
    {
        $success = false;
        $message = "";

        try {
            $url = $this->request->getParam("url");

            if (true === empty($url)) {
                throw new \Exception("URL is empty");
            }

            if (false === filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \Exception("URL is invalid");
            }

            if (true === $this->feedMapper->feedExists($this->userId, $url)) {
                throw new \Exception("Feed already exists");
            }

            $feed = new Feed();
            $feed->setName("");
            $feed->setCover("");
            $feed->setUrl($url);
            $feed->setUid($this->userId);

            if (true === $this->feedUpdater->checkFeed($feed)) {
                $feed = $this->feedMapper->insert($feed);
                $this->feedUpdater->processFeed($feed);
            } else {
                throw new \Exception("Feed is invalid");
            }

            $success = true;
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return new JSONResponse(
            [
                "success" => $success,
                "message" => $message,
            ],
            (false === empty($message)) ? Http::STATUS_BAD_REQUEST : Http::STATUS_OK
        );
    }
}