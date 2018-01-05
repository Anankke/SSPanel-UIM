<?php
namespace Aws\Efs;

use Aws\AwsClient;

/**
 * This client is used to interact with **Amazon EFS**.
 *
 * @method \Aws\Result createFileSystem(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createFileSystemAsync(array $args = [])
 * @method \Aws\Result createMountTarget(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createMountTargetAsync(array $args = [])
 * @method \Aws\Result createTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createTagsAsync(array $args = [])
 * @method \Aws\Result deleteFileSystem(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteFileSystemAsync(array $args = [])
 * @method \Aws\Result deleteMountTarget(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteMountTargetAsync(array $args = [])
 * @method \Aws\Result deleteTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteTagsAsync(array $args = [])
 * @method \Aws\Result describeFileSystems(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeFileSystemsAsync(array $args = [])
 * @method \Aws\Result describeMountTargetSecurityGroups(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeMountTargetSecurityGroupsAsync(array $args = [])
 * @method \Aws\Result describeMountTargets(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeMountTargetsAsync(array $args = [])
 * @method \Aws\Result describeTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeTagsAsync(array $args = [])
 * @method \Aws\Result modifyMountTargetSecurityGroups(array $args = [])
 * @method \GuzzleHttp\Promise\Promise modifyMountTargetSecurityGroupsAsync(array $args = [])
 */
class EfsClient extends AwsClient {}
