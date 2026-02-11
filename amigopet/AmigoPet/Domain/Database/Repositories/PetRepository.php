<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Pet;

class PetRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('pets');
    }

    protected function createEntity(array $row): object
    {
        $pet = new Pet(
            $row['name'],
            (int) $row['species_id'],
            (int) $row['breed_id'],
            (int) $row['organization_id']
        );

        if (isset($row['id'])) {
            $pet->setId((int) $row['id']);
        }

        if (isset($row['size'])) {
            $pet->setSize($row['size']);
        }

        if (isset($row['description'])) {
            $pet->setDescription($row['description']);
        }

        if (isset($row['status'])) {
            $pet->setStatus($row['status']);
        }

        if (isset($row['age'])) {
            $pet->setAge((int) $row['age']);
        }

        if (isset($row['rga'])) {
            $pet->setRGA($row['rga']);
        }

        if (isset($row['microchip_number'])) {
            $pet->setMicrochipNumber($row['microchip_number']);
        }

        if (isset($row['health_info'])) {
            $pet->setHealthInfo(is_string($row['health_info']) ? json_decode($row['health_info'], true) : $row['health_info']);
        }

        if (isset($row['qrcode_id'])) {
            $pet->setQRCodeId((int) $row['qrcode_id']);
        }

        if (isset($row['created_at'])) {
            $pet->setCreatedAt(new \DateTime($row['created_at']));
        }

        return $pet;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Pet) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Pet', 'amigopet'));
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
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $search = '%' . $wpdb->esc_like($term) . '%';

        return array_map(
            [$this, 'createEntity'],
            $wpdb->get_results(
                $wpdb->prepare(
                    'SELECT * FROM %i WHERE name LIKE %s OR description LIKE %s OR rga LIKE %s OR microchip_number LIKE %s',
                    $table,
                    $search,
                    $search,
                    $search,
                    $search
                ),
                ARRAY_A
            )
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        if ($table === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';

        return $wpdb->get_results(
            $wpdb->prepare(
                'SELECT COUNT(*) as total, species_id, breed_id, status, DATE(created_at) as date FROM %i WHERE created_at BETWEEN %s AND %s GROUP BY species_id, breed_id, status, DATE(created_at) ORDER BY date DESC',
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
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