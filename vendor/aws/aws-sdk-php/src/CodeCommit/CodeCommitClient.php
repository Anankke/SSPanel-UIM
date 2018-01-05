<?php
namespace Aws\CodeCommit;

use Aws\AwsClient;

/**
 * This client is used to interact with the **AWS CodeCommit** service.
 *
 * @method \Aws\Result batchGetRepositories(array $args = [])
 * @method \GuzzleHttp\Promise\Promise batchGetRepositoriesAsync(array $args = [])
 * @method \Aws\Result createBranch(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createBranchAsync(array $args = [])
 * @method \Aws\Result createRepository(array $args = [])
 * @method \GuzzleHttp\Promise\Promise createRepositoryAsync(array $args = [])
 * @method \Aws\Result deleteRepository(array $args = [])
 * @method \GuzzleHttp\Promise\Promise deleteRepositoryAsync(array $args = [])
 * @method \Aws\Result getBlob(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBlobAsync(array $args = [])
 * @method \Aws\Result getBranch(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getBranchAsync(array $args = [])
 * @method \Aws\Result getCommit(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getCommitAsync(array $args = [])
 * @method \Aws\Result getDifferences(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getDifferencesAsync(array $args = [])
 * @method \Aws\Result getRepository(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getRepositoryAsync(array $args = [])
 * @method \Aws\Result getRepositoryTriggers(array $args = [])
 * @method \GuzzleHttp\Promise\Promise getRepositoryTriggersAsync(array $args = [])
 * @method \Aws\Result listBranches(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listBranchesAsync(array $args = [])
 * @method \Aws\Result listRepositories(array $args = [])
 * @method \GuzzleHttp\Promise\Promise listRepositoriesAsync(array $args = [])
 * @method \Aws\Result putRepositoryTriggers(array $args = [])
 * @method \GuzzleHttp\Promise\Promise putRepositoryTriggersAsync(array $args = [])
 * @method \Aws\Result testRepositoryTriggers(array $args = [])
 * @method \GuzzleHttp\Promise\Promise testRepositoryTriggersAsync(array $args = [])
 * @method \Aws\Result updateDefaultBranch(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateDefaultBranchAsync(array $args = [])
 * @method \Aws\Result updateRepositoryDescription(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateRepositoryDescriptionAsync(array $args = [])
 * @method \Aws\Result updateRepositoryName(array $args = [])
 * @method \GuzzleHttp\Promise\Promise updateRepositoryNameAsync(array $args = [])
 */
class CodeCommitClient extends AwsClient {}
