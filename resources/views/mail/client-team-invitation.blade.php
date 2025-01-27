<x-mail::message>
# Hi {{$user->name??$user->email}}!

You have new team requesst for the workspace : **{{$team->name}}**
plase click accept invitaion to the proceed this invitaion 
<x-mail::button url="{{route('invitation.index',[$token])}}">
Accept Invition
</x-mail::button>

Thanks!

Team {{config('app.name')}}
</x-mail::message>