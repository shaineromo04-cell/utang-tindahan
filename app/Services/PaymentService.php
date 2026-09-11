<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Utang;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Record a payment from a customer.
     *
     * If $utang is given, the amount is applied to that utang first;
     * any leftover automatically spills over to the customer's other
     * open utangs, oldest first (FIFO).
     *
     * If $utang is null, the whole amount is applied FIFO across all
     * of the customer's open utangs — this is the "bayad sa lahat ng
     * utang" case.
     *
     * One or more Payment ledger rows are created — one per utang the
     * amount touched — so the ledger always shows exactly which utang
     * each peso went to.
     *
     * @return Payment[] the ledger rows created for this transaction
     */
    public function recordPayment(
        Customer $customer,
        float $amount,
        ?Utang $utang = null,
        string $method = 'cash',
        ?string $notes = null,
    ): array {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }

        return DB::transaction(function () use ($customer, $amount, $utang, $method, $notes) {
            $remaining = $amount;
            $ledgerRows = [];

            // Build the ordered list of utangs to apply this payment to:
            // the specified one first (if any), then the rest oldest-first.
            $queue = $utang
                ? collect([$utang])->merge(
                    $customer->openUtangs()->reject(fn ($u) => $u->id === $utang->id)
                )
                : $customer->openUtangs();

            foreach ($queue as $target) {
                if ($remaining <= 0) {
                    break;
                }

                $target->refresh();
                $applied = min($remaining, $target->balance);

                if ($applied <= 0) {
                    continue;
                }

                $target->amount_paid += $applied;
                $target->refreshStatus();

                $ledgerRows[] = Payment::create([
                    'customer_id' => $customer->id,
                    'utang_id' => $target->id,
                    'amount' => $applied,
                    'payment_method' => $method,
                    'notes' => $notes,
                ]);

                $remaining -= $applied;
            }

            // Any leftover means the customer overpaid beyond all open
            // utangs — record it unattached to a specific utang so the
            // money isn't lost from the ledger (shows as store credit).
            if ($remaining > 0) {
                $ledgerRows[] = Payment::create([
                    'customer_id' => $customer->id,
                    'utang_id' => null,
                    'amount' => $remaining,
                    'payment_method' => $method,
                    'notes' => trim(($notes ?? '') . ' (overpayment / advance credit)'),
                ]);
            }

            return $ledgerRows;
        });
    }
}