<?php
namespace AmigoPetWp\Domain\Services;

use AmigoPetWp\Domain\Database\Repositories\PetPhotoRepository;
use AmigoPetWp\Domain\Entities\PetPhoto;

class PetPhotoService {
    private $repository;
    private $uploadDir;
    
    public function __construct(PetPhotoRepository $repository) {
        $this->repository = $repository;
        
        // Configura diretório de upload
        $wpUploadDir = wp_upload_dir();
        $this->uploadDir = $wpUploadDir['basedir'] . '/amigopet-photos/';
        
        // Cria diretório se não existir
        if (!file_exists($this->uploadDir)) {
            wp_mkdir_p($this->uploadDir);
        }
    }
    
    /**
     * Adiciona foto ao pet
     */
    public function addPhoto(int $petId, array $file, array $metadata = []): int {
        // Valida arquivo
        if (!$this->validatePhoto($file)) {
            throw new \Exception('Arquivo inválido');
        }
        
        // Prepara o upload
        $filename = $this->generateFilename($petId, $file['name']);
        $filepath = $this->uploadDir . $filename;
        
        // Move o arquivo
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            throw new \Exception('Erro ao fazer upload da foto');
        }
        
        // Cria os thumbnails
        $thumbnails = $this->createThumbnails($filepath);
        
        // Cria a entidade
        $photo = new PetPhoto(
            $petId,
            $filename,
            $metadata['title'] ?? '',
            $metadata['description'] ?? '',
            $metadata['is_profile'] ?? false
        );
        
        $photo->setThumbnails($thumbnails);
        
        // Salva no banco
        return $this->repository->save($photo);
    }
    
    /**
     * Atualiza metadados da foto
     */
    public function updatePhoto(int $id, array $metadata): bool {
        $photo = $this->repository->findById($id);
        if (!$photo) {
            return false;
        }
        
        if (isset($metadata['title'])) {
            $photo->setTitle($metadata['title']);
        }
        
        if (isset($metadata['description'])) {
            $photo->setDescription($metadata['description']);
        }
        
        if (isset($metadata['is_profile'])) {
            $photo->setIsProfile($metadata['is_profile']);
        }
        
        return $this->repository->save($photo) > 0;
    }
    
    /**
     * Remove uma foto
     */
    public function deletePhoto(int $id): bool {
        $photo = $this->repository->findById($id);
        if (!$photo) {
            return false;
        }
        
        // Remove os arquivos
        $this->deletePhotoFiles($photo);
        
        // Remove do banco
        return $this->repository->delete($id);
    }
    
    /**
     * Lista fotos de um pet
     */
    public function listPhotos(int $petId): array {
        return $this->repository->findAll(['pet_id' => $petId]);
    }
    
    /**
     * Busca foto por ID
     */
    public function getPhoto(int $id): ?PetPhoto {
        return $this->repository->findById($id);
    }
    
    /**
     * Define foto de perfil
     */
    public function setProfilePhoto(int $photoId): bool {
        $photo = $this->repository->findById($photoId);
        if (!$photo) {
            return false;
        }
        
        // Remove flag de perfil das outras fotos
        $this->repository->unsetProfilePhotos($photo->getPetId());
        
        // Define esta como perfil
        $photo->setIsProfile(true);
        return $this->repository->save($photo) > 0;
    }
    
    /**
     * Valida arquivo de foto
     */
    private function validatePhoto(array $file): bool {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Gera nome único para o arquivo
     */
    private function generateFilename(int $petId, string $originalName): string {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        return sprintf('pet_%d_%s.%s', $petId, uniqid(), $ext);
    }
    
    /**
     * Cria thumbnails da foto
     */
    private function createThumbnails(string $filepath): array {
        $thumbnails = [];
        $sizes = [
            'thumb' => [150, 150],
            'medium' => [300, 300],
            'large' => [600, 600]
        ];
        
        foreach ($sizes as $size => list($width, $height)) {
            $thumbPath = $this->generateThumbnailPath($filepath, $size);
            $this->resizeImage($filepath, $thumbPath, $width, $height);
            $thumbnails[$size] = basename($thumbPath);
        }
        
        return $thumbnails;
    }
    
    /**
     * Gera caminho para thumbnail
     */
    private function generateThumbnailPath(string $filepath, string $size): string {
        $info = pathinfo($filepath);
        return sprintf(
            '%s/%s-%s.%s',
            $info['dirname'],
            $info['filename'],
            $size,
            $info['extension']
        );
    }
    
    /**
     * Redimensiona imagem
     */
    private function resizeImage(string $source, string $target, int $width, int $height): void {
        $editor = wp_get_image_editor($source);
        if (!is_wp_error($editor)) {
            $editor->resize($width, $height, true);
            $editor->save($target);
        }
    }
    
    /**
     * Remove arquivos de foto
     */
    private function deletePhotoFiles(PetPhoto $photo): void {
        // Remove arquivo original
        $filepath = $this->uploadDir . $photo->getFilename();
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Remove thumbnails
        foreach ($photo->getThumbnails() as $thumbnail) {
            $thumbPath = $this->uploadDir . $thumbnail;
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }
        }
    }
}
