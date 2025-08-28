
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
    <button onclick="confirmName()" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">Confirm Name</button>

    <div id="step2" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> I am now <span id="personaLabel"></span>, please paste the answers to the following questions regarding your persona that should have:</p>
        <ul class="list-disc list-inside mt-2 text-sm text-gray-800">
            <li><strong>Market:</strong> In what sector or industry does the customer operate (e.g., finance, healthcare, technology, manufacturing, etc.)?</li>
            <li><strong>Product/Service:</strong> What products or services does your customer sell, make, or research?</li>
            <li><strong>Pain Points:</strong> Describe the challenges or problems the customer has that are related to what you sell.</li>
        </ul>
        <textarea id="personaDetails" class="w-full border p-2 rounded mt-2" rows="8" placeholder="Paste Q&A about your persona here..."></textarea>
        <button onclick="sendPersonaDetails()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Persona Info</button>
    </div>

    <div id="step3" class="hidden bg-gray-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Here are the characteristics I have:</p>
        <p id="formattedPersona"></p>
        <p class="mt-4">What would you like to do next?</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
            @foreach (['Create Ads', 'Create Keywords', 'Create Display URLs', 'Create Callouts', 'Create Extensions', 'Create Sitelinks', 'Create Audience'] as $action)
                <button onclick="selectAction('{{ $action }}')" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded transition">
                    {{ $action }}
                </button>
            @endforeach
        </div>
    </div>

    <div id="step4" class="hidden bg-yellow-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Great! Now I will generate <strong id="selectedAction"></strong> based on the persona.</p>
        <textarea id="actionSpecifics" class="w-full border p-2 rounded mt-4" rows="3" placeholder="Are there any specifics for this action I should follow? (optional)"></textarea>
        <button onclick="submitAction()" class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Generate Content</button>
        <div id="actionResultsHistory" class="mt-6 space-y-4"></div>
    </div>
</div>

<script>
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
                body: JSON.stringify({ name: personaName, details: details })
            });
            const data = await res.json();
            document.getElementById('formattedPersona').innerText = data.formatted;
            document.getElementById('step3').classList.remove('hidden');
        } catch (err) {
            alert('❌ Failed to send to AI:\n' + err.message);
        }
    }

    function selectAction(action) {
        document.getElementById('selectedAction').innerText = action;
        document.getElementById('step4').classList.remove('hidden');
    }

    async function submitAction() {
        const action = document.getElementById('selectedAction').innerText;
        const details = document.getElementById('personaDetails').value;
        const specifics = document.getElementById('actionSpecifics').value;

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
                    persona_details: details,
                    specifics: specifics
                })
            });

            const data = await res.json();

            const resultContainer = document.createElement('div');
            resultContainer.classList.add('mt-6', 'bg-white', 'rounded', 'shadow', 'p-4', 'border');

            const title = document.createElement('h3');
            title.classList.add('text-lg', 'font-bold', 'mb-2');
            title.innerText = `Generated ${action}`;

            const content = document.createElement('pre');
            content.classList.add('whitespace-pre-wrap', 'text-gray-900', 'text-sm');
            content.innerText = data.result;

            resultContainer.appendChild(title);
            resultContainer.appendChild(content);

            const followUp = document.createElement('div');
            followUp.classList.add('mt-4');
            followUp.innerHTML = `
                <p class="mt-4 font-medium">Would you like to do anything else?</p>
                <button onclick="restartActions()" class="mt-2 mr-2 bg-blue-600 text-white px-3 py-1 rounded">Yes</button>
                <button onclick="endSession()" class="mt-2 bg-gray-400 text-white px-3 py-1 rounded">No</button>
            `;
            resultContainer.appendChild(followUp);

            document.getElementById('actionResultsHistory').appendChild(resultContainer);
            document.getElementById('actionSpecifics').value = '';

        } catch (err) {
            alert('❌ Failed to generate content:\n' + err.message);
        }
    }

    function restartActions() {
        document.getElementById('step4').scrollIntoView({ behavior: 'smooth' });
    }

    function endSession() {
        alert('Thank you! Session complete.');
    }
</script>
</body>
</html>
