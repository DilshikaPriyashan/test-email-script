<?php

namespace App\Http\Controllers\API\V1;

use App\DTOs\EmailActionDTO;
use App\Enums\EmailAuthMethods;
use App\Exceptions\CustomAPIException;
use App\Http\Controllers\Controller;
use App\Jobs\ClientEmailSendingJob;
use App\Models\APIKey;
use App\Models\EmailHistory;
use App\Models\EmailTemplate;
use App\Models\Team;
use App\Traits\AppTrait;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClientEmailSendController extends Controller
{
    use AppTrait;

    public function __invoke(string $team, Request $request)
    {

        try {
            $request->validate([
                'email_template' => 'required|string',
                'attributes' => [
                    'nullable',
                    'array',
                    function ($attribute, $value, $fail) {
                        foreach ($value as $key => $val) {
                            if (! is_string($key) || ! is_string($val)) {
                                $fail("The $attribute must be an associative array with string keys and values.");
                            }
                        }
                    },
                ],
                'to' => 'nullable|array',
                'to.*' => 'email',
                'cc' => 'nullable|array',
                'cc.*' => 'email',
                'bcc' => 'nullable|array',
                'bcc.*' => 'email',
            ]);

            $team = Team::where('slug', $team)->first();

            if (empty($team)) {
                throw new CustomAPIException('Bad gateway', 502);
            }

            $emailTemplate = EmailTemplate::where('api_ref', $request->get('email_template'))->first();

            if (empty($emailTemplate)) {
                throw new CustomAPIException('Incorrect email template identifier', 403);
            }

            $apiKey=null;
            if ($emailTemplate->auth_mechanism == EmailAuthMethods::API_KEY->value) {
                $token = $request->bearerToken();
                if (empty($token)) {
                    throw new CustomAPIException('Unauthorized request', 401);
                }
                $keyId = Arr::get(explode('-', $token), 1);
                if (empty($keyId)) {
                    throw new CustomAPIException('Unauthorized request', 401);
                }
                $apiKey = APIKey::where('id', $keyId)->where('team_id', $emailTemplate->team_id)->first();
                if (empty($apiKey)) {
                    throw new CustomAPIException('Unauthorized request', 401);
                }

                if ($apiKey->key != $token) {
                    throw new CustomAPIException('Unauthorized request', 401);
                }
            }
            $data = [
                'sender' => $team->name,
                'email_template' => $emailTemplate->name,
            ];

            $attributes = $request->get('attributes', []);

            if ($emailTemplate->strict_mode === 'yes') {
                $to = $emailTemplate->to;
                $cc = $emailTemplate->cc;
                $bcc = $emailTemplate->bcc;
            } else {
                $to = $request->get('to', []);
                $cc = $request->get('cc', []);
                $bcc = $request->get('bcc', []);
            }

           $emailHistory= new EmailHistory();
           $emailHistory->email_template_id=$emailTemplate->id;
           $emailHistory->team_id=$emailTemplate->team_id;
           $emailHistory->sended_via=$apiKey;
           $emailHistory->to=$to;
           $emailHistory->cc=$cc;
           $emailHistory->bcc=$bcc;
           $emailHistory->save();

           ClientEmailSendingJob::dispatch($emailHistory, $emailTemplate, $team, $attributes, $to, $cc, $bcc)->onQueue('client_default');

            return $this->successApiResponse(
                message: 'email has been sent',
                data: $data
            );
        } catch (ValidationException $vex) {
            return $this->errorApiResponse(
                code: 422,
                errors: $vex->validator->messages(),
                message: 'given data invalid',
            );
        } catch (CustomAPIException $cex) {
            return $this->errorApiResponse(
                code: $cex->getCode(),
                message: $cex->getMessage(),
            );
        } catch (\Throwable $th) {
            Log::error("email send failed team: $team", [
                $th->getMessage(),
                $th->getTrace(),
            ]);

            return $this->errorApiResponse();
        }
    }
}
