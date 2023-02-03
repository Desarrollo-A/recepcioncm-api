<?php

namespace App\Repositories;

use App\Contracts\Repositories\ActionRequestNotificationRepositoryInterface;
use App\Core\BaseRepository;
use App\Helpers\Utils;
use App\Models\ActionRequestNotification;
use App\Models\Enums\Lookups\StatusRoomRequestLookup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

class ActionRequestNotificationRepository extends BaseRepository implements ActionRequestNotificationRepositoryInterface
{
    /**
     * @var Builder|Model|QueryBuilder|ActionRequestNotification
     */
    protected $entity;

    public function __construct(ActionRequestNotification $actionRequestNotification)
    {
        $this->entity = $actionRequestNotification;
    }

    public function updatePastRecords()
    {
        $this->entity
            ->whereIn('request_notification_id', function ($query) {
                return $query
                    ->select('request_notification_id')
                    ->from('action_request_notifications')
                    ->whereIn('request_notification_id', function ($query) {
                        return $query
                            ->select('id')
                            ->from('request_notifications')
                            ->whereIn('request_id', function ($query) {
                                return $query
                                    ->select('requests.id')
                                    ->from('requests')
                                    ->join('lookups AS s', 's.id', '=', 'requests.status_id')
                                    ->whereDate('start_date', '<', now())
                                    ->whereIn('s.code', Utils::getStatusApprovedRequest());
                            });
                    });
            })
            ->update(['is_answered' => true]);
    }
}