<?php
namespace Aws\ElasticsearchService;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Elasticsearch Service** service.
 *
 * @method \Aws\Result addTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise addTagsAsync(array $args = [])
 * @method \Aws\Result createElasticsearchDomain(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createElasticsearchDomainAsync(array $args = [])
 * @method \Aws\Result deleteElasticsearchDomain(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteElasticsearchDomainAsync(array $args = [])
 * @method \Aws\Result describeElasticsearchDomain(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeElasticsearchDomainAsync(array $args = [])
 * @method \Aws\Result describeElasticsearchDomainConfig(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeElasticsearchDomainConfigAsync(array $args = [])
 * @method \Aws\Result describeElasticsearchDomains(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeElasticsearchDomainsAsync(array $args = [])
 * @method \Aws\Result listDomainNames(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listDomainNamesAsync(array $args = [])
 * @method \Aws\Result listTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTagsAsync(array $args = [])
 * @method \Aws\Result removeTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeTagsAsync(array $args = [])
 * @method \Aws\Result updateElasticsearchDomainConfig(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateElasticsearchDomainConfigAsync(array $args = [])
 */
class ElasticsearchServiceClient extends AwsClient {}
