<?php
namespace Aws\WorkSpaces;

use Aws\AwsClient;

/**
 * Amazon WorkSpaces client.
 *
 * @method \Aws\Result createTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createTagsAsync(array $args = [])
 * @method \Aws\Result createWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createWorkspacesAsync(array $args = [])
 * @method \Aws\Result deleteTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteTagsAsync(array $args = [])
 * @method \Aws\Result describeTags(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeTagsAsync(array $args = [])
 * @method \Aws\Result describeWorkspaceBundles(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeWorkspaceBundlesAsync(array $args = [])
 * @method \Aws\Result describeWorkspaceDirectories(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeWorkspaceDirectoriesAsync(array $args = [])
 * @method \Aws\Result describeWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeWorkspacesAsync(array $args = [])
 * @method \Aws\Result describeWorkspacesConnectionStatus(array $args = [])
 * @method \GuzzleHttp\Promise\Promise describeWorkspacesConnectionStatusAsync(array $args = [])
 * @method \Aws\Result modifyWorkspaceProperties(array $args = [])
 * @method \GuzzleHttp\Promise\Promise modifyWorkspacePropertiesAsync(array $args = [])
 * @method \Aws\Result rebootWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise rebootWorkspacesAsync(array $args = [])
 * @method \Aws\Result rebuildWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise rebuildWorkspacesAsync(array $args = [])
 * @method \Aws\Result startWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startWorkspacesAsync(array $args = [])
 * @method \Aws\Result stopWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopWorkspacesAsync(array $args = [])
 * @method \Aws\Result terminateWorkspaces(array $args = [])
 * @method \GuzzleHttp\Promise\Promise terminateWorkspacesAsync(array $args = [])
 */
class WorkSpacesClient extends AwsClient {}
