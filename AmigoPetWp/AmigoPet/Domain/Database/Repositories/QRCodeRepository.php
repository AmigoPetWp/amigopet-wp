<?php
namespace AmigoPetWp\Domain\Database\Repositories;

use AmigoPetWp\Domain\Entities\QRCode;

class QRCodeRepository extends AbstractRepository {
    protected function getTableName(): string {
        return 'apwp_qrcodes';
    }

    protected function createEntity(array $data): QRCode {
        $qrcode = new QRCode(
            (int)$data['pet_id'],
            $data['code'],
            $data['tracking_url']
        );

        if (isset($data['id'])) {
            $qrcode->setId((int)$data['id']);
        }

        if (isset($data['status'])) {
            $qrcode->setStatus($data['status']);
        }

        if (isset($data['created_at'])) {
            $qrcode->setCreatedAt(new \DateTime($data['created_at']));
        }

        if (isset($data['updated_at'])) {
            $qrcode->setUpdatedAt(new \DateTime($data['updated_at']));
        }

        return $qrcode;
    }

    protected function toDatabase($entity): array {
        if (!$entity instanceof QRCode) {
            throw new \InvalidArgumentException('Entity must be an instance of QRCode');
        }

        return [
            'pet_id' => $entity->getPetId(),
            'code' => $entity->getCode(),
            'tracking_url' => $entity->getTrackingUrl(),
            'status' => $entity->getStatus(),
            'created_at' => $entity->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updated_at' => $entity->getUpdatedAt()?->format('Y-m-d H:i:s')
        ];
    }

    public function findByCode(string $code): ?QRCode {
        $results = $this->findAll(['code' => $code]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByPet(int $petId): array {
        return $this->findAll(['pet_id' => $petId]);
    }

    public function search(string $term): array {
        $sql = $this->wpdb->prepare(
            "SELECT * FROM {$this->table} 
            WHERE code LIKE %s 
            OR tracking_url LIKE %s",
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
                status,
                DATE(created_at) as date
            FROM {$this->table}
            {$whereClause}
            GROUP BY status, DATE(created_at)
            ORDER BY date DESC",
            $params
        );
        
        return $this->wpdb->get_results($sql, ARRAY_A);
    }
}
