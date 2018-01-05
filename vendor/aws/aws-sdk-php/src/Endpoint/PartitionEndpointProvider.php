<?php
namespace Aws\Endpoint;

class PartitionEndpointProvider
{
    /** @var Partition[] */
    private $partitions;
    /** @var string */
    private $defaultPartition;

    public function __construct(array $partitions, $defaultPartition = 'aws')
    {
        $this->partitions = array_map(function (array $definition) {
            return new Partition($definition);
        }, array_values($partitions));
        $this->defaultPartition = $defaultPartition;
    }

    public function __invoke(array $args = [])
    {
        $partition = $this->getPartition(
            isset($args['region']) ? $args['region'] : '',
            isset($args['service']) ? $args['service'] : ''
        );

        return $partition($args);
    }

    /**
     * Returns the partition containing the provided region or the default
     * partition if no match is found.
     *
     * @param string $region
     * @param string $service
     *
     * @return Partition
     */
    public function getPartition($region, $service)
    {
        foreach ($this->partitions as $partition) {
            if ($partition->isRegionMatch($region, $service)) {
                return $partition;
            }
        }

        return $this->getPartitionByName($this->defaultPartition);
    }

    /**
     * Returns the partition with the provided name or null if no partition with
     * the provided name can be found.
     *
     * @param string $name
     * 
     * @return Partition|null
     */
    public function getPartitionByName($name)
    {
        foreach ($this->partitions as $partition) {
            if ($name === $partition->getName()) {
                return $partition;
            }
        }
    }

    /**
     * Creates and returns the default SDK partition provider.
     *
     * @return PartitionEndpointProvider
     */
    public static function defaultProvider()
    {
        $data = \Aws\load_compiled_json(__DIR__ . '/../data/endpoints.json');

        return new self($data['partitions']);
    }
}
