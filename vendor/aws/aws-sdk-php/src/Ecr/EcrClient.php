<?php
namespace Aws\Ecr;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon EC2 Container Registry** service.
 *
 * @method \Aws\Result batchCheckLayerAvailability(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchCheckLayerAvailabilityAsync(array $args = [])
 * @method \Aws\Result batchDeleteImage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchDeleteImageAsync(array $args = [])
 * @method \Aws\Result batchGetImage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGetImageAsync(array $args = [])
 * @method \Aws\Result completeLayerUpload(array $args = [])
 * @method \GuzzleHttp\Promise\Promise completeLayerUploadAsync(array $args = [])
 * @method \Aws\Result createRepository(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createRepositoryAsync(array $args = [])
 * @method \Aws\Result deleteRepository(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteRepositoryAsync(array $args = [])
 * @method \Aws\Result deleteRepositoryPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteRepositoryPolicyAsync(array $args = [])
 * @method \Aws\Result describeImages(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeImagesAsync(array $args = [])
 * @method \Aws\Result describeRepositories(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeRepositoriesAsync(array $args = [])
 * @method \Aws\Result getAuthorizationToken(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getAuthorizationTokenAsync(array $args = [])
 * @method \Aws\Result getDownloadUrlForLayer(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDownloadUrlForLayerAsync(array $args = [])
 * @method \Aws\Result getRepositoryPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getRepositoryPolicyAsync(array $args = [])
 * @method \Aws\Result initiateLayerUpload(array $args = [])
 * @method \GuzzleHttp\Promise\Promise initiateLayerUploadAsync(array $args = [])
 * @method \Aws\Result listImages(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listImagesAsync(array $args = [])
 * @method \Aws\Result putImage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putImageAsync(array $args = [])
 * @method \Aws\Result setRepositoryPolicy(array $args = [])
 * @method \GuzzleHttp\Promise\Promise setRepositoryPolicyAsync(array $args = [])
 * @method \Aws\Result uploadLayerPart(array $args = [])
 * @method \GuzzleHttp\Promise\Promise uploadLayerPartAsync(array $args = [])
 */
class EcrClient extends AwsClient {}
