<?php
namespace Concept\Singularity\Cache;

use Psr\SimpleCache\CacheInterface;

class SimpleCache implements CacheInterface
{

    public function __construct()
    {
        $cache = file_get_contents('/tmp/singularity-sc.json');
        $this->cache = json_decode($cache, true) ?? [];
    }

    public function __destruct()
    {
       $f = fopen('/tmp/singularity-sc.json', 'w');
       $json = json_encode($this->cache);
       fwrite($f, $json);
       fclose($f);
    }
    
    /**
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * {@inheritDoc}
     */
    public function get(string $key, $default = null): mixed
    {
        return isset($this->cache[$key])
                ? $this->cache[$key]
                : $default;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        $this->cache[$key] = $value;

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $key): bool
    {
        unset($this->cache[$key]);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getMultiple($keys, $default = null): iterable
    {
        foreach ($keys as $key) {
            yield $this->get($key, $default);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function has($key): bool
    {
        return isset($this->cache[$key]);
    }

    
}