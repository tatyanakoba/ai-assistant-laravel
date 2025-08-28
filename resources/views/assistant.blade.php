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
    <button onclick="confirmName()" class="bg-blue-600 text-white px-4 py-2 rounded">Confirm Name</button>

    <!-- PART 1 -->
    <div id="step1" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> I am now <span id="personaLabel"></span>, please paste the answers to the following questions:</p>
        <ul class="list-disc list-inside mt-2 text-sm text-gray-800">
            <li><strong>Market:</strong> In what sector or industry does the customer operate (e.g., finance, healthcare,     technology, manufacturing, etc.)?</li>
            <li><strong>Product/Service:</strong> What products or services does your customer sell, make, or research?</li>
            <li><strong>Pain Points:</strong> Describe the challenges or problems the customer has that are related to what you sell.</li>
        </ul>
        <textarea id="step1Input" class="w-full border p-2 rounded mt-2" rows="6"></textarea>
        <button onclick="submitStep1()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Persona Info</button>
    </div>

    <!-- PART 2 -->
    <div id="step2" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> Please paste the answers to the following demographic questions:</p>
        <ul class="list-disc list-inside text-sm text-gray-800">
            <li>Company Size (Employees & Revenue)</li>
            <li>Niche, Age, Job Title, Search Terms</li>
            <li>Location, Years in Business, Buying Authority</li>
            <li>Growth Rate, Tech Savvy, Communication Channel</li>
            <li>Competitors, Current Solutions</li>
            <li>Associations, Type of Company, Education, Goals</li>
            <li>Content Preferences</li>
        </ul>
        <textarea id="step2Input" class="w-full border p-2 rounded mt-2" rows="6"></textarea>
        <button onclick="submitStep2()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Demographic Info</button>
    </div>

    <!-- PART 3 -->
    <div id="step3" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> Please paste the answers to the following psychographic questions:</p>
        <ul class="list-disc list-inside text-sm text-gray-800">
            <li>Opportunities, Decision Drivers</li>
            <li>Company Culture, Risk Tolerance</li>
            <li>Info-Seeking, Trust Factors</li>
            <li>Budget Constraints, Change Readiness</li>
            <li>Post-Purchase Expectations</li>
        </ul>
        <textarea id="step3Input" class="w-full border p-2 rounded mt-2" rows="6"></textarea>
        <button onclick="submitStep3()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Psychographic Info</button>
    </div>

    <!-- SUMMARY + ACTIONS -->
    <div id="step4" class="hidden bg-gray-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Here are the characteristics I have:</p>
        <p id="formattedPersona"></p>

        <p class="mt-4">What would you like to do next?</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
            @foreach ([
                'Create Ads', 'Create Keywords', 'Create Display URLs',
                'Create Callouts', 'Create Extensions', 'Create Sitelinks', 'Create Audience'
            ] as $action)
                <button onclick="selectAction('{{ $action }}')" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded transition">
                    {{ $action }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- ACTION GENERATION -->
    <div id="step5" class="hidden bg-yellow-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Great! Now I will generate <strong id="selectedAction"></strong>.</p>
        <textarea id="actionSpecifics" class="w-full border p-2 rounded mt-4" rows="3" placeholder="Any specifics? (optional)"></textarea>
        <button onclick="submitAction()" class="mt-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">Generate</button>

        <pre id="actionResult" class="mt-4 p-4 bg-white rounded shadow border text-gray-900 leading-relaxed whitespace-pre-wrap font-sans text-sm"></pre>
    </div>
</div>

<script>
    let personaName = '', step1 = '', step2 = '', step3 = '';

    function confirmName() {
        personaName = document.getElementById('personaName').value.trim();
        if (personaName) {
            document.getElementById('personaLabel').innerText = personaName;
            document.getElementById('step1').classList.remove('hidden');
        }
    }

    function submitStep1() {
        step1 = document.getElementById('step1Input').value.trim();
        if (step1) document.getElementById('step2').classList.remove('hidden');
    }

    function submitStep2() {
        step2 = document.getElementById('step2Input').value.trim();
        if (step2) document.getElementById('step3').classList.remove('hidden');
    }

    async function submitStep3() {
        step3 = document.getElementById('step3Input').value.trim();
        if (!step3) return;

        try {
            const res = await fetch('/persona', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ name: personaName, details: step1 + "\n" + step2 + "\n" + step3 })
            });

            const data = await res.json();
            document.getElementById('formattedPersona').innerText = data.formatted;
            document.getElementById('step4').classList.remove('hidden');
        } catch (err) {
            alert('❌ Failed to send to AI:\n' + err.message);
        }
    }

    function selectAction(action) {
        document.getElementById('selectedAction').innerText = action;
        document.getElementById('step5').classList.remove('hidden');
    }

    async function submitAction() {
        const specifics = document.getElementById('actionSpecifics').value.trim();
        try {
            const res = await fetch('/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    action: document.getElementById('selectedAction').innerText,
                    persona_name: personaName,
                    persona_details: step1 + "\n" + step2 + "\n" + step3,
                    specifics: specifics
                })
            });

            const data = await res.json();
            document.getElementById('actionResult').innerText = data.result + "\n\nWould you like help with anything else? [Yes] [No]";
        } catch (err) {
            document.getElementById('actionResult').innerText = '❌ Failed to generate content.';
        }
    }
</script>
</body>
</html>
