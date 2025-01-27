<x-guest-layout>
        <div class="p-6 bg-gray-100">
            <div class="mb-4 text-center">
              <h1 class="text-lg font-bold text-gray-700">Available Teams</h1>
            </div>
          
            <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
              <table class="w-full text-sm text-left text-gray-600">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-4 py-2 font-medium text-gray-700 border-b">Name</th>
                    <th class="px-4 py-2 font-medium text-gray-700 border-b"></th>
                  </tr>
                </thead>
                <tbody>
                @foreach ($teams as $team)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-blue-600 border-b"><a href="{{route('filament.app.pages.dashboard', ["tenant" => $team->slug])}}">{{$team->name}}</a></td>
                    <td class="px-4 py-2 text-gray-600 border-b">{{$team->slug}}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
</x-guest-layout>
