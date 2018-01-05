<?php
namespace Aws\XRay;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS X-Ray** service.
 * @method \Aws\Result batchGetTraces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGetTracesAsync(array $args = [])
 * @method \Aws\Result getServiceGraph(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getServiceGraphAsync(array $args = [])
 * @method \Aws\Result getTraceGraph(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTraceGraphAsync(array $args = [])
 * @method \Aws\Result getTraceSummaries(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getTraceSummariesAsync(array $args = [])
 * @method \Aws\Result putTelemetryRecords(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putTelemetryRecordsAsync(array $args = [])
 * @method \Aws\Result putTraceSegments(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putTraceSegmentsAsync(array $args = [])
 */
class XRayClient extends AwsClient {}
