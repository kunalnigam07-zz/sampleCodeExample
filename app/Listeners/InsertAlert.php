<?php

namespace App\Listeners;

use Carbon;
use DateHelper;
use RouteHelper;
use StringHelper;
use NumberHelper;
use App\Models\Feed;
use App\Models\User;
use App\Events\AlertTriggered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InsertAlert
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Feed $feed, User $user)
    {
        $this->feed = $feed;
        $this->user = $user;
    }

    /**
     * Handle the event.
     *
     * @param  AlertTriggered  $event
     * @return void
     */
    public function handle(AlertTriggered $event)
    {
        $type = $event->type;
        $data = $event->data;

        switch ($type) {
            case 3:
                $this->feed->create([
                    'type_id' => 3, 
                    'user_id' => $data->user_id, 
                    'data' => serialize(['CREDITS' => $data->creds]),
                    'url' => '/member/credits',
                    'expires_at' => Carbon::now()->addDays(14)
                ]);
                break;
            case 4:
                $this->feed->create([
                    'type_id' => 4, 
                    'user_id' => $data->user_id, 
                    'data' => '',
                    'url' => '/member/bulk',
                    'expires_at' => Carbon::now()->addDays(14)
                ]);
                break;
            case 5:
                $this->feed->create([
                    'type_id' => 5, 
                    'user_id' => $data['user'], 
                    'data' => serialize(['CLASS' => $data['class']]),
                    'url' => '/instructor/classes',
                    'expires_at' => Carbon::now()->addDays(1)
                ]);
                break;
            case 6:
                $this->feed->create([
                    'type_id' => 6, 
                    'user_id' => $data->id, 
                    'data' => serialize(['TOTAL' => $data->members_trained]),
                    'url' => '',
                    'expires_at' => Carbon::now()->addDays(14)
                ]);
                break;
            case 7:
                $this->feed->create([
                    'type_id' => 7, 
                    'user_id' => $data->id, 
                    'data' => serialize(['TOTAL' => $data->classes_held]),
                    'url' => '',
                    'expires_at' => Carbon::now()->addDays(14)
                ]);
                break;
            case 14:
                $this->feed->create([
                    'type_id' => 14, 
                    'user_id' => $data['user'], 
                    'data' => serialize(['CLASS' => $data['class'], 'DATE' => $data['date'], 'TOTAL' => $data['total'], 'LINK' => '/instructor/invite/' . $data['cid']]),
                    'url' => '/instructor/invite/' . $data['cid'],
                    'expires_at' => Carbon::now()->addDays(7)
                ]);
                break;
            case 15:
                $this->feed->create([
                    'type_id' => 15, 
                    'user_id' => $data['user'], 
                    'data' => serialize(['TOTAL' => $data['trained'], 'MONEY' => '&pound;' . NumberHelper::money($data['earned']), 'LINK' => '/instructor/stats']),
                    'url' => '/instructor/stats',
                    'expires_at' => Carbon::now()->addDays(14)
                ]);
                break;
            case 16:
                $this->feed->create([
                    'type_id' => 16, 
                    'user_id' => $data['user'], 
                    'data' => serialize(['TOTAL' => $data['booked'], 'LINK' => '/instructor/classes']),
                    'url' => '/instructor/classes',
                    'expires_at' => Carbon::now()->addDays(7)
                ]);
                break;
            case 17:
                $this->feed->create([
                    'type_id' => 17, 
                    'user_id' => $data['user'], 
                    'data' => serialize(['TOTAL' => $data['rating'], 'CLASS' => $data['class'], 'LINK' => '/instructor/stats']),
                    'url' => '/instructor/stats',
                    'expires_at' => Carbon::now()->addDays(7)
                ]);
                break;
            case 18:
                $class_url = RouteHelper::getRoute('web.class.details', [$data['class']->id, StringHelper::slug($data['class']->title)]);
                $instructor_url = RouteHelper::getRoute('web.instructor.profile-details', [$data['instructor']->id, StringHelper::slug($data['instructor']->name . ' ' . $data['instructor']->surname)]);
                $member = $this->user->findOrFail($data['user']);

                $this->feed->create([
                    'type_id' => 18, 
                    'user_id' => $data['user'], 
                    'data' => serialize([
                        'INSTRUCTOR_LINK' => $instructor_url, 
                        'INSTRUCTOR' => $data['instructor']->name . ' ' . $data['instructor']->surname, 
                        'CLASS_LINK' => $class_url,
                        'CLASS' => $data['class']->title,
                        'DATE' => DateHelper::showDateTZ($data['class']->class_at, 'j M Y', $member->timezone),
                        'TIME' => DateHelper::showDateTZ($data['class']->class_at, 'H:i', $member->timezone)
                    ]),
                    'url' => $class_url,
                    'instructor_id' => $data['instructor']->id,
                    'class_id' => $data['class']->id,
                    'expires_at' => $data['class']->class_at
                ]);
                break;
            case 19:
                $class_url = RouteHelper::getRoute('web.class.details', [$data['class']->id, StringHelper::slug($data['class']->title)]);
                $instructor_url = RouteHelper::getRoute('web.instructor.profile-details', [$data['instructor']->id, StringHelper::slug($data['instructor']->name . ' ' . $data['instructor']->surname)]);
                $member = $this->user->findOrFail($data['user']);

                $this->feed->create([
                    'type_id' => 19, 
                    'user_id' => $data['user'], 
                    'data' => serialize([
                        'INSTRUCTOR_LINK' => $instructor_url, 
                        'INSTRUCTOR' => $data['instructor']->name . ' ' . $data['instructor']->surname, 
                        'CLASS_LINK' => $class_url,
                        'CLASS' => $data['class']->title,
                        'DATE' => DateHelper::showDateTZ($data['class']->class_at, 'j M Y', $member->timezone),
                        'TIME' => DateHelper::showDateTZ($data['class']->class_at, 'H:i', $member->timezone)
                    ]),
                    'url' => $class_url,
                    'instructor_id' => $data['instructor']->id,
                    'class_id' => $data['class']->id,
                    'expires_at' => $data['class']->class_at
                ]);
                break;
            case 21;
            case 23:
                $class_url = RouteHelper::getRoute('web.class.details', [$data['class']->id, StringHelper::slug($data['class']->title)]);
                $instructor_url = RouteHelper::getRoute('web.instructor.profile-details', [$data['instructor']->id, StringHelper::slug($data['instructor']->name . ' ' . $data['instructor']->surname)]);

                $this->feed->create([
                    'type_id' => $type, 
                    'user_id' => 0, 
                    'data' => serialize([
                        'INSTRUCTOR_LINK' => $instructor_url, 
                        'INSTRUCTOR' => $data['instructor']->name . ' ' . $data['instructor']->surname, 
                        'CLASS_LINK' => $class_url,
                        'CLASS' => $data['class']->title
                    ]),
                    'url' => $class_url,
                    'instructor_id' => $data['instructor']->id,
                    'class_id' => $data['class']->id,
                    'expires_at' => $data['class']->class_at
                ]);
                break;
            case 20:
            case 22:
                $class_url = RouteHelper::getRoute('web.class.details', [$data['class']->id, StringHelper::slug($data['class']->title)]);
                $instructor_url = RouteHelper::getRoute('web.instructor.profile-details', [$data['instructor']->id, StringHelper::slug($data['instructor']->name . ' ' . $data['instructor']->surname)]);

                $this->feed->create([
                    'type_id' => $type, 
                    'user_id' => $data['user'], 
                    'data' => serialize([
                        'INSTRUCTOR_LINK' => $instructor_url, 
                        'INSTRUCTOR' => $data['instructor']->name . ' ' . $data['instructor']->surname, 
                        'CLASS_LINK' => $class_url,
                        'CLASS' => $data['class']->title
                    ]),
                    'url' => $class_url,
                    'instructor_id' => $data['instructor']->id,
                    'class_id' => $data['class']->id,
                    'expires_at' => $data['class']->class_at
                ]);
                break;
        }
    }
}
