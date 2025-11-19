<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $profile->user->name }}'s Profile
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Main Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start space-x-6">
                        <!-- Profile Photo -->
                        <div class="flex-shrink-0">
                            @if($profile->user->profile_photo)
                                <img src="{{ Storage::url($profile->user->profile_photo) }}" 
                                     alt="Profile photo" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                            @else
                                <img src="{{ $profile->user->profile_photo_url }}" 
                                     alt="Avatar" 
                                     class="w-32 h-32 rounded-full object-cover border-4 border-gray-300">
                            @endif
                        </div>

                        <!-- User Information -->
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $profile->user->name }}
                            </h3>
                            
                            @if($profile->current_workplace)
                                <p class="mt-2 text-gray-700 dark:text-gray-300">
                                    <i class="bi bi-briefcase"></i> {{ $profile->current_workplace }}
                                </p>
                            @endif

                            @if($profile->skills)
                                <div class="mt-3">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">Skills:</span>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach(explode(',', $profile->skills) as $skill)
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                {{ trim($skill) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- About me -->
                    @if($profile->about_me)
                        <div class="mt-6">
                            <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">
                                About
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ $profile->about_me }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Academic Training -->
            @if($profile->academicTrainings->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            <i class="bi bi-mortarboard"></i> Academic Training
                        </h3>
                        <div class="space-y-4">
                            @foreach($profile->academicTrainings as $training)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $training->degree }}
                                    </h4>
                                    <p class="text-gray-600 dark:text-gray-400">
                                        {{ $training->institution }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($training->start_date)->format('Y') }} - 
                                        {{ $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('Y') : 'Present' }}
                                    </p>
                                    @if($training->description)
                                        <p class="text-gray-700 dark:text-gray-300 mt-2">
                                            {{ $training->description }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Social Networks -->
            @if($profile->socialNetworks->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            <i class="bi bi-share"></i> Social Networks
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            @foreach($profile->socialNetworks as $network)
                                <a href="{{ $network->link }}" 
                                   target="_blank"
                                   class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white rounded">
                                    <i class="bi bi-link-45deg mr-2"></i>
                                    {{ $network->platform }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activities Performed -->
            @if($activities->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            <i class="bi bi-calendar-check"></i> Talks/Workshops Delivered
                        </h3>
                        <div class="space-y-4">
                            @foreach($activities as $activity)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $activity->name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $activity->event->name }}
                                            </p>
                                            <span class="inline-block mt-2 px-3 py-1 text-xs font-semibold rounded-full
                                                @if($activity->type == 'talk') bg-purple-100 text-purple-800
                                                @elseif($activity->type == 'workshop') bg-green-100 text-green-800
                                                @else bg-blue-100 text-blue-800
                                                @endif">
                                                {{ ucfirst($activity->type) }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            @if($activity->schedules->first())
                                                <p class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($activity->schedules->first()->date)->format('m/d/Y') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>