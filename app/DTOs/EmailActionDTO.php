<?php

namespace App\DTOs;

use DateTime;

class EmailActionDTO
{
    public function __construct(
        public DateTime $action_at,
        public string $how_trigger,
        public string $triggered_by,
        public string $status="processing",
        public ?string $fail_reason=null,
    ) {}

    function toArray(): array
    {
        return [
            'action_at' => $this->action_at,
            'how_trigger' => $this->how_trigger,
            'triggered_by' => $this->triggered_by,
            'status' => $this->status,
            'fail_reason' => $this->fail_reason,
        ];
    }
}
