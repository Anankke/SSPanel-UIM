<?php
namespace Aws\Rekognition;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon Rekognition** service.
 * @method \Aws\Result compareFaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise compareFacesAsync(array $args = [])
 * @method \Aws\Result createCollection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createCollectionAsync(array $args = [])
 * @method \Aws\Result deleteCollection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteCollectionAsync(array $args = [])
 * @method \Aws\Result deleteFaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteFacesAsync(array $args = [])
 * @method \Aws\Result detectFaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise detectFacesAsync(array $args = [])
 * @method \Aws\Result detectLabels(array $args = [])
 * @method \GuzzleHttp\Promise\Promise detectLabelsAsync(array $args = [])
 * @method \Aws\Result indexFaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise indexFacesAsync(array $args = [])
 * @method \Aws\Result listCollections(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCollectionsAsync(array $args = [])
 * @method \Aws\Result listFaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listFacesAsync(array $args = [])
 * @method \Aws\Result searchFaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchFacesAsync(array $args = [])
 * @method \Aws\Result searchFacesByImage(array $args = [])
 * @method \GuzzleHttp\Promise\Promise searchFacesByImageAsync(array $args = [])
 */
class RekognitionClient extends AwsClient {}
