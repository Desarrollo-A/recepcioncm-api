<?php

namespace App\Console\Commands\Request;

use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\ProposalRequest;
use App\Models\Request;
use Illuminate\Console\Command;

class ExpiredRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pasar todas las solicitudes sin atender a Expiradas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $requests = Request::query()
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('end_date', '<', now())
            ->expired()
            ->get(['requests.id']);

        $statusId = Lookup::query()
            ->where('code', StatusRequestLookup::code(StatusRequestLookup::EXPIRED))
            ->where('type', TypeLookup::STATUS_REQUEST)
            ->where('status', true)
            ->firstOrFail()
            ->id;

        if ($requests->count() > 0) {
            Request::query()
                ->whereIn('id', array_values($requests->toArray()))
                ->update(['status_id' => $statusId]);
        }

        $proposalRequests = Request::query()
            ->join('lookups', 'lookups.id', '=', 'requests.status_id')
            ->whereDate('end_date', '<', now())
            ->where('lookups.code', StatusRequestLookup::code(StatusRequestLookup::PROPOSAL))
            ->get(['requests.id']);

        if ($proposalRequests->count() > 0) {
            ProposalRequest::query()
                ->whereIn('request_id', array_values($proposalRequests->toArray()))
                ->delete();

            Request::query()
                ->whereIn('id', array_values($requests->toArray()))
                ->update(['status_id' => $statusId]);
        }
    }
}
