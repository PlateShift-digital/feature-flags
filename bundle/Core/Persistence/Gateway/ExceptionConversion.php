<?php

declare(strict_types=1);

namespace PlateShift\FeatureFlagBundle\Core\Persistence\Gateway;

use Doctrine\DBAL\DBALException;
use Exception;
use PlateShift\FeatureFlagBundle\Core\Persistence\Gateway;
use PDOException;
use RuntimeException;

class ExceptionConversion extends Gateway
{
    public Gateway $innerGateway;

    public function __construct(Gateway $gateway)
    {
        $this->innerGateway = $gateway;
    }

    public function load(string $identifier, string $scope): ?array
    {
        try {
            return $this->innerGateway->load($identifier, $scope);
        } catch (Exception | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    public function insert(string $identifier, string $scope, bool $enabled): void
    {
        try {
            $this->innerGateway->insert($identifier, $scope, $enabled);
        } catch (Exception | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    public function delete(string $identifier, string $scope): void
    {
        try {
            $this->innerGateway->delete($identifier, $scope);
        } catch (Exception | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    public function update(string $identifier, string $scope, bool $enabled): void
    {
        try {
            $this->innerGateway->update($identifier, $scope, $enabled);
        } catch (Exception | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    public function list(string $scope): array
    {
        try {
            return $this->innerGateway->list($scope);
        } catch (Exception | PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }
}
