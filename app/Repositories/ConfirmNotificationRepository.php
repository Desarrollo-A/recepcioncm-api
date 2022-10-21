<?php

namespace App\Repositories;

use App\Contracts\Repositories\ConfirmNotificationRepositoryInterface;
use App\Core\BaseRepository;
use App\Models\ConfirmNotification;
use App\Models\Enums\Lookups\StatusRequestLookup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ConfirmNotificationRepository extends BaseRepository implements ConfirmNotificationRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|ConfirmNotification
     */
    protected $entity;

    public function __construct(ConfirmNotification $confirmNotification)
    {
        $this->entity = $confirmNotification;
    }

    public function updatePastRecords()
    {
        $this->entity
            ->whereIn('request_notification_id', function ($query) {
                return $query
                    ->select('request_notification_id')
                    ->from('confirm_notifications')
                    ->whereIn('request_notification_id', function ($query) {
                        return $query
                            ->select('id')
                            ->from('request_notifications')
                            ->whereIn('request_id', function ($query) {
                                return $query
                                    ->select('requests.id')
                                    ->from('requests')
                                    ->join('lookups', 'lookups.id', '=', 'requests.status_id')
                                    ->whereDate('start_date', '<', now())
                                    ->where('lookups.code', StatusRequestLookup::code(StatusRequestLookup::APPROVED));
                            });
                    });
            })
            ->update(['is_answered' => true]);
    }
}