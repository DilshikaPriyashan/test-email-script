<x-guest-layout>

    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-8" x-data="redirectToWorkspace()" x-init="init()">
        <!-- Welcome Message -->
        <h2 class="text-3xl font-bold text-gray-800 mb-4 text-center">Welcome to <span class="text-indigo-600">{{ $team->name }}</span>!</h2>
        <p class="text-gray-600 text-center text-lg mb-6">You have successfully joined the team!</p>
    
        <!-- Countdown Section -->
        <div class="flex flex-col items-center justify-center space-y-4">
            <!-- Circular Countdown UI -->
            <div class="relative w-24 h-24">
                <svg class="absolute top-0 left-0 w-full h-full" viewBox="0 0 36 36">
                    <path class="text-gray-300" d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none" stroke-width="3.8" />
                    <path class="text-indigo-600" x-bind:stroke-dasharray="(countdown / 10) * 100 + ', 100'" stroke-linecap="round"
                        d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                        fill="none" stroke-width="3.8" stroke-dasharray="100, 100" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-xl font-bold text-gray-700" x-text="countdown"></span>
                </div>
            </div>
    
            <!-- Redirect Text -->
            <p class="text-lg font-medium text-gray-700 text-center">
                Redirecting you to your workspace in <span x-text="countdown"></span> seconds...
            </p>
        </div>
    
        <!-- Manual Redirect Link -->
        <p class="mt-4 text-sm text-center text-indigo-600">
            Or <a href="{{$redirect}}" class="underline">click here</a> if you're not redirected automatically.
        </p>
    
        <!-- Alpine.js Script -->
        <script>
            function redirectToWorkspace() {
                return {
                    countdown: 10,
                    init() {
                        let timer = setInterval(() => {
                            if (this.countdown > 0) {
                                this.countdown--;
                            } else {
                                clearInterval(timer);
                                window.location.href = "{{$redirect}}"; // Change to your workspace URL
                            }
                        }, 1000);
                    }
                }
            }
        </script>
    </div>
    
</x-guest-layout>
