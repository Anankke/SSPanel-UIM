<?php
namespace Aws\CodeBuild;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS CodeBuild** service.
 * @method \Aws\Result batchGetBuilds(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGetBuildsAsync(array $args = [])
 * @method \Aws\Result batchGetProjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGetProjectsAsync(array $args = [])
 * @method \Aws\Result createProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createProjectAsync(array $args = [])
 * @method \Aws\Result deleteProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteProjectAsync(array $args = [])
 * @method \Aws\Result listBuilds(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBuildsAsync(array $args = [])
 * @method \Aws\Result listBuildsForProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBuildsForProjectAsync(array $args = [])
 * @method \Aws\Result listCuratedEnvironmentImages(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listCuratedEnvironmentImagesAsync(array $args = [])
 * @method \Aws\Result listProjects(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listProjectsAsync(array $args = [])
 * @method \Aws\Result startBuild(array $args = [])
 * @method \GuzzleHttp\Promise\Promise startBuildAsync(array $args = [])
 * @method \Aws\Result stopBuild(array $args = [])
 * @method \GuzzleHttp\Promise\Promise stopBuildAsync(array $args = [])
 * @method \Aws\Result updateProject(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateProjectAsync(array $args = [])
 */
class CodeBuildClient extends AwsClient {}
