<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\Adopter;

class AdopterRepository extends AbstractRepository
{


    protected function initTable(): void
    {
        $this->table = $this->getTableName('adopters');
    }

    protected function createEntity(array $row): object
    {
        $adopter = new Adopter(
            $row['name'],
            $row['email'],
            $row['document']
        );

        if (isset($row['id'])) {
            $adopter->setId((int) $row['id']);
        }

        if (isset($row['phone'])) {
            $adopter->setPhone($row['phone']);
        }

        if (isset($row['address'])) {
            $adopter->setAddress($row['address']);
        }

        if (isset($row['created_at'])) {
            $adopter->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $adopter->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $adopter;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof Adopter) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of Adopter', 'amigopet'));
        }

        return [
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'document' => $entity->getDocument(),
            'phone' => $entity->getPhone(),
            'address' => $entity->getAddress()
        ];
    }

    public function findByEmail(string $email): ?Adopter
    {
        $results = $this->findAll(['email' => $email]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByDocument(string $document): ?Adopter
    {
        $results = $this->findAll(['document' => $document]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByPhone(string $phone): ?Adopter
    {
        $results = $this->findAll(['phone' => $phone]);
        return !empty($results) ? $results[0] : null;
    }

    public function getAdopterReport(?string $startDate = null, ?string $endDate = null): array
    {
        $wpdb = $this->wpdb;
        $table = $this->sanitizeIdentifier($this->table);
        $adoptionsTable = $this->sanitizeIdentifier($this->wpdb->prefix . 'apwp_adoptions');
        if ($table === '' || $adoptionsTable === '') {
            return [];
        }
        $start = ($startDate ?: '1970-01-01') . ' 00:00:00';
        $end = ($endDate ?: '9999-12-31') . ' 23:59:59';
        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                COUNT(*) as total_adopters,
                COUNT(DISTINCT document) as unique_documents,
                COUNT(DISTINCT email) as unique_emails,
                COUNT(DISTINCT phone) as unique_phones,
                COUNT(CASE WHEN address IS NOT NULL THEN 1 END) as with_address,
                (SELECT COUNT(*) 
                 FROM %i a 
                 WHERE a.adopter_id IN (SELECT id FROM %i)
                 AND a.status = 'completed') as successful_adoptions,
                (SELECT COUNT(DISTINCT adopter_id) 
                 FROM %i
                 WHERE status = 'completed') as adopters_with_pets
            FROM %i
            WHERE created_at BETWEEN %s AND %s",
                $adoptionsTable,
                $table,
                $adoptionsTable,
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
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
                    'SELECT * FROM %i WHERE name LIKE %s OR email LIKE %s OR document LIKE %s',
                    $table,
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
                'SELECT COUNT(*) as total, DATE(created_at) as date FROM %i WHERE created_at BETWEEN %s AND %s GROUP BY DATE(created_at) ORDER BY date DESC',
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
    }

}