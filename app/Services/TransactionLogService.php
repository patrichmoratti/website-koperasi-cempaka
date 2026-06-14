<?php

namespace App\Services;

use App\Models\TransactionLog;

class TransactionLogService
{
    public function log(array $data): TransactionLog
    {
        return TransactionLog::create(array_merge([
            'pengurus_id' => auth()->id(),
            'status'      => 'committed',
            'ip_address'  => request()->ip(),
        ], $data));
    }

    public function logFailure(array $data, \Throwable $e): TransactionLog
    {
        return $this->log(array_merge($data, [
            'status'      => 'rolled_back',
            'description' => $data['description'] . ' — gagal: ' . $e->getMessage(),
        ]));
    }
}