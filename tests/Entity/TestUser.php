<?php

namespace CampaignBundle\Tests\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'test_users', options: ['comment' => '测试用户表'])]
class TestUser implements UserInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '用户ID'])]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    #[ORM\Column(type: Types::STRING, length: 180, unique: true, options: ['comment' => '用户名'])]
    private ?string $username = null;

    /** @var array<string> $roles */
    #[Assert\Type(type: 'array')]
    #[ORM\Column(type: Types::JSON, options: ['comment' => '用户角色'])]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getUserIdentifier(): string
    {
        $identifier = (string) $this->username;
        if ('' === $identifier) {
            return 'unknown_user_' . uniqid();
        }

        return $identifier;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function __toString(): string
    {
        return $this->username ?? '';
    }
}
