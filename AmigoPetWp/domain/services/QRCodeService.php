<?php
namespace AmigoPet\Domain\Services;

use AmigoPet\Domain\Database\QRCodeRepository;
use AmigoPet\Domain\Entities\QRCode;

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
    ): void {
        $qrcode = $this->repository->findById($id);
        if (!$qrcode) {
            throw new \InvalidArgumentException("QR Code não encontrado");
        }

        $qrcode = new QRCode(
            $qrcode->getPetId(),
            $code,
            $trackingUrl
        );

        $this->repository->save($qrcode);
    }

    /**
     * Marca um QR Code como inativo
     */
    public function deactivate(int $qrcodeId): void {
        $qrcode = $this->repository->findById($qrcodeId);
        if (!$qrcode) {
            throw new \InvalidArgumentException("QR Code não encontrado");
        }

        $qrcode->deactivate();
        $this->repository->save($qrcode);
    }

    /**
     * Busca um QR Code por ID
     */
    public function findById(int $id): ?QRCode {
        return $this->repository->findById($id);
    }

    /**
     * Busca um QR Code por pet
     */
    public function findByPet(int $petId): ?QRCode {
        return $this->repository->findByPet($petId);
    }

    /**
     * Busca um QR Code por código
     */
    public function findByCode(string $code): ?QRCode {
        return $this->repository->findByCode($code);
    }
}
