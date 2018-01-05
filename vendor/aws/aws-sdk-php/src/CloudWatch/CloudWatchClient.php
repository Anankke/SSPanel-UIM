<?php
namespace Aws\CloudWatch;

use Aws\AwsClient;

/**
 * This client is used to interact with the **Amazon CloudWatch** service.
 *
 * @method \Aws\Result deleteAlarms(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteAlarmsAsync(array $args = [])
 * @method \Aws\Result describeAlarmHistory(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAlarmHistoryAsync(array $args = [])
 * @method \Aws\Result describeAlarms(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAlarmsAsync(array $args = [])
 * @method \Aws\Result describeAlarmsForMetric(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeAlarmsForMetricAsync(array $args = [])
 * @method \Aws\Result disableAlarmActions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise disableAlarmActionsAsync(array $args = [])
 * @method \Aws\Result enableAlarmActions(array $args = [])
 * @method \GuzzleHttp\Promise\Promise enableAlarmActionsAsync(array $args = [])
 * @method \Aws\Result getMetricStatistics(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getMetricStatisticsAsync(array $args = [])
 * @method \Aws\Result listMetrics(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listMetricsAsync(array $args = [])
 * @method \Aws\Result putMetricAlarm(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putMetricAlarmAsync(array $args = [])
 * @method \Aws\Result putMetricData(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putMetricDataAsync(array $args = [])
 * @method \Aws\Result setAlarmState(array $args = [])
 * @method \GuzzleHttp\Promise\Promise setAlarmStateAsync(array $args = [])
 */
class CloudWatchClient extends AwsClient {}
