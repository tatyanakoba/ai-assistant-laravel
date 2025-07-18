@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 space-y-4">
        {{-- Step 1: Ask for persona name --}}
        @if (!session('step') || session('step') === 1)
            <div class="bg-gray-100 p-4 rounded shadow">
                <p><strong>AI:</strong> Hi, please tell me the name of the person I should become to create your buyer persona.</p>
            </div>

            <form method="POST" action="{{ route('persona.confirm-name') }}">
                @csrf
                <input type="text" name="persona_name" class="w-full border p-2 rounded my-2" placeholder="Please become a person named..." required>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Confirm Name</button>
            </form>

        @elseif (session('step') === 2)
            <div class="bg-gray-100 p-4 rounded shadow">
                <p><strong>AI:</strong> I am now <strong>{{ session('persona_name') }}</strong>, please paste the characteristics I should have.</p>
            </div>

            <form method="POST" action="{{ route('persona.submit') }}">
                @csrf
                <textarea name="persona_details" class="w-full border p-2 rounded my-2" rows="8" placeholder="Paste Q&A about your persona here..." required></textarea>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit Persona Info</button>
            </form>

        @elseif (session('step') === 3)
            <div class="bg-gray-100 p-4 rounded shadow whitespace-pre-wrap">
                <p><strong>AI:</strong> Here are the characteristics I have:</p>
                <p>{{ session('formatted_persona') }}</p>
                <p class="mt-4">What would you like to do next?</p>

                <form method="POST" action="{{ route('persona.generate') }}" class="mt-2 space-y-2">
                    @csrf
                    <input type="hidden" name="persona_name" value="{{ session('persona_name') }}">
                    <input type="hidden" name="persona_details" value="{{ session('persona_details') }}">

                    @foreach ([
                      'Create Ads', 'Create Keywords', 'Create Display URLs',
                      'Create Callouts', 'Create Extensions', 'Create Sitelinks'
                    ] as $action)
                        <button name="action" value="{{ $action }}" class="px-3 py-1 bg-purple-600 text-white rounded">{{ $action }}</button>
                    @endforeach
                </form>

                @elseif (session('step') === 4)
                    <div class="bg-yellow-100 p-4 rounded shadow whitespace-pre-wrap">
                        <p><strong>AI:</strong> Great! Now I will generate <strong>{{ session('selected_action') }}</strong> based on the persona.</p>
                        <p class="mt-2 font-mono text-sm text-gray-800">
                            {{ session('action_result') }}
                        </p>
                    </div>
                @endif
            </div>
@endsection
