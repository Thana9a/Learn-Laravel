<?php

namespace App\Events;

use App\Models\Report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * ReportGenerated Event — fired when a report job completes successfully.
 *
 * Implements ShouldBroadcast so Laravel Echo / Reverb pushes
 * the payload to the Vue frontend via WebSocket.
 *
 * Channel: private-reports.{userId}
 * Event name: report.generated
 */
class ReportGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Report $report,
    ) {}

    // ─── Broadcast ─────────────────────────────────────────────────────────────

    /**
     * The channel the event should broadcast on.
     * Private channel scoped per-user for security.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('reports.' . $this->report->user_id),
        ];
    }

    /**
     * Custom event name used by Laravel Echo on the frontend.
     */
    public function broadcastAs(): string
    {
        return 'report.generated';
    }

    /**
     * Data pushed to the client via WebSocket.
     */
    public function broadcastWith(): array
    {
        return [
            'id'           => $this->report->id,
            'title'        => $this->report->title,
            'status'       => $this->report->status,
            'generated_at' => $this->report->generated_at?->toIso8601String(),
            'result'       => $this->report->result,
        ];
    }
}
