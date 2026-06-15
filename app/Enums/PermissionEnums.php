<?php

namespace App\Enums;

enum PermissionEnums: string
{
    case User = 'user';
    case Role = 'role';
    case Permission = 'permission';
    case ADMINs = "admins";
    case USERs = "users";
    case DOCTORs = "doctors";
    case PATIENTs = "patients";
    case REPORTs = "reports";


















    public function label(): string
    {
        return trns($this->value);
    }

    public function permissions(): array
    {
        return [
            $this->value . "_create",
            $this->value . "_edit",
            $this->value . "_read",
            $this->value . "_delete",
        ];
    }
}
