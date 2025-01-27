<?php

namespace App\Http\Controllers\API;

use App\Exceptions\CustomAPIException;
use App\Http\Controllers\Controller;
use App\Jobs\ClientEmailSendingJob;
use App\Models\EmailTemplate;
use App\Models\Team;
use App\Traits\AppTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EmailSendingController extends Controller
{
    use AppTrait;

    public function sendEmail($teamSlug, $emailTemplateRef, Request $request)
    {
        try {
            $team = Team::where('slug', $teamSlug)->first();

            if (empty($team)) {
                throw new CustomAPIException('Bad Gateway', Response::HTTP_FORBIDDEN);
            }

            $emailTemplate = EmailTemplate::where('api_ref', $emailTemplateRef)
                ->where('team_id', $team->id)
                ->first();

            if (empty($emailTemplate)) {
                throw new CustomAPIException('Email Template identifier is incorrect.', Response::HTTP_BAD_REQUEST);
            }

            $to = $request->get('to', []);
            $cc = $request->get('cc', []);
            $bcc = $request->get('bcc', []);
            $attributes = $request->get('attributes', []);

            if (empty($to) && empty($cc) && empty($bcc)) {
                throw new CustomAPIException('Email should have at least one email address', Response::HTTP_BAD_REQUEST);
            }

            ClientEmailSendingJob::dispatch($emailTemplate, $team, $attributes, $to, $cc, $bcc)->onQueue('client_default');

            return $this->successApiResponse(
                'Email sending request has been received',
                Response::HTTP_OK,
                [
                    'team' => $team,
                    'email' => $emailTemplate->name,
                    'to' => $to,
                    'cc' => $cc,
                    'bcc' => $bcc,
                ]
            );
        } catch (CustomAPIException $ex) {
            return $this->errorApiResponse($ex->getMessage(), $ex->getCode(), [
                'team_slug' => $teamSlug,
                'email_template_identifier' => $emailTemplateRef,
                'to' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
            ]);
        } catch (\Throwable $th) {
            Log::debug('Email Send Fail', [$th->getMessage(), $th->getTrace()]);

            return $this->errorApiResponse();
        }
    }
}
