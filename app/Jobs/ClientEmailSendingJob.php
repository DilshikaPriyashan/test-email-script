<?php

namespace App\Jobs;

use App\DTOs\EmailActionDTO;
use App\Exceptions\CustomAPIException;
use App\Exceptions\CustomException;
use App\Mail\ClientCustomEmail;
use App\Models\EmailHistory;
use App\Models\EmailTemplate;
use App\Models\Team;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Mail;

class ClientEmailSendingJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected EmailHistory $emailHistory,
        protected EmailTemplate $emailTemplate,
        protected Team $team,
        protected readonly array $attributes = [],
        protected readonly array $to = [],
        protected readonly array $cc = [],
        protected readonly array $bcc = [],
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->emailHistory->status="processing";
        $this->emailHistory->job_id=$this->job->uuid();
        $this->emailHistory->save();
        $action=new EmailActionDTO(
            action_at:now(),
            how_trigger:"API",
            triggered_by:"API"
           );
        try {
            $availableAttr = [];
            foreach ($this->emailTemplate->attributes as $attribute) {
                if (array_key_exists($attribute['key'], $this->attributes)) {
                    $availableAttr['{'.$attribute['key'].'}'] = $this->attributes[$attribute['key']];
                } else {
                    $availableAttr['{'.$attribute['key'].'}'] = $attribute['default_value'];
                }
            }
            $htmlContent = $this->emailTemplate->content;
            $htmlContent = str_replace(array_keys($availableAttr), array_values($availableAttr), $htmlContent);
    
            $subject = $this->emailTemplate->subject;
            $subject = str_replace(array_keys($availableAttr), array_values($availableAttr), $subject);
            if (! array_key_exists('custom_mailer_'.$this->team->id, config('mail.mailers'))) {
                throw new CustomException('SMTP credentials not found', 500);
            }
    
            if (empty($this->team->teamSettings)) {
                throw new CustomException('SMTP credentials not found', 500);
            }
    
            $fromAddress = $this->team->teamSettings->from_email;
            $fromName = $this->team->teamSettings->from_name;
    
            if (empty($fromAddress)) {
                throw new CustomException('Sender email not found', 500);
            }
            $from = new Address(address: $fromAddress, name: $fromName);
            Mail::mailer('custom_mailer_'.$this->team->id)
                ->to($this->to)
                ->cc($this->cc)
                ->bcc($this->bcc)
                ->send(new ClientCustomEmail($htmlContent, $subject, $from));

            $this->emailHistory->status="send";
            $action->status="send";
            $this->emailHistory->addAction($action);
            $this->emailHistory->save();
        } catch (CustomException $ex) {
            $this->emailHistory->status="failed";
            $action->status="failed";
            $action->fail_reason=$ex->getMessage();
            $this->emailHistory->addAction($action);
            $this->emailHistory->save();
            throw $ex;
        }catch (Exception $th) {
            $this->emailHistory->status="failed";
            $action->status="failed";
            $action->fail_reason="internal server error";
            $this->emailHistory->addAction($action);
            $this->emailHistory->save();
            throw $th;
        }

    }
}
