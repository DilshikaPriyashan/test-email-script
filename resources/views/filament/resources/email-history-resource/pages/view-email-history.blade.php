<x-filament-panels::page>
<div class="max-w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <a href="#">
        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{$this->emailHistory->emailTemplate->name}}</h5>
    </a>
    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{$this->emailHistory->created_at->since()}}</p>
</div>

<div class="max-w-full p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
    <a href="#">
        <h6 class="mb-2 text-lg font-bold tracking-tight text-gray-900 dark:text-white">Sending Attempts History</h6>
    </a>
    <div>
        @foreach ($this->emailHistory->getHistory() as $action )
        {{$action->status}}
        @endforeach
    </div>
</div>

</x-filament-panels::page>
