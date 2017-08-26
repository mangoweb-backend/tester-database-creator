<?php declare(strict_types = 1);

namespace Mangoweb\Tester\DatabaseCreator;


class Mutex
{

	/** @var string */
	private $dir;

	/** @var array (name => handle) */
	private $locks;


	public function __construct(string $dir)
	{
		$this->dir = $dir;
	}


	/**
	 * @return mixed value returned by callback
	 */
	public function synchronized(string $key, callable $callback)
	{
		$this->lock($key);

		try {
			return $callback();
		} finally {
			$this->unlock($key);
		}
	}


	protected function lock(string $key): void
	{
		$key = $this->getKey($key);
		if (isset($this->locks[$key])) {
			throw new \LogicException('Trying to acquire the same lock multiple times');
		}

		$path = $this->dir . '/lock-' . $key;
		$this->locks[$key] = fopen($path, 'w');
		flock($this->locks[$key], LOCK_EX);
	}


	protected function unlock(string $key): void
	{
		$key = $this->getKey($key);
		if (!isset($this->locks[$key])) {
			throw new \LogicException('Trying to release a lock which has been already released');
		}

		flock($this->locks[$key], LOCK_UN);
		fclose($this->locks[$key]);
		unset($this->locks[$key]);
	}


	protected function getKey(string $key): string
	{
		return md5($key);
	}

}
