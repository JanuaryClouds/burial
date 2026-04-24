<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'source_id',
        'source_type',
        'notifiable_id',
        'notifiable_type',
        'payload',
        'read_at',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->morphTo();
    }

    public static function defaultPayload($classname)
    {
        if (class_exists($classname)) {
            $payload = null;
            switch ($classname) {
                case Interview::class:
                    $payload = json_encode([
                        'subject' => 'Please attend the interview we have set',
                        'body' => 'In order for us to recommend you a service or assistance, you must attend an interview for us to give you an assessment. Please check your schedule from your phone\'s messages.',
                    ]);
                    break;
                case Referral::class:
                    $payload = json_encode([
                        'subject' => 'You have been given a Referral to another department',
                        'body' => 'Unfortunately, our services do not cover your application. You have been given a referral to another department for further assistance.',
                    ]);
                    break;
                case ClientAssessment::class:
                    $payload = json_encode([
                        'subject' => 'Assessment completed',
                        'body' => 'You have finished your assessment. This will determine the type of funeral assistance you or your beneficiary will receive.',
                    ]);
                    break;
                case ClientRecommendation::class:
                    $payload = json_encode([
                        'subject' => 'You have been given a recommended service',
                        'body' => 'After completing your assessment, you have been recommended a service.',
                    ]);
                    break;
                case BurialAssistance::class:
                    $payload = json_encode([
                        'subject' => 'Burial Assistance has been set',
                        'body' => 'You have been set to receive burial assistance for your beneficiary. Please be patient as this will take around 60 minutes.',
                    ]);
                    break;
                case FuneralAssistance::class:
                    $payload = json_encode([
                        'subject' => 'Libreng Libing has been recommended',
                        'body' => 'Your beneficiary will be given libreng libing at the Taguig Public Cemetery. Processing this assistance will take around 20 minutes.',
                    ]);
                    break;
                default:
                    $payload = json_encode([
                        'subject' => 'Notification',
                        'body' => 'You have been given a notification. This is likely a test.',
                    ]);

            }

            return $payload;
        }
    }
}
