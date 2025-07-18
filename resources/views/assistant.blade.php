<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Assistant</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
<div class="max-w-2xl mx-auto mt-10 space-y-4">
    <div class="bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> Hi, please tell me the name of the person I should become to create your buyer persona.</p>
    </div>

    <input id="personaName" type="text" class="w-full border p-2 rounded" placeholder="Please become a person named..." />

    <button onclick="confirmName()" class="bg-blue-600 text-white px-4 py-2 rounded">
        Confirm Name
    </button>

    <div id="step2" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> I am now <span id="personaLabel"></span>, please paste the characteristics I should have.</p>
        <textarea id="personaDetails" class="w-full border p-2 rounded mt-2" rows="8" placeholder="Paste Q&A about your persona here..."></textarea>
        <button onclick="sendPersonaDetails()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">
            Submit Persona Info
        </button>
    </div>

    <div id="step3" class="hidden bg-gray-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Here are the characteristics I have:</p>
        <p id="formattedPersona"></p>
        <p class="mt-4">What would you like to do next?</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
            @foreach (['Create Ads', 'Create Keywords', 'Create Display URLs', 'Create Callouts', 'Create Extensions', 'Create Sitelinks'] as $action)
                <button onclick="selectAction('{{ $action }}')" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded transition">
                    {{ $action }}
                </button>
            @endforeach
        </div>

    </div>

    <div id="step4" class="hidden bg-yellow-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Great! Now I will generate <strong id="selectedAction"></strong> based on the persona.</p>
        <article id="actionResult" class="prose prose-sm prose-pre:bg-transparent prose-pre:p-0 prose-headings:font-semibold prose-p:my-2 mt-4 text-gray-900 max-w-none"></article>
    </div>
</div>

<script>
    tailwind.config = {
        plugins: [tailwindcss.typography]
    }
    
    let personaName = '';
    function confirmName() {
        personaName = document.getElementById('personaName').value;
        if (personaName.trim()) {
            document.getElementById('personaLabel').innerText = personaName;
            document.getElementById('step2').classList.remove('hidden');
        }
    }

    async function sendPersonaDetails() {
        const details = document.getElementById('personaDetails').value;
        try {
            const res = await fetch('/persona', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    name: personaName,
                    details: details
                })
            });

            const data = await res.json();
            document.getElementById('formattedPersona').innerText = data.formatted;
            document.getElementById('step3').classList.remove('hidden');
        } catch (err) {
            alert('❌ Failed to send to AI:\n' + err.message);
        }
    }

    async function selectAction(action) {
        document.getElementById('selectedAction').innerText = action;
        document.getElementById('step4').classList.remove('hidden');

        const details = document.getElementById('personaDetails').value;

        try {
            const res = await fetch('/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: action,
                    persona_name: personaName,
                    persona_details: details
                })
            });

            const data = await res.json();
            document.getElementById('actionResult').innerText = data.result;
        } catch (err) {
            document.getElementById('actionResult').innerText = '❌ Failed to generate content.';
        }
    }
</script>
</body>
</html>
