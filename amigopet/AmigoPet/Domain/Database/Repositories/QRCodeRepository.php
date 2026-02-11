<?php declare(strict_types=1);
namespace AmigoPetWp\Domain\Database\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

use AmigoPetWp\Domain\Entities\QRCode;

class QRCodeRepository extends AbstractRepository
{
    protected function initTable(): void
    {
        $this->table = $this->getTableName('qrcodes');
    }

    protected function createEntity(array $row): object
    {
        $qrcode = new QRCode(
            (int) $row['pet_id'],
            $row['code'],
            $row['tracking_url']
        );

        if (isset($row['id'])) {
            $qrcode->setId((int) $row['id']);
        }

        if (isset($row['status'])) {
            $qrcode->setStatus($row['status']);
        }

        if (isset($row['created_at'])) {
            $qrcode->setCreatedAt(new \DateTime($row['created_at']));
        }

        if (isset($row['updated_at'])) {
            $qrcode->setUpdatedAt(new \DateTime($row['updated_at']));
        }

        return $qrcode;
    }

    protected function toDatabase($entity): array
    {
        if (!$entity instanceof QRCode) {
            throw new \InvalidArgumentException(esc_html__('Entity must be an instance of QRCode', 'amigopet'));
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

    public function findByCode(string $code): ?QRCode
    {
        $results = $this->findAll(['code' => $code]);
        return !empty($results) ? $results[0] : null;
    }

    public function findByPet(int $petId): array
    {
        return $this->findAll(['pet_id' => $petId]);
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
                    'SELECT * FROM %i WHERE code LIKE %s OR tracking_url LIKE %s',
                    $table,
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
                'SELECT COUNT(*) as total, status, DATE(created_at) as date FROM %i WHERE created_at BETWEEN %s AND %s GROUP BY status, DATE(created_at) ORDER BY date DESC',
                $table,
                $start,
                $end
            ),
            ARRAY_A
        );
    }
}