<?php
namespace Aws\CloudWatchEvents;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon CloudWatch Events** service.
 *
 * @method \Aws\Result deleteRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteRuleAsync(array $args = [])
 * @method \Aws\Result describeRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeRuleAsync(array $args = [])
 * @method \Aws\Result disableRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disableRuleAsync(array $args = [])
 * @method \Aws\Result enableRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise enableRuleAsync(array $args = [])
 * @method \Aws\Result listRuleNamesByTarget(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRuleNamesByTargetAsync(array $args = [])
 * @method \Aws\Result listRules(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRulesAsync(array $args = [])
 * @method \Aws\Result listTargetsByRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listTargetsByRuleAsync(array $args = [])
 * @method \Aws\Result putEvents(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putEventsAsync(array $args = [])
 * @method \Aws\Result putRule(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putRuleAsync(array $args = [])
 * @method \Aws\Result putTargets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putTargetsAsync(array $args = [])
 * @method \Aws\Result removeTargets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise removeTargetsAsync(array $args = [])
 * @method \Aws\Result testEventPattern(array $args = [])
 * @method \GuzzleHttp\Promise\Promise testEventPatternAsync(array $args = [])
 */
class CloudWatchEventsClient extends AwsClient {}
