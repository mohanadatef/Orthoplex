<?php
namespace Domain\DTOs;

final class UserDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $org_id,
        public readonly string $name,
        public readonly string $email,
    ) {}
    public static function fromArray(array $a): self
    {
        return new self($a['id'] ?? null, $a['org_id'], $a['name'], $a['email']);
    }
    public function toArray(): array
    {
        return ['id'=>$this->id,'org_id'=>$this->org_id,'name'=>$this->name,'email'=>$this->email];
    }
}
