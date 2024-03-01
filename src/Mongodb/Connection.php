<?php
/**
 * @description: MongoDB - Transaction
 *
 * @date 2019-07-22
 */

namespace Zs\Mongodb;


use Jenssegers\Mongodb\Connection as JenssegersConnection;
use MongoDB\Driver\Session;
use Zs\Mongodb\Query\Builder;

class Connection extends JenssegersConnection
{
    /**
     * @var \MongoDB\Driver\Session
     */
    protected ?Session $session = null;

    /**
     * @description: 创建事务
     *
     * @param array $options
     * @date 2019-07-22
     */
    public function beginTransaction(array $options = []): void
    {
        if (!$this->getSession()) {
            $this->session = $this->getMongoClient()->startSession();
            $this->session->startTransaction($options);
        }
    }

    /**
     * @description: 提交
     *
     * @date 2019-07-22
     */
    public function commit(): void
    {
        if ($this->getSession()) {
            $this->session->commitTransaction();
            $this->clearSession();
        }
    }

    /**
     * @description: 回滚
     *
     * @param $toLevel
     * @date 2019-07-22
     */
    public function rollBack($toLevel = null): void
    {
        if ($this->getSession()) {
            $this->session->abortTransaction();
            $this->clearSession();
        }
    }

    /**
     * @description: 清理session
     *
     * @date 2019-07-22
     */
    protected function clearSession()
    {
        $this->session = null;
    }

    /**
     *
     * @param string $collection
     * @return Builder|\Jenssegers\Mongodb\Query\Builder
     * @date 2019-07-22
     */
    public function collection($collection)
    {
        $query = new Query\Builder($this, $this->getPostProcessor());

        return $query->from($collection);
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }
}
