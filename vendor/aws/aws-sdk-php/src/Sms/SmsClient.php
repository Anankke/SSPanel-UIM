<?php
namespace Aws\Sms;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Server Migration Service** service.
 * @method \Aws\Result createReplicationJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createReplicationJobAsync(array $args = [])
 * @method \Aws\Result deleteReplicationJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteReplicationJobAsync(array $args = [])
 * @method \Aws\Result deleteServerCatalog(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteServerCatalogAsync(array $args = [])
 * @method \Aws\Result disassociateConnector(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disassociateConnectorAsync(array $args = [])
 * @method \Aws\Result getConnectors(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getConnectorsAsync(array $args = [])
 * @method \Aws\Result getReplicationJobs(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReplicationJobsAsync(array $args = [])
 * @method \Aws\Result getReplicationRuns(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getReplicationRunsAsync(array $args = [])
 * @method \Aws\Result getServers(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getServersAsync(array $args = [])
 * @method \Aws\Result importServerCatalog(array $args = [])
 * @method \GuzzleHttp\Promise\Promise importServerCatalogAsync(array $args = [])
 * @method \Aws\Result startOnDemandReplicationRun(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startOnDemandReplicationRunAsync(array $args = [])
 * @method \Aws\Result updateReplicationJob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateReplicationJobAsync(array $args = [])
 */
class SmsClient extends AwsClient {}
