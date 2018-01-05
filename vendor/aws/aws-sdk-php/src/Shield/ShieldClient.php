<?php
namespace Aws\Shield;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS Shield** service.
 * @method \Aws\Result createProtection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createProtectionAsync(array $args = [])
 * @method \Aws\Result createSubscription(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createSubscriptionAsync(array $args = [])
 * @method \Aws\Result deleteProtection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProtectionAsync(array $args = [])
 * @method \Aws\Result deleteSubscription(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteSubscriptionAsync(array $args = [])
 * @method \Aws\Result describeAttack(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAttackAsync(array $args = [])
 * @method \Aws\Result describeProtection(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeProtectionAsync(array $args = [])
 * @method \Aws\Result describeSubscription(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeSubscriptionAsync(array $args = [])
 * @method \Aws\Result listAttacks(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listAttacksAsync(array $args = [])
 * @method \Aws\Result listProtections(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProtectionsAsync(array $args = [])
 */
class ShieldClient extends AwsClient {}
