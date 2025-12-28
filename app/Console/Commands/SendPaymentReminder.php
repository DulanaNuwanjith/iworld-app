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
        $today = Carbon::today();

        // Reminder days
        $reminderDays = [7, 3];

        foreach ($reminderDays as $daysBefore) {

            $targetDate = $today->copy()->addDays($daysBefore);

            $payments = FinancePayment::whereDate('expected_date', $targetDate)->get();

            if ($payments->isEmpty()) {
                $this->info("No payments found due in {$daysBefore} days.");
                continue;
            }

            foreach ($payments as $payment) {

                $order = $payment->financeOrder;

                // Safety check
                if (!$order || !$order->phone_1) {
                    $this->warn("Skipping payment ID {$payment->id} — missing order or phone.");
                    continue;
                }

                // Clean phone number
                $phone = preg_replace('/\D/', '', $order->phone_1);

                if (str_starts_with($phone, '0')) {
                    $phone = '94' . substr($phone, 1);
                }

                // SMS message
                $message = "Dear {$order->buyer_name},\n\n"
                    . "This is a reminder that your installment #{$payment->installment_number} "
                    . "amounting to Rs. {$payment->amount} is due on "
                    . "{$payment->expected_date->format('Y-m-d')} "
                    . "(in {$daysBefore} days).\n\n"
                    . "Kindly make the payment on or before the due date to avoid overdue charges.\n\n"
                    . "For further assistance:\n"
                    . "Tel: 076 411 28 49 | 077 20 87 649\n\n"
                    . "Thank you.\n"
                    . "Iworld Finance";

                // Send SMS
                $response = SmsHelper::sendSms($phone, $message);

                if (isset($response['status']) && $response['status'] === 'success') {
                    $this->info("✅ {$daysBefore}-day reminder sent to {$order->buyer_name} ({$phone})");
                } else {
                    $this->error("❌ Failed to send {$daysBefore}-day reminder to {$order->buyer_name}");
                    $this->line("Response: " . json_encode($response));
                }
            }
        }
    }

}