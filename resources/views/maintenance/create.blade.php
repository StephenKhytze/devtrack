@extends('layouts.app')

@section('title', 'Log Maintenance')

@section('toolbar')
    <a href="{{ route('maintenance.index') }}"
       class="flex items-center gap-2 text-base text-gray-500 hover:text-green-700 transition">
        ← Back to maintenance logs
    </a>
    <span class="text-base font-medium text-gray-800">Log maintenance</span>
@endsection

@section('content')
<div class="mt-4">

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-base">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('maintenance.store') }}"
          class="bg-white border border-gray-200 rounded-xl p-6 flex flex-col gap-4">
        @csrf

        <div class="grid grid-cols-2 gap-6">

            {{-- Column 1 --}}
            <div class="flex flex-col gap-4">

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Device</label>
                    <select name="device_id"
                            id="device_id"
                            onchange="autoFillStatusBefore()"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                        <option value="">Select a device</option>
                        @foreach ($devices as $d)
                            <option value="{{ $d->id }}"
                                    data-status="{{ $d->status_id }}"
                                {{ old('device_id', isset($device) ? $device->id : '') == $d->id ? 'selected' : '' }}>
                                {{ $d->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Performed by</label>
                    <input type="text"
                        name="performed_by_name"
                        list="users-list"
                        placeholder="Type or select a user..."
                        value="{{ old('performed_by_name') }}"
                        class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                    <datalist id="users-list">
                        @foreach ($users as $user)
                            <option value="{{ $user->username }}">
                        @endforeach
                    </datalist>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Date</label>
                    <input type="date" name="date"
                           value="{{ old('date', date('Y-m-d')) }}"
                           class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">
                        Deadline
                        <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="date" name="deadline"
                        value="{{ old('deadline') }}"
                        class="text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Status before</label>
                    <select name="status_before_id"
                            id="status_before_id"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_before_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-base font-medium text-gray-700">Status after</label>
                    <select name="status_after_id"
                            class="text-base border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-green-700">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_after_id') == $status->id ? 'selected' : '' }}>
                                {{ $status->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Column 2 --}}
            <div class="flex flex-col gap-4">

                <div class="flex flex-col gap-1 flex-1">
                    <label class="text-base font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="10"
                              placeholder="Describe what was done during maintenance..."
                              class="text-base border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-700 resize-none">{{ old('description') }}</textarea>
                </div>

            </div>

        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="px-5 py-2 text-base font-medium bg-green-700 text-white rounded-md hover:bg-green-800 transition">
                Save log
            </button>
            <a href="{{ route('maintenance.index') }}"
               class="px-5 py-2 text-base font-medium border border-gray-300 text-gray-500 rounded-md hover:bg-gray-100 transition">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
    const deviceStatuses = {
        @foreach ($devices as $d)
            {{ $d->id }}: {{ $d->status_id }},
        @endforeach
    };

    function autoFillStatusBefore() {
        const deviceId = document.getElementById('device_id').value;
        const statusId = deviceStatuses[deviceId];
        if (statusId) {
            document.getElementById('status_before_id').value = statusId;
        }
    }

    // Run on page load if device is pre-selected
    autoFillStatusBefore();
</script>

@endsection
