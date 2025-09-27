<?php
namespace App\Services\Provisioning;

use Illuminate\Support\Facades\DB;

final class ProvisioningSaga
{
    private array $compensations = [];

    public function step(callable $do, callable $compensate): void
    {
        $this->compensations[] = $compensate;
        $do();
    }

    public function run(callable $fn): void
    {
        DB::beginTransaction();
        try {
            $fn($this);
            DB::commit();
        } catch (\Throwable $e) {
            // run compensations in reverse
            for ($i = count($this->compensations) - 1; $i >= 0; $i--) {
                try { ($this->compensations[$i])(); } catch (\Throwable $ignored) {}
            }
            DB::rollBack();
            throw $e;
        }
    }
}
