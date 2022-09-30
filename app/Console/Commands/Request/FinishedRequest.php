<?php

namespace App\Console\Commands\Request;

use App\Models\Enums\Lookups\StatusRequestLookup;
use App\Models\Enums\TypeLookup;
use App\Models\Lookup;
use App\Models\Request;
use Illuminate\Console\Command;

class FinishedRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'request:finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambiar estatus de solicitudes Aprobadas a Terminadas';

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
            ->where('lookups.code', StatusRequestLookup::code(StatusRequestLookup::APPROVED))
            ->get(['requests.id']);

        if ($requests->count() > 0) {
            $statusId = Lookup::query()
                ->where('code', StatusRequestLookup::code(StatusRequestLookup::FINISHED))
                ->where('type', TypeLookup::STATUS_REQUEST)
                ->where('status', true)
                ->firstOrFail()
                ->id;

            Request::query()
                ->whereIn('id', array_values($requests->toArray()))
                ->update(['status_id' => $statusId]);
        }
    }
}
