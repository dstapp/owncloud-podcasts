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

use OCA\Podcasts\Db\EpisodeMapper;
use OCA\Podcasts\Db\FeedMapper;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use \OCP\IRequest;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCP\AppFramework\Controller;
use \OCP\IDb;
use OCP\IURLGenerator;

/**
 * Class WebViewController
 *
 * @package OCA\Podcasts\Controller
 */
class WebViewController extends Controller
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
     * @var IURLGenerator
     */
    protected $urlGenerator;

    /**
     * @var FeedMapper
     */
    protected $feedMapper;

    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        IURLGenerator $urlGenerator,
        EpisodeMapper $episodeMapper,
        FeedMapper $feedMapper
    ) {
        parent::__construct($appName, $request);

        $this->userId = $userId;
        $this->episodeMapper = $episodeMapper;
        $this->urlGenerator = $urlGenerator;
        $this->feedMapper = $feedMapper;
    }

    /**
     * App interface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return TemplateResponse
     */
    public function index()
    {
        $response = new TemplateResponse("podcasts", "main", []);

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedFrameDomain("'self'");
        $policy->addAllowedImageDomain("*");

        $response->setContentSecurityPolicy($policy);

        return $response;
    }

    /**
     * Player interface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @param int $id
     *
     * @return TemplateResponse
     */
    public function player($id)
    {
        $id = (int)$id;

        $episode = $this->episodeMapper->getEpisode($id, $this->userId);
        $feed = $this->feedMapper->getFeed($episode->getFeedId(), $this->userId);

        $params = [
            "episode" => $episode,
            "feed"    => $feed,
        ];

        $response = new TemplateResponse("podcasts", "player", $params);

        $policy = new ContentSecurityPolicy();
        $policy->addAllowedMediaDomain("*");
        $policy->addAllowedImageDomain("*");
        $policy->addAllowedConnectDomain("*");

        $response->setContentSecurityPolicy($policy);

        return $response;
    }
}