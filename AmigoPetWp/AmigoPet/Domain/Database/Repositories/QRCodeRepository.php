<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\DomainEntities\QRCode;

class QRCodeRepository {
    private $wpdb;
    private $table;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_qrcodes';
    }
    
    public function save(QRCode $qrcode): int {
        $data = [
            'pet_id' => $qrcode->getPetId(),
            'code' => $qrcode->getCode(),
            'tracking_url' => $qrcode->getTrackingUrl(),
            'status' => $qrcode->getStatus()
        ];

        if ($qrcode->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $qrcode->getId()]
            );
            return $qrcode->getId();
        }

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?QRCode {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByPet(int $petId): ?QRCode {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE pet_id = %d",
                $petId
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByCode(string $code): ?QRCode {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE code = %s",
                $code
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    private function hydrate(array $row): QRCode {
        $qrcode = new QRCode(
            (int) $row['pet_id'],
            $row['code'],
            $row['tracking_url']
        );

        $reflection = new \ReflectionClass($qrcode);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($qrcode, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($qrcode, $row['status']);

        return $qrcode;
    }
}
