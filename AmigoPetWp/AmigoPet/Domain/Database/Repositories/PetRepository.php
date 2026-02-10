<?php
declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Pet;

class PetRepository extends AbstractRepository
{
    protected function getTableName(): string
    {
        return 'apwp_pets';
    }

    protected function createEntity(array $data): Pet
    {
        $pet = new Pet(
            $data['name'],
            (int) $data['species_id'],
            (int) $data['breed_id'],
            (int) $data['organization_id']
        );

        if (isset($data['id'])) {
            $pet->setId((int) $data['id']);
        }

        if (isset($data['size'])) {
            $pet->setSize($data['size']);
        }

        if (isset($data['description'])) {
            $pet->setDescription($data['description']);
        }

        if (isset($data['status'])) {
            $pet->setStatus($data['status']);
        }

        if (isset($data['age'])) {
            $pet->setAge((int) $data['age']);
        }

        if (isset($data['rga'])) {
            $pet->setRGA($data['rga']);
        }

        if (isset($data['microchip_number'])) {
            $pet->setMicrochipNumber($data['microchip_number']);
        }

        if (isset($data['health_info'])) {
            $pet->setHealthInfo(is_string($data['health_info']) ? json_decode($data['health_info'], true) : $data['health_info']);
        }

        if (isset($data['qrcode_id'])) {
            $pet->setQRCodeId((int) $data['qrcode_id']);
        }

        if (isset($data['created_at'])) {
            $pet->setCreatedAt(new \DateTime($data['created_at']));
        }

        return $pet;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Pet) {
            throw new \InvalidArgumentException('Entity must be an instance of Pet');
        }

        return [
            'name' => $entity->getName(),
            'species_id' => $entity->getSpeciesId(),
            'breed_id' => $entity->getBreedId(),
            'organization_id' => $entity->getOrganizationId(),
            'size' => $entity->getSize(),
            'age' => $entity->getAge(),
            'description' => $entity->getDescription(),
            'status' => $entity->getStatus(),
            'rga' => $entity->getRGA(),
            'microchip_number' => $entity->getMicrochipNumber(),
            'health_info' => json_encode($entity->getHealthInfo()),
            'qrcode_id' => $entity->getQRCodeId(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findByOrganization(int $organizationId): array
    {
        return $this->findAll(['organization_id' => $organizationId]);
    }

    public function findBySpecies(int $speciesId): array
    {
        return $this->findAll(['species_id' => $speciesId]);
    }

    public function findByBreed(int $breedId): array
    {
        return $this->findAll(['breed_id' => $breedId]);
    }

    public function findByStatus(string $status): array
    {
        return $this->findAll(['status' => $status]);
    }

    public function search(string $term): array
    {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE name LIKE %s 
            OR description LIKE %s 
            OR rga LIKE %s
            OR microchip_number LIKE %s",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%"
        );

        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $where = [];
        $params = [];

        if ($startDate) {
            $where[] = "created_at >= %s";
            $params[] = $startDate;
        }

        if ($endDate) {
            $where[] = "created_at <= %s";
            $params[] = $endDate;
        }

        $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

        $sql = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total,
                species_id,
                breed_id,
                status,
                DATE(created_at) as date
            FROM {$this->table}
            {$whereClause}
            GROUP BY species_id, breed_id, status, DATE(created_at)
            ORDER BY date DESC",
            $params
        );

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function findAvailableForAdoption(): array
    {
        return $this->findAll(['status' => 'available']);
    }

    public function findAdopted(): array
    {
        return $this->findAll(['status' => 'adopted']);
    }

    public function findByFilters(array $filters): array
    {
        $criteria = [];

        if (isset($filters['species_id'])) {
            $criteria['species_id'] = (int) $filters['species_id'];
        }

        if (isset($filters['breed_id'])) {
            $criteria['breed_id'] = (int) $filters['breed_id'];
        }

        if (isset($filters['size'])) {
            $criteria['size'] = $filters['size'];
        }


        if (isset($filters['age_min'])) {
            $criteria['age_min'] = (int) $filters['age_min'];
        }

        if (isset($filters['age_max'])) {
            $criteria['age_max'] = (int) $filters['age_max'];
        }

        if (isset($filters['organization_id'])) {
            $criteria['organization_id'] = (int) $filters['organization_id'];
        }

        if (isset($filters['status'])) {
            $criteria['status'] = $filters['status'];
        }

        return $this->findAll($criteria);
    }
}
