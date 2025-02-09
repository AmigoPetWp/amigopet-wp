<?php
namespace AmigoPet\Domain\Database;

use AmigoPet\Domain\Entities\Event;

class EventRepository {
    private $wpdb;
    private $table;
    
    public function __construct(\wpdb $wpdb) {
        $this->wpdb = $wpdb;
        $this->table = $wpdb->prefix . 'amigopet_events';
    }
    
    public function save(Event $event): int {
        $data = [
            'organization_id' => $event->getOrganizationId(),
            'title' => $event->getTitle(),
            'description' => $event->getDescription(),
            'location' => $event->getLocation(),
            'start_date' => $event->getStartDate()->format('Y-m-d H:i:s'),
            'end_date' => $event->getEndDate()->format('Y-m-d H:i:s'),
            'status' => $event->getStatus(),
            'max_participants' => $event->getMaxParticipants(),
            'current_participants' => $event->getCurrentParticipants()
        ];

        if ($event->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $event->getId()]
            );
            return $event->getId();
        }

        $this->wpdb->insert($this->table, $data);
        return $this->wpdb->insert_id;
    }

    public function findById(int $id): ?Event {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE id = %d",
                $id
            ),
            ARRAY_A
        );

        return $row ? $this->hydrate($row) : null;
    }

    public function findByOrganization(int $organizationId): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} WHERE organization_id = %d ORDER BY start_date",
                $organizationId
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findUpcoming(): array {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                AND start_date > %s 
                ORDER BY start_date",
                current_time('mysql')
            ),
            ARRAY_A
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    private function hydrate(array $row): Event {
        $event = new Event(
            (int) $row['organization_id'],
            $row['title'],
            $row['description'],
            $row['location'],
            new \DateTimeImmutable($row['start_date']),
            new \DateTimeImmutable($row['end_date']),
            (int) $row['max_participants']
        );

        $reflection = new \ReflectionClass($event);
        
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($event, (int) $row['id']);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($event, $row['status']);

        $currentParticipantsProperty = $reflection->getProperty('currentParticipants');
        $currentParticipantsProperty->setAccessible(true);
        $currentParticipantsProperty->setValue($event, (int) $row['current_participants']);

        return $event;
    }
}
