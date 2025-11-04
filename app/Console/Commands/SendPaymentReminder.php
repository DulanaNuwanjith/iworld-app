<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FinancePayment;
use Carbon\Carbon;
use App\Helpers\SmsHelper;

class SendPaymentReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SMS reminders 3 days before installment due date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get today's date and target date (3 days before due)
        $today = Carbon::today();
        $targetDate = $today->addDays(3);

        // Fetch payments due in 3 days
        $payments = FinancePayment::whereDate('expected_date', $targetDate)->get();

        if ($payments->isEmpty()) {
            $this->info("No payments found due in 3 days.");
            return;
        }

        foreach ($payments as $payment) {
            $order = $payment->financeOrder;

            // Safety check
            if (!$order || !$order->phone_1) {
                $this->warn("Skipping payment ID {$payment->id} — missing related order or phone number.");
                continue;
            }

            // Clean and format the phone number before sending
            $phone = preg_replace('/\D/', '', $order->phone_1); // remove non-digits

            if (str_starts_with($phone, '0')) {
                $phone = '94' . substr($phone, 1);
            }

            // Format message
            $message = "Dear {$order->buyer_name}, your installment #{$payment->installment_number} "
                     . "of Rs. {$payment->amount} is due on {$payment->expected_date->format('Y-m-d')}. "
                     . "Please make the payment to avoid overdue charges. - Iworld Finance";

            // Send SMS using Notify.lk helper
            $response = SmsHelper::sendSms($phone, $message);

            // Log output
            if (isset($response['status']) && $response['status'] === 'success') {
                $this->info("✅ SMS sent successfully to {$order->buyer_name} ({$phone})");
            } else {
                $this->error("❌ Failed to send SMS to {$order->buyer_name} ({$phone})");
                $this->line("Response: " . json_encode($response));
            }
        }
    }
}