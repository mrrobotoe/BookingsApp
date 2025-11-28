<x-app-layout>
    <a href="{{ route('bookings') }}" class="text-xs text-blue-500 hover:underline hover:underline-offset-2">&larr; Back to Home</a>
    <div class="space-y-12">
        <div>
            <h2 class="text-xl font-medium mt-4">
                {{ !$appointment->isCancelled() ? "Your booking is confirmed!" : "Your booking has been cancelled" }}
            </h2>
            <div class="flex mt-6 space-x-3 bg-slate-100 rounded-lg p-4">
                <img src="{{ $appointment->employee->profile_photo_url }}" alt="" class="rounded-lg size-14 bg-slate-100">
                <div class="w-full">
                    <div class="flex justify-between">
                        <div class="font-semibold">
                            {{ $appointment->service->title }} ({{ $appointment->service->duration }} minutes)
                        </div>
                        <div class="text-sm">
                            {{ $appointment->service->price }}
                        </div>
                    </div>
                    <div class="text-sm">
                        {{ $appointment->employee->name }}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <h2 class="text-xl font-medium mt-4">When</h2>
            <div class="mt-6 bg-slate-100 rounded-lg p-4">
                {{ $appointment->starts_at->format('F d Y \a\t H:i') }}
            </div>
        </div>

        @if (!$appointment->isCancelled())
        <form
            method="post"
            action="{{ route('appointments.destroy', $appointment) }}"
            x-data
            x-on:submit.prevent="
                if (confirm('Are you sure you want to cancel this booking?')) {
                    $el.submit();
                }
            "
        >
            @csrf
            @method('DELETE')
            <button type="submit" class="text-blue-500 hover:underline hover:underline-offset-4">
                Cancel booking
            </button>
        </form>
        @endif
    </div>
</x-app-layout>
