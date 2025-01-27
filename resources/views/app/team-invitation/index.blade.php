<x-guest-layout>
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        
        <form class="space-y-6"  method="post" action="{{route('invitation.accept')}}">
            @csrf
            @if (empty($user->email_verified_at))
            <h2 class="text-xl font-bold text-gray-800 mb-4">Wellcome to the {{ $team->name }}</h2>
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700">Your's Name</label>
                <input type="text" id="full_name" name="full_name" value="{{old('full_name')}}" placeholder="John"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
            </div>   
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Your's Email</label>
                <input type="text" id="email" name="email" value="{{$user->email}}" disabled
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>    
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" value="{{old('password')}}" placeholder="••••••••"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('password')" />
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="••••••••"
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                </div>
            <input type="hidden" name="is_new_user" value="yes">
            @else
            <h2 class="text-xl font-bold text-gray-800 mb-4">Hi.. {{ $user->name }}</h2>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Wellcome to the {{ $team->name }}</h2>
            @endif
            <input type="hidden" name="code" value="{{$code}}">

            <div>
                <button type="submit"
                    class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Accept Invitation
                </button>
            </div>
        </form>
    </div>
    
</x-guest-layout>
