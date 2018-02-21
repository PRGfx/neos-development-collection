<?php

namespace Neos\Neos\Domain\Context\Content;

/*
 * This file is part of the Neos.Neos package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cache\CacheAwareInterface;
use Neos\ContentRepository\Domain\ValueObject\WorkspaceName;
use Neos\ContentRepository\Domain\ValueObject\DimensionSpacePoint;
use Neos\ContentRepository\Domain\ValueObject\NodeAggregateIdentifier;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Domain\Context\Content\Exception\InvalidContentQuerySerializationException;

/**
 * The content request data transfer object
 *
 * Describes the intention of the user making the current request:
 * Show me
 *  node $nodeAggregateIdentifier
 *  in site $siteIdentifier
 *  in dimensions $dimensionSpacePoint
 *  in workspace $workspaceName
 */
final class ContentQuery implements \JsonSerializable, CacheAwareInterface
{
    /**
     * @var NodeAggregateIdentifier
     */
    protected $nodeAggregateIdentifier;

    /**
     * @var WorkspaceName
     */
    protected $workspaceName;

    /**
     * @var DimensionSpacePoint
     */
    protected $dimensionSpacePoint;

    /**
     * @var NodeAggregateIdentifier
     */
    protected $siteIdentifier;


    /**
     * @param NodeAggregateIdentifier $nodeAggregateIdentifier
     * @param WorkspaceName $workspaceName
     * @param DimensionSpacePoint $dimensionSpacePoint
     * @param NodeAggregateIdentifier $siteIdentifier
     */
    public function __construct(NodeAggregateIdentifier $nodeAggregateIdentifier, WorkspaceName $workspaceName, DimensionSpacePoint $dimensionSpacePoint, NodeAggregateIdentifier $siteIdentifier)
    {
        $this->nodeAggregateIdentifier = $nodeAggregateIdentifier;
        $this->workspaceName = $workspaceName;
        $this->dimensionSpacePoint = $dimensionSpacePoint;
        $this->siteIdentifier = $nodeAggregateIdentifier;
    }

    /**
     * @param string $jsonSerialization
     * @return ContentQuery
     * @throws InvalidContentQuerySerializationException
     */
    public static function fromJson(string $jsonSerialization): ContentQuery
    {
        $rawComponents = json_decode($jsonSerialization, true);
        if (is_null($rawComponents)) {
            throw new InvalidContentQuerySerializationException('The given serialized content request "' . $jsonSerialization . '" could not be decoded.', 1518868835);
        }
        if (!isset($rawComponents['nodeAggregateIdentifier'])) {
            throw new InvalidContentQuerySerializationException('The given serialized content request "' . $jsonSerialization . '" does not contain a node identifier.', 1518868782);
        }
        if (!isset($rawComponents['workspaceName'])) {
            throw new InvalidContentQuerySerializationException('The given serialized content request "' . $jsonSerialization . '" does not contain a workspace name.', 1518868875);
        }
        if (!isset($rawComponents['dimensionSpacePoint']['coordinates'])) {
            throw new InvalidContentQuerySerializationException('The given serialized content request "' . $jsonSerialization . '" does not contain a dimension space point.', 1518868912);
        }
        if (!isset($rawComponents['siteIdentifier'])) {
            throw new InvalidContentQuerySerializationException('The given serialized content request "' . $jsonSerialization . '" does not contain a site identifier.', 1518868937);
        }

        return new ContentQuery(
            new NodeAggregateIdentifier($rawComponents['nodeAggregateIdentifier']),
            new WorkspaceName($rawComponents['workspaceName']),
            new DimensionSpacePoint($rawComponents['dimensionSpacePoint']['coordinates']),
            new NodeAggregateIdentifier($rawComponents['siteIdentifier'])
        );
    }


    /**
     * @return NodeAggregateIdentifier
     */
    public function getNodeAggregateIdentifier(): NodeAggregateIdentifier
    {
        return $this->nodeAggregateIdentifier;
    }

    /**
     * @return WorkspaceName
     */
    public function getWorkspaceName(): WorkspaceName
    {
        return $this->workspaceName;
    }

    /**
     * @return DimensionSpacePoint
     */
    public function getDimensionSpacePoint(): DimensionSpacePoint
    {
        return $this->dimensionSpacePoint;
    }

    /**
     * @return NodeAggregateIdentifier
     */
    public function getSiteIdentifier(): NodeAggregateIdentifier
    {
        return $this->siteIdentifier;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this);
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'nodeAggregateIdentifier' => $this->nodeAggregateIdentifier,
            'workspaceName' => $this->workspaceName,
            'dimensionSpacePoint' => $this->dimensionSpacePoint,
            'siteIdentifier' => $this->siteIdentifier
        ];
    }

    /**
     * @return string
     */
    public function getCacheEntryIdentifier(): string
    {
        return (string) $this;
    }
}
