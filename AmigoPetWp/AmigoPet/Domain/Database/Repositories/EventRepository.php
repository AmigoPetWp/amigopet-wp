<?php
namespace AmigoPetWp\Domain\Database;

use AmigoPetWp\Domain\Entities\Event;

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
            'date' => $event->getDate()->format('Y-m-d H:i:s'),
            'location' => $event->getLocation(),
            'max_participants' => $event->getMaxParticipants(),
            'current_participants' => $event->getCurrentParticipants(),
            'status' => $event->getStatus(),
            'created_at' => $event->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $event->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        if ($event->getId()) {
            $this->wpdb->update(
                $this->table,
                $data,
                ['id' => $event->getId()],
                ['%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s'],
                ['%d']
            );
            return $event->getId();
        } else {
            $this->wpdb->insert(
                $this->table,
                $data,
                ['%d', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s']
            );
            $id = $this->wpdb->insert_id;
            $event->setId($id);
            return $id;
        }
    }

    public function findById(int $id): ?Event {
        $row = $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM {$this->table} WHERE id = %d", $id)
        );

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findByOrganization(int $organizationId, ?string $status = null): array {
        $sql = "SELECT * FROM {$this->table} WHERE organization_id = %d";
        $params = [$organizationId];

        if ($status) {
            $sql .= " AND status = %s";
            $params[] = $status;
        }

        $sql .= " ORDER BY date ASC";

        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $params)
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function findUpcoming(): array {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = %s 
                AND date > %s 
                ORDER BY date ASC";

        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare(
                $sql,
                [Event::STATUS_SCHEDULED, current_time('mysql')]
            )
        );

        return array_map([$this, 'hydrate'], $rows);
    }

    public function getReport(): array {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as ongoing,
                SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = %s THEN 1 ELSE 0 END) as cancelled,
                SUM(current_participants) as total_participants
                FROM {$this->table}";

        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                $sql,
                [
                    Event::STATUS_SCHEDULED,
                    Event::STATUS_ONGOING,
                    Event::STATUS_COMPLETED,
                    Event::STATUS_CANCELLED
                ]
            ),
            ARRAY_A
        );
    }

    private function hydrate($row): Event {
        $event = new Event(
            (int) $row->organization_id,
            $row->title,
            $row->description,
            new \DateTimeImmutable($row->date),
            $row->location,
            $row->max_participants ? (int) $row->max_participants : null
        );

        $reflection = new \ReflectionClass($event);

        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($event, (int) $row->id);

        $statusProperty = $reflection->getProperty('status');
        $statusProperty->setAccessible(true);
        $statusProperty->setValue($event, $row->status);

        $currentParticipantsProperty = $reflection->getProperty('currentParticipants');
        $currentParticipantsProperty->setAccessible(true);
        $currentParticipantsProperty->setValue($event, (int) $row->current_participants);

        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($event, new \DateTimeImmutable($row->created_at));

        $updatedAtProperty = $reflection->getProperty('updatedAt');
        $updatedAtProperty->setAccessible(true);
        $updatedAtProperty->setValue($event, new \DateTimeImmutable($row->updated_at));

        return $event;
    }
}
