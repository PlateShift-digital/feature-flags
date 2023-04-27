<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Connection as ConnectionInterface;
use PlateShift\FeatureFlagBundle\Core\Persistence\Gateway;
use InvalidArgumentException;
use PDO;

class DoctrineDatabase extends Gateway
{
    protected Connection $connection;

    public function __construct(ConnectionInterface $connection)
    {
        if (! $connection instanceof Connection) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected connection to be of type "%s" but "%s" given.',
                    Connection::class,
                    get_class($connection),
                )
            );
        }

        $this->connection = $connection;
    }

    public function load(string $identifier, string $scope): ?array
    {
        $query             = $this->connection->createQueryBuilder();
        $expressionBuilder = $query->expr();

        $query
            ->select(...$this->getColumns())
            ->from(self::TABLE_FEATURE_FLAG)
            ->where($expressionBuilder->andX(
                $expressionBuilder->eq(self::COLUMN_IDENTIFIER, ':identifier'),
                $expressionBuilder->eq(self::COLUMN_SCOPE, ':scope')
            ))
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR);

        return $query->execute()->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function insert(string $identifier, string $scope, bool $enabled): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert(self::TABLE_FEATURE_FLAG)
            ->setValue(self::COLUMN_IDENTIFIER, ':identifier')
            ->setValue(self::COLUMN_SCOPE, ':scope')
            ->setValue(self::COLUMN_ENABLED, ':enabled')
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR)
            ->setParameter(':enabled', $enabled, PDO::PARAM_BOOL);

        $query->execute();
    }

    public function delete(string $identifier, string $scope): void
    {
        $query             = $this->connection->createQueryBuilder();
        $expressionBuilder = $query->expr();

        $query
            ->delete(self::TABLE_FEATURE_FLAG)
            ->where($expressionBuilder->andX(
                $expressionBuilder->eq(self::COLUMN_IDENTIFIER, ':identifier'),
                $expressionBuilder->eq(self::COLUMN_SCOPE, ':scope')
            ))
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR);

        $query->execute();
    }

    public function update(string $identifier, string $scope, bool $enabled): void
    {
        $query             = $this->connection->createQueryBuilder();
        $expressionBuilder = $query->expr();

        $query
            ->update(self::TABLE_FEATURE_FLAG)
            ->where($expressionBuilder->andX(
                $expressionBuilder->eq(self::COLUMN_IDENTIFIER, ':identifier'),
                $expressionBuilder->eq(self::COLUMN_SCOPE, ':scope')
            ))
            ->set(self::COLUMN_ENABLED, ':enabled')
            ->setParameter(':identifier', $identifier, PDO::PARAM_STR)
            ->setParameter(':scope', $scope, PDO::PARAM_STR)
            ->setParameter(':enabled', $enabled, PDO::PARAM_BOOL);

        $query->execute();
    }

    public function list(string $scope): array
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select(...$this->getColumns())
            ->from(self::TABLE_FEATURE_FLAG)
            ->where($query->expr()->eq(self::COLUMN_SCOPE, ':scope'))
            ->setParameter(':scope', $scope, PDO::PARAM_STR);

        return $query->execute()->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getColumns(): array
    {
        return [
            self::COLUMN_IDENTIFIER,
            self::COLUMN_SCOPE,
            self::COLUMN_ENABLED,
        ];
    }
}
