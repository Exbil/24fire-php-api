<?php

namespace FireAPI\Traits;

use InvalidArgumentException;

trait VMIdValidator
{
    /**
     * Validates that a VM ID is a five-digit number.
     *
     * @param string|int $vm_Id The VM ID to validate
     * @return void
     * @throws InvalidArgumentException If the VM ID is invalid
     */
    protected function validateVMId(string|int $vm_Id): void
    {
        if (!preg_match('/^\d{5}$/', (string) $vm_Id)) {
            throw new InvalidArgumentException('VMID must be a five-digit number');
        }
    }
}