<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret  = config('services.stripe.webhook_secret', env('STRIPE_WEBHOOK_SECRET'));
        $payload = $request->getContent();
        $sig     = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $secret);
        } catch (\Throwable $e) {
            Log::warning('stripe webhook signature error', ['msg' => $e->getMessage()]);
            return response('invalid signature', 400);
        }

        $type = $event->type ?? 'unknown';
        Log::info('stripe webhook received', ['type' => $type]);

        switch ($type) {
            case 'checkout.session.completed': {
                $session = $event->data->object; // \Stripe\Checkout\Session
                if (($session->payment_status ?? null) === 'paid') {
                    $this->markSoldFromSession($session);
                }
                break;
            }

            case 'checkout.session.async_payment_succeeded': {
                $this->markSoldFromSession($event->data->object);
                break;
            }

            case 'payment_intent.succeeded': {
                $pi = $event->data->object; // \Stripe\PaymentIntent
                $itemId = $pi->metadata->item_id ?? null;
                if ($itemId) {
                    $this->markSold((int)$itemId);
                }
                break;
            }

            case 'charge.succeeded': {
                // 一部の環境/triggerではこれだけが来ることがある
                $charge = $event->data->object; // \Stripe\Charge
                $itemId = $charge->metadata->item_id ?? null;

                if (!$itemId && !empty($charge->payment_intent)) {
                    // PI から metadata を引き直す
                    Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));
                    $pi = PaymentIntent::retrieve($charge->payment_intent);
                    $itemId = $pi->metadata->item_id ?? null;
                }
                if ($itemId) {
                    $this->markSold((int)$itemId);
                }
                break;
            }

            default:
                // 未対応タイプは何もせず 200 で再送停止
                break;
        }

        return response('ok', 200);
    }

    private function markSoldFromSession($session): void
    {
        $itemId = $session->metadata->item_id ?? null;

        if (!$itemId && !empty($session->payment_intent)) {
            Stripe::setApiKey(config('services.stripe.secret', env('STRIPE_SECRET')));
            $pi = PaymentIntent::retrieve($session->payment_intent);
            $itemId = $pi->metadata->item_id ?? null;
        }
        if ($itemId) {
            $this->markSold((int)$itemId);
        }
    }

    private function markSold(int $itemId): void
    {
        DB::transaction(function () use ($itemId) {
            /** @var Item|null $item */
            $item = Item::lockForUpdate()->find($itemId);
            if (!$item) {
                Log::warning('stripe markSold: item not found', ['item_id' => $itemId]);
                return;
            }

            // 既に売却済みなら何もしない（attributes を見る）
            if ((int)($item->status ?? 1) === 0) {
                return;
            }

            // 要件に合わせて更新（例：status=0 を売却済みとする）
            $item->status = 0;
            // $item->sold = 1; // sold カラムがある場合は併用
            $item->save();

            Log::info('item marked sold', ['item_id' => $itemId]);
        });
    }
}
