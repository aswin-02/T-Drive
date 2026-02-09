<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    /**
     * Create a credit or debit transaction and update user's wallet balance atomically.
     *
     * @param User $user
     * @param array $params Key-value array of transaction fields (user_id, type, source, amount, etc.)
     * @return Transaction
     * @throws Exception
     */
    public function createTransaction(User $user, array $params): Transaction
    {
        return DB::transaction(function () use ($user, $params) {
            $type = $params['type'] ?? 'credit';
            $amount = $params['amount'] ?? 0;
            if ($type === 'debit' && $user->wallet_balance < $amount) {
                throw new Exception('Insufficient wallet balance');
            }

            $newBalance = $type === 'credit'
                ? $user->wallet_balance + $amount
                : $user->wallet_balance - $amount;

            $params['user_id'] = $user->id;
            $params['balance_after'] = $newBalance;

            $transaction = Transaction::create($params);

            // Optionally: log audit, fire events, etc.

            return $transaction;
        });
    }
}
