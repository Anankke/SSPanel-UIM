<?php
namespace Aws;

/**
 * Builds AWS clients based on configuration settings.
 *
 * @method \Aws\Acm\AcmClient createAcm(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionAcm(array $args = [])
 * @method \Aws\ApiGateway\ApiGatewayClient createApiGateway(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionApiGateway(array $args = [])
 * @method \Aws\ApplicationAutoScaling\ApplicationAutoScalingClient createApplicationAutoScaling(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionApplicationAutoScaling(array $args = [])
 * @method \Aws\ApplicationDiscoveryService\ApplicationDiscoveryServiceClient createApplicationDiscoveryService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionApplicationDiscoveryService(array $args = [])
 * @method \Aws\Appstream\AppstreamClient createAppstream(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionAppstream(array $args = [])
 * @method \Aws\AutoScaling\AutoScalingClient createAutoScaling(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionAutoScaling(array $args = [])
 * @method \Aws\Batch\BatchClient createBatch(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionBatch(array $args = [])
 * @method \Aws\Budgets\BudgetsClient createBudgets(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionBudgets(array $args = [])
 * @method \Aws\CloudDirectory\CloudDirectoryClient createCloudDirectory(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudDirectory(array $args = [])
 * @method \Aws\CloudFormation\CloudFormationClient createCloudFormation(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudFormation(array $args = [])
 * @method \Aws\CloudFront\CloudFrontClient createCloudFront(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudFront(array $args = [])
 * @method \Aws\CloudHsm\CloudHsmClient createCloudHsm(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudHsm(array $args = [])
 * @method \Aws\CloudSearch\CloudSearchClient createCloudSearch(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudSearch(array $args = [])
 * @method \Aws\CloudSearchDomain\CloudSearchDomainClient createCloudSearchDomain(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudSearchDomain(array $args = [])
 * @method \Aws\CloudTrail\CloudTrailClient createCloudTrail(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudTrail(array $args = [])
 * @method \Aws\CloudWatch\CloudWatchClient createCloudWatch(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudWatch(array $args = [])
 * @method \Aws\CloudWatchEvents\CloudWatchEventsClient createCloudWatchEvents(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudWatchEvents(array $args = [])
 * @method \Aws\CloudWatchLogs\CloudWatchLogsClient createCloudWatchLogs(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCloudWatchLogs(array $args = [])
 * @method \Aws\CodeBuild\CodeBuildClient createCodeBuild(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCodeBuild(array $args = [])
 * @method \Aws\CodeCommit\CodeCommitClient createCodeCommit(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCodeCommit(array $args = [])
 * @method \Aws\CodeDeploy\CodeDeployClient createCodeDeploy(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCodeDeploy(array $args = [])
 * @method \Aws\CodePipeline\CodePipelineClient createCodePipeline(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCodePipeline(array $args = [])
 * @method \Aws\CognitoIdentity\CognitoIdentityClient createCognitoIdentity(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCognitoIdentity(array $args = [])
 * @method \Aws\CognitoIdentityProvider\CognitoIdentityProviderClient createCognitoIdentityProvider(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCognitoIdentityProvider(array $args = [])
 * @method \Aws\CognitoSync\CognitoSyncClient createCognitoSync(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCognitoSync(array $args = [])
 * @method \Aws\ConfigService\ConfigServiceClient createConfigService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionConfigService(array $args = [])
 * @method \Aws\CostandUsageReportService\CostandUsageReportServiceClient createCostandUsageReportService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionCostandUsageReportService(array $args = [])
 * @method \Aws\DataPipeline\DataPipelineClient createDataPipeline(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDataPipeline(array $args = [])
 * @method \Aws\DatabaseMigrationService\DatabaseMigrationServiceClient createDatabaseMigrationService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDatabaseMigrationService(array $args = [])
 * @method \Aws\DeviceFarm\DeviceFarmClient createDeviceFarm(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDeviceFarm(array $args = [])
 * @method \Aws\DirectConnect\DirectConnectClient createDirectConnect(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDirectConnect(array $args = [])
 * @method \Aws\DirectoryService\DirectoryServiceClient createDirectoryService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDirectoryService(array $args = [])
 * @method \Aws\DynamoDb\DynamoDbClient createDynamoDb(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDynamoDb(array $args = [])
 * @method \Aws\DynamoDbStreams\DynamoDbStreamsClient createDynamoDbStreams(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionDynamoDbStreams(array $args = [])
 * @method \Aws\Ec2\Ec2Client createEc2(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionEc2(array $args = [])
 * @method \Aws\Ecr\EcrClient createEcr(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionEcr(array $args = [])
 * @method \Aws\Ecs\EcsClient createEcs(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionEcs(array $args = [])
 * @method \Aws\Efs\EfsClient createEfs(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionEfs(array $args = [])
 * @method \Aws\ElastiCache\ElastiCacheClient createElastiCache(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionElastiCache(array $args = [])
 * @method \Aws\ElasticBeanstalk\ElasticBeanstalkClient createElasticBeanstalk(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionElasticBeanstalk(array $args = [])
 * @method \Aws\ElasticLoadBalancing\ElasticLoadBalancingClient createElasticLoadBalancing(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionElasticLoadBalancing(array $args = [])
 * @method \Aws\ElasticLoadBalancingV2\ElasticLoadBalancingV2Client createElasticLoadBalancingV2(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionElasticLoadBalancingV2(array $args = [])
 * @method \Aws\ElasticTranscoder\ElasticTranscoderClient createElasticTranscoder(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionElasticTranscoder(array $args = [])
 * @method \Aws\ElasticsearchService\ElasticsearchServiceClient createElasticsearchService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionElasticsearchService(array $args = [])
 * @method \Aws\Emr\EmrClient createEmr(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionEmr(array $args = [])
 * @method \Aws\Firehose\FirehoseClient createFirehose(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionFirehose(array $args = [])
 * @method \Aws\GameLift\GameLiftClient createGameLift(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionGameLift(array $args = [])
 * @method \Aws\Glacier\GlacierClient createGlacier(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionGlacier(array $args = [])
 * @method \Aws\Health\HealthClient createHealth(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionHealth(array $args = [])
 * @method \Aws\Iam\IamClient createIam(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionIam(array $args = [])
 * @method \Aws\ImportExport\ImportExportClient createImportExport(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionImportExport(array $args = [])
 * @method \Aws\Inspector\InspectorClient createInspector(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionInspector(array $args = [])
 * @method \Aws\Iot\IotClient createIot(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionIot(array $args = [])
 * @method \Aws\IotDataPlane\IotDataPlaneClient createIotDataPlane(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionIotDataPlane(array $args = [])
 * @method \Aws\Kinesis\KinesisClient createKinesis(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionKinesis(array $args = [])
 * @method \Aws\KinesisAnalytics\KinesisAnalyticsClient createKinesisAnalytics(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionKinesisAnalytics(array $args = [])
 * @method \Aws\Kms\KmsClient createKms(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionKms(array $args = [])
 * @method \Aws\Lambda\LambdaClient createLambda(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionLambda(array $args = [])
 * @method \Aws\LexRuntimeService\LexRuntimeServiceClient createLexRuntimeService(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionLexRuntimeService(array $args = [])
 * @method \Aws\Lightsail\LightsailClient createLightsail(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionLightsail(array $args = [])
 * @method \Aws\MachineLearning\MachineLearningClient createMachineLearning(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionMachineLearning(array $args = [])
 * @method \Aws\MarketplaceCommerceAnalytics\MarketplaceCommerceAnalyticsClient createMarketplaceCommerceAnalytics(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionMarketplaceCommerceAnalytics(array $args = [])
 * @method \Aws\MarketplaceMetering\MarketplaceMeteringClient createMarketplaceMetering(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionMarketplaceMetering(array $args = [])
 * @method \Aws\OpsWorks\OpsWorksClient createOpsWorks(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionOpsWorks(array $args = [])
 * @method \Aws\OpsWorksCM\OpsWorksCMClient createOpsWorksCM(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionOpsWorksCM(array $args = [])
 * @method \Aws\Pinpoint\PinpointClient createPinpoint(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionPinpoint(array $args = [])
 * @method \Aws\Polly\PollyClient createPolly(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionPolly(array $args = [])
 * @method \Aws\Rds\RdsClient createRds(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionRds(array $args = [])
 * @method \Aws\Redshift\RedshiftClient createRedshift(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionRedshift(array $args = [])
 * @method \Aws\Rekognition\RekognitionClient createRekognition(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionRekognition(array $args = [])
 * @method \Aws\Route53\Route53Client createRoute53(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionRoute53(array $args = [])
 * @method \Aws\Route53Domains\Route53DomainsClient createRoute53Domains(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionRoute53Domains(array $args = [])
 * @method \Aws\S3\S3Client createS3(array $args = [])
 * @method \Aws\S3\S3MultiRegionClient createMultiRegionS3(array $args = [])
 * @method \Aws\ServiceCatalog\ServiceCatalogClient createServiceCatalog(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionServiceCatalog(array $args = [])
 * @method \Aws\Ses\SesClient createSes(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSes(array $args = [])
 * @method \Aws\Sfn\SfnClient createSfn(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSfn(array $args = [])
 * @method \Aws\Shield\ShieldClient createShield(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionShield(array $args = [])
 * @method \Aws\Sms\SmsClient createSms(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSms(array $args = [])
 * @method \Aws\SnowBall\SnowBallClient createSnowBall(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSnowBall(array $args = [])
 * @method \Aws\Sns\SnsClient createSns(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSns(array $args = [])
 * @method \Aws\Sqs\SqsClient createSqs(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSqs(array $args = [])
 * @method \Aws\Ssm\SsmClient createSsm(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSsm(array $args = [])
 * @method \Aws\StorageGateway\StorageGatewayClient createStorageGateway(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionStorageGateway(array $args = [])
 * @method \Aws\Sts\StsClient createSts(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSts(array $args = [])
 * @method \Aws\Support\SupportClient createSupport(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSupport(array $args = [])
 * @method \Aws\Swf\SwfClient createSwf(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionSwf(array $args = [])
 * @method \Aws\Waf\WafClient createWaf(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionWaf(array $args = [])
 * @method \Aws\WafRegional\WafRegionalClient createWafRegional(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionWafRegional(array $args = [])
 * @method \Aws\WorkSpaces\WorkSpacesClient createWorkSpaces(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionWorkSpaces(array $args = [])
 * @method \Aws\XRay\XRayClient createXRay(array $args = [])
 * @method \Aws\MultiRegionClient createMultiRegionXRay(array $args = [])
 */
class Sdk
{
    const VERSION = '3.22.7';

    /** @var array Arguments for creating clients */
    private $args;

    /**
     * Constructs a new SDK object with an associative array of default
     * client settings.
     *
     * @param array $args
     *
     * @throws \InvalidArgumentException
     * @see Aws\AwsClient::__construct for a list of available options.
     */
    public function __construct(array $args = [])
    {
        $this->args = $args;

        if (!isset($args['handler']) && !isset($args['http_handler'])) {
            $this->args['http_handler'] = default_http_handler();
        }
    }

    public function __call($name, array $args)
    {
        $args = isset($args[0]) ? $args[0] : [];
        if (strpos($name, 'createMultiRegion') === 0) {
            return $this->createMultiRegionClient(substr($name, 17), $args);
        } elseif (strpos($name, 'create') === 0) {
            return $this->createClient(substr($name, 6), $args);
        }

        throw new \BadMethodCallException("Unknown method: {$name}.");
    }

    /**
     * Get a client by name using an array of constructor options.
     *
     * @param string $name Service name or namespace (e.g., DynamoDb, s3).
     * @param array  $args Arguments to configure the client.
     *
     * @return AwsClientInterface
     * @throws \InvalidArgumentException if any required options are missing or
     *                                   the service is not supported.
     * @see Aws\AwsClient::__construct for a list of available options for args.
     */
    public function createClient($name, array $args = [])
    {
        // Get information about the service from the manifest file.
        $service = manifest($name);
        $namespace = $service['namespace'];

        // Instantiate the client class.
        $client = "Aws\\{$namespace}\\{$namespace}Client";
        return new $client($this->mergeArgs($namespace, $service, $args));
    }

    public function createMultiRegionClient($name, array $args = [])
    {
        // Get information about the service from the manifest file.
        $service = manifest($name);
        $namespace = $service['namespace'];

        $klass = "Aws\\{$namespace}\\{$namespace}MultiRegionClient";
        $klass = class_exists($klass) ? $klass : 'Aws\\MultiRegionClient';

        return new $klass($this->mergeArgs($namespace, $service, $args));
    }

    private function mergeArgs($namespace, array $manifest, array $args = [])
    {
        // Merge provided args with stored, service-specific args.
        if (isset($this->args[$namespace])) {
            $args += $this->args[$namespace];
        }

        // Provide the endpoint prefix in the args.
        if (!isset($args['service'])) {
            $args['service'] = $manifest['endpoint'];
        }

        return $args + $this->args;
    }

    /**
     * Determine the endpoint prefix from a client namespace.
     *
     * @param string $name Namespace name
     *
     * @return string
     * @internal
     * @deprecated Use the `\Aws\manifest()` function instead.
     */
    public static function getEndpointPrefix($name)
    {
        return manifest($name)['endpoint'];
    }
}
