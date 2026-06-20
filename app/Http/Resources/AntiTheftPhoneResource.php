<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AntiTheftPhoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serial_number' => $this->serial_number,
            
            // Phone Information
            'phone_details' => [
                'brand' => $this->phone_brand,
                'model' => $this->phone_model,
                'color' => $this->phone_color,
                'full_name' => $this->phone_info, // Uses the accessor
            ],
            
            // Status Information
            'status' => [
                'code' => $this->status,
                'display_name' => $this->status_display,
                'badge_color' => $this->status_badge_color,
                'can_generate_receipt' => $this->canGenerateReceipt(),
                'is_available_for_ownership' => $this->isAvailableForOwnership(),
                'is_stolen_or_blacklisted' => $this->isStolenOrBlacklisted(),
            ],
            
            // Current Owner Information
            'current_owner' => [
                'name' => $this->current_owner_name,
                'phone' => $this->current_owner_phone,
                'receipt_id' => $this->current_receipt_id,
                'receipt_number' => $this->whenLoaded('currentReceipt', function () {
                    return $this->currentReceipt?->receipt_number;
                }),
                'shop_name' => $this->whenLoaded('currentReceipt', function () {
                    return $this->currentReceipt?->shop?->shop_name;
                }),
            ],
            
            // API Sync Information
            'api_sync' => [
                'external_api_id' => $this->external_api_id,
                'last_sync' => $this->last_api_sync?->toISOString(),
                'has_recent_sync' => $this->hasRecentApiSync(),
                'sync_data' => $this->when(
                    $request->query('include_sync_data') === 'true', 
                    $this->api_response_data
                ),
            ],
            
            // Status History
            'status_history' => $this->when(
                $request->query('include_history') === 'true',
                $this->status_history ?? []
            ),
            
            // Notes and Additional Info
            'notes' => $this->notes,
            
            // Timestamps
            'dates' => [
                'registered_at' => $this->registered_at?->toISOString(),
                'last_verified_at' => $this->last_verified_at?->toISOString(),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
            
            // Receipt Information (when loaded)
            'receipt' => $this->whenLoaded('currentReceipt', function () {
                return $this->currentReceipt ? [
                    'id' => $this->currentReceipt->id,
                    'receipt_number' => $this->currentReceipt->receipt_number,
                    'customer_name' => $this->currentReceipt->customer_name,
                    'customer_phone' => $this->currentReceipt->customer_phone,
                    'customer_email' => $this->currentReceipt->customer_email,
                    'amount' => $this->currentReceipt->amount,
                    'payment_status' => $this->currentReceipt->payment_status,
                    'enable_antitheft' => $this->currentReceipt->enable_antitheft,
                    'receipt_type' => $this->currentReceipt->receipt_type,
                    'created_at' => $this->currentReceipt->created_at?->toISOString(),
                    'shop' => [
                        'id' => $this->currentReceipt->shop?->id,
                        'name' => $this->currentReceipt->shop?->shop_name,
                        'location' => $this->currentReceipt->shop?->shop_location,
                    ],
                ] : null;
            }),
            
            // Metadata for API consumers
            'metadata' => [
                'resource_type' => 'antitheft_phone',
                'api_version' => '1.0',
                'last_api_sync_status' => $this->hasRecentApiSync() ? 'recent' : 'stale',
                'data_freshness' => $this->last_api_sync ? 
                    $this->last_api_sync->diffForHumans() : 'never synced',
            ],
        ];
    }

    /**
     * Get additional data that should be added to the top-level resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'available_statuses' => $this->resource::getStatuses(),
            'api_documentation' => [
                'endpoints' => [
                    'update' => route('api.antitheft.phones.update', $this->serial_number),
                    'delete' => route('api.antitheft.phones.destroy', $this->serial_number),
                ],
                'query_parameters' => [
                    'include_history' => 'Set to "true" to include status history',
                    'include_sync_data' => 'Set to "true" to include API sync data',
                ],
            ],
        ];
    }
}