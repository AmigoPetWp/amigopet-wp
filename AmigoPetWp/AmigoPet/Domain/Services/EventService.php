<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\EventRepository;
use AmigoPetWp\Domain\Entities\Event;

class EventService {
    private $repository;

    public function __construct(EventRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo evento
     */
    public function createEvent(
        int $organizationId,
        string $title,
        string $description,
        string $location,
        \DateTimeInterface $date,
        ?int $maxParticipants = null
    ): int {
        $event = new Event(
            $organizationId,
            $title,
            $description,
            $date,
            $location,
            $maxParticipants
        );

        return $this->repository->save($event);
    }

    /**
     * Atualiza um evento existente
     */
    public function updateEvent(
        int $id,
        string $title,
        string $description,
        string $location,
        \DateTimeInterface $date,
        ?int $maxParticipants = null
    ): void {
        $event = $this->repository->findById($id);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event = new Event(
            $event->getOrganizationId(),
            $title,
            $description,
            $date,
            $location,
            $maxParticipants
        );
        $event->setId($id);

        $this->repository->save($event);
    }

    /**
     * Inicia um evento
     */
    public function startEvent(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->start();
        $this->repository->save($event);
    }

    /**
     * Completa um evento
     */
    public function completeEvent(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->complete();
        $this->repository->save($event);
    }

    /**
     * Cancela um evento
     */
    public function cancelEvent(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->cancel();
        $this->repository->save($event);
    }

    /**
     * Adiciona um participante ao evento
     */
    public function addParticipant(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->addParticipant();
        $this->repository->save($event);
    }

    /**
     * Remove um participante do evento
     */
    public function removeParticipant(int $eventId): void {
        $event = $this->repository->findById($eventId);
        if (!$event) {
            throw new \InvalidArgumentException("Evento não encontrado");
        }

        $event->removeParticipant();
        $this->repository->save($event);
    }

    /**
     * Busca um evento por ID
     */
    public function findById(int $id): ?Event {
        return $this->repository->findById($id);
    }

    /**
     * Lista eventos de uma organização
     */
    public function findByOrganization(int $organizationId, ?string $status = null): array {
        return $this->repository->findByOrganization($organizationId, $status);
    }

    /**
     * Lista eventos futuros
     */
    public function findUpcoming(): array {
        return $this->repository->findUpcoming();
    }

    /**
     * Obtém relatório de eventos
     */
    public function getReport(): array {
        return $this->repository->getReport();
    }
}
