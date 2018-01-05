<?php
namespace Aws\DynamoDb;

use Aws\DynamoDb\Exception\DynamoDbException;

/**
 * The standard connection performs the read and write operations to DynamoDB.
 */
class StandardSessionConnection implements SessionConnectionInterface
{
    /** @var DynamoDbClient The DynamoDB client */
    protected $client;

    /** @var array The session handler config options */
    protected $config;

    /**
     * @param DynamoDbClient $client DynamoDB client
     * @param array          $config Session handler config
     */
    public function __construct(DynamoDbClient $client, array $config = [])
    {
        $this->client = $client;
        $this->config = $config + [
            'table_name'       => 'sessions',
            'hash_key'         => 'id',
            'session_lifetime' => (int) ini_get('session.gc_maxlifetime'),
            'consistent_read'  => true,
            'batch_config'     => [],
        ];
    }

    public function read($id)
    {
        $item = [];
        try {
            // Execute a GetItem command to retrieve the item.
            $result = $this->client->getItem([
                 'TableName'      => $this->config['table_name'],
                 'Key'            => $this->formatKey($id),
                 'ConsistentRead' => (bool) $this->config['consistent_read'],
             ]);

            // Get the item values
            $result = isset($result['Item']) ? $result['Item'] : [];
            foreach ($result as $key => $value) {
                $item[$key] = current($value);
            }
        } catch (DynamoDbException $e) {
            // Could not retrieve item, so return nothing.
        }

        return $item;
    }

    public function write($id, $data, $isChanged)
    {
        // Prepare the attributes
        $expires = time() + $this->config['session_lifetime'];
        $attributes = [
            'expires' => ['Value' => ['N' => (string) $expires]],
            'lock' => ['Action' => 'DELETE'],
        ];
        if ($isChanged) {
            if ($data != '') {
                $attributes['data'] = ['Value' => ['S' => $data]];
            } else {
                $attributes['data'] = ['Action' => 'DELETE'];
            }
        }

        // Perform the UpdateItem command
        try {
            return (bool) $this->client->updateItem([
                'TableName'        => $this->config['table_name'],
                'Key'              => $this->formatKey($id),
                'AttributeUpdates' => $attributes,
            ]);
        } catch (DynamoDbException $e) {
            return $this->triggerError("Error writing session $id: {$e->getMessage()}");
        }
    }

    public function delete($id)
    {
        try {
            return (bool) $this->client->deleteItem([
                'TableName' => $this->config['table_name'],
                'Key'       => $this->formatKey($id),
            ]);
        } catch (DynamoDbException $e) {
            return $this->triggerError("Error deleting session $id: {$e->getMessage()}");
        }
    }

    public function deleteExpired()
    {
        // Create a Scan iterator for finding expired session items
        $scan = $this->client->getPaginator('Scan', [
            'TableName' => $this->config['table_name'],
            'AttributesToGet' => [$this->config['hash_key']],
            'ScanFilter' => [
                'expires' => [
                    'ComparisonOperator' => 'LT',
                    'AttributeValueList' => [['N' => (string) time()]],
                ],
                'lock' => [
                    'ComparisonOperator' => 'NULL',
                ]
            ],
        ]);

        // Create a WriteRequestBatch for deleting the expired items
        $batch = new WriteRequestBatch($this->client, $this->config['batch_config']);

        // Perform Scan and BatchWriteItem (delete) operations as needed
        foreach ($scan->search('Items') as $item) {
            $batch->delete(
                [$this->config['hash_key'] => $item[$this->config['hash_key']]],
                $this->config['table_name']
            );
        }

        // Delete any remaining items that were not auto-flushed
        $batch->flush();
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function formatKey($key)
    {
        return [$this->config['hash_key'] => ['S' => $key]];
    }

    /**
     * @param string $error
     *
     * @return bool
     */
    protected function triggerError($error)
    {
        trigger_error($error, E_USER_WARNING);

        return false;
    }
}
