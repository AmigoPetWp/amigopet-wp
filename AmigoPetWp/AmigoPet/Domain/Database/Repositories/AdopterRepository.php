<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\Adopter;

class AdopterRepository extends AbstractRepository {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    protected function getTableName(): string {
        return 'apwp_adopters';
    }

    protected function createEntity(array $data): Adopter {
        $adopter = new Adopter(
            $data['name'],
            $data['email'],
            $data['document']
        );

        if (isset($data['id'])) {
            $adopter->setId((int)$data['id']);
        }

        if (isset($data['phone'])) {
            $adopter->setPhone($data['phone']);
        }

        if (isset($data['address'])) {
            $adopter->setAddress($data['address']);
        }

        if (isset($data['created_at'])) {
            $adopter->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $adopter->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $adopter;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof Adopter) {
            throw new \InvalidArgumentException('Entity must be an instance of Adopter');
        }

        return [
            'name' => $entity->getName(),
            'email' => $entity->getEmail(),
            'document' => $entity->getDocument(),
            'phone' => $entity->getPhone(),
            'address' => $entity->getAddress()
        ];
    }

    public function findByEmail(string $email): ?Adopter {
        $results = $this->findAll(['email' => $email]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByDocument(string $document): ?Adopter {
        $results = $this->findAll(['document' => $document]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByPhone(string $phone): ?Adopter {
        $results = $this->findAll(['phone' => $phone]);
        return !empty($results) ? $results[0] : null;
    }

    public function getAdopterReport(?string $startDate = null, ?string $endDate = null): array {
        $where = ['1=1'];
        $params = [];

        if ($startDate && $endDate) {
            $where[] = 'created_at BETWEEN %s AND %s';
            $params[] = $startDate . ' 00:00:00';
            $params[] = $endDate . ' 23:59:59';
        }

        $whereClause = implode(' AND ', $where);

        $query = $this->wpdb->prepare(
            "SELECT 
                COUNT(*) as total_adopters,
                COUNT(DISTINCT document) as unique_documents,
                COUNT(DISTINCT email) as unique_emails,
                COUNT(DISTINCT phone) as unique_phones,
                COUNT(CASE WHEN address IS NOT NULL THEN 1 END) as with_address,
                (SELECT COUNT(*) 
                 FROM {$this->wpdb->prefix}apwp_adoptions a 
                 WHERE a.adopter_id IN (SELECT id FROM {$this->table})
                 AND a.status = 'completed') as successful_adoptions,
                (SELECT COUNT(DISTINCT adopter_id) 
                 FROM {$this->wpdb->prefix}apwp_adoptions 
                 WHERE status = 'completed') as adopters_with_pets
            FROM {$this->table}
            WHERE {$whereClause}",
            $params
        );

        return $this->wpdb->get_row($query, ARRAY_A);
    }

    public function search(string $term): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE name LIKE %s 
            OR email LIKE %s 
            OR document LIKE %s",
            "%{$term}%",
            "%{$term}%",
            "%{$term}%"
        );
        
        return array_map(
            [$this, 'createEntity'],
            $this->wpdb->get_results($sql, ARRAY_A)
        );
    }

    public function getReport(?string $startDate = null, ?string $endDate = null): array {
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
                DATE(created_at) as date
            FROM {$this->table}
            {$whereClause}
            GROUP BY DATE(created_at)
            ORDER BY date DESC",
            $params
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

}
