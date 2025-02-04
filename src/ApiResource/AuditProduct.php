<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\State\AuditProductProvider;

#[ApiResource(
    operations: [
        new GetCollection(),
    ],
    provider: AuditProductProvider::class
)]
class AuditProduct
{
    public ?int $id;
    public ?string $barcode;
    public ?string $place;
    public ?string $hs_code;
    public ?string $name;
    public ?string $description;
    public ?string $pos_category;
    public ?string $category;
    public ?int $quantity;
    public float|string|null $price;
    public ?string $status;
}