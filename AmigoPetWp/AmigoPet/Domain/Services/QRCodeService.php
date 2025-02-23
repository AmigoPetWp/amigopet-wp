<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\QRCodeRepository;
use AmigoPetWp\Domain\Entities\QRCode;

class QRCodeService {
    private $repository;

    public function __construct(QRCodeRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * Cria um novo QR Code
     */
    public function createQRCode(
        int $petId,
        string $code,
        string $trackingUrl
    ): int {
        $qrcode = new QRCode(
            $petId,
            $code,
            $trackingUrl
        );

        return $this->repository->save($qrcode);
    }

    /**
     * Atualiza um QR Code existente
     */
    public function updateQRCode(
        int $id,
        string $code,
        string $trackingUrl
    ): bool {
        $qrcode = $this->repository->findById($id);
        if (!$qrcode) {
            return false;
        }

        $qrcode->setCode($code);
        $qrcode->setTrackingUrl($trackingUrl);

        return $this->repository->save($qrcode) > 0;
    }

    /**
     * Busca um QR Code por ID
     */
    public function findById(int $id): ?QRCode {
        return $this->repository->findById($id);
    }

    /**
     * Busca QR Code por código
     */
    public function findByCode(string $code): ?QRCode {
        return $this->repository->findByCode($code);
    }

    /**
     * Lista QR Codes por critérios
     */
    public function findQRCodes(array $criteria = []): array {
        return $this->repository->findAll($criteria);
    }

    /**
     * Remove um QR Code
     */
    public function deleteQRCode(int $id): bool {
        return $this->repository->delete($id);
    }

    /**
     * Gera um novo código único
     */
    public function generateUniqueCode(): string {
        do {
            $code = substr(uniqid(), -8);
        } while ($this->findByCode($code) !== null);

        return $code;
    }

    /**
     * Gera URL de rastreamento
     */
    public function generateTrackingUrl(string $code): string {
        return home_url("/pet/track/{$code}");
    }
}
