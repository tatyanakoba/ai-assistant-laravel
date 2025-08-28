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
        <p><strong>AI:</strong> Hi, please tell me the name of the person I should become to create your buyer persona</p>
    </div>

    <input id="personaName" type="text" class="w-full border p-2 rounded" placeholder="Please become a person named (insert your name here)" />
    <button onclick="confirmName()" class="bg-blue-600 text-white px-4 py-2 rounded">Confirm Name</button>

    <!-- PART 1 -->
    <div id="step1" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> I am now <span id="personaLabel"></span>, please paste the answers to the following questions regarding your persona that should have:</p>
        <ol class="list-disc list-inside mt-2 text-sm text-gray-800">
            <li><strong>Market:</strong> In what sector or industry does the customer operate (e.g., finance, healthcare,     technology, manufacturing, etc.)?</li>
            <li><strong>Product/Service:</strong> What products or services does your customer sell, make, or research?</li>
            <li><strong>Pain Points:</strong> Describe the challenges or problems the customer has that are related to what you sell.</li>
        </ol>
        <textarea id="step1Input" class="w-full border p-2 rounded mt-2" rows="6"></textarea>
        <button onclick="submitStep1()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Persona Info</button>
    </div>

    <!-- PART 2 -->
    <div id="step2" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> Please paste the answers to the following questions regarding your persona that should have:</p>
        <ol class="list-disc list-inside text-sm text-gray-800">
            <li><strong>Company Size (Employees): </strong>What is the size of the business that employs them? (small business, start-up, funded startup, solopreneur, enterprise)</li>
            <li><strong>Company Size (Revenue): </strong>What is the annual sales range for this business?</li>
            <li><strong>Niche: </strong>Does the customer serve only a segment of the market? Describe that segment.</li>
            <li><strong>Age: </strong> What is the age range of this customer?</li>
            <li><strong>Job Title/Role: </strong>The specific role of the customer (e.g., CEO, CTO, Marketing Director)?</li>
            <li><strong>Possible Search Terms: </strong>What information might the customer search for?</li>
            <li><strong>Geographic Location: </strong>Where is the customer’s business located (e.g., North America, Europe, urban vs. rural).</li>
            <li><strong>Years in Business: </strong> How long has the customer’s company been in business?</li>
            <li><strong>Buying Authority: </strong> Does the customer have the power to make purchasing decisions, or do they need approval?</li>
            <li><strong>Company Growth Rate: </strong> Is the customer rapidly expanding, stable, or downsizing?</li>
            <li><strong>Technological Sophistication: </strong> How tech-savvy is the customer?</li>
            <li><strong>Preferred Communication Channel: </strong> Does the customer prefer email, phone calls, face-to-face meetings, or something else?</li>
            <li><strong>Organizational Structure: </strong> Does the customer have a centralized or decentralized decision-making process?</li>
            <li><strong>Please list your competitors: </strong> </li>
            <li><strong>Current Solutions: </strong> What products or services is the customer currently using that are similar to Azelis’s products?</li>
            <li><strong>Membership in Professional Organizations: </strong> Is the customer a member of any industry-specific groups or associations?</li>
            <li><strong>Type of Academic Institution or Company: </strong> What are some of the possible places where the customer may work or places similar to where this person may work.</li>
            <li><strong>Education Level: </strong> What is the customer’s formal education or training level?</li>
            <li><strong>Professional Goals: </strong> What does the customer aim to achieve in their position or for their company?</li>
            <li><strong>Preferred Content Types: </strong> How does the customer consume content (e.g., reading whitepapers, watching webinars, attending conferences)?</li>
        </ol>
        <textarea id="step2Input" class="w-full border p-2 rounded mt-2" rows="6"></textarea>
        <button onclick="submitStep2()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Demographic Info</button>
    </div>

    <!-- PART 3 -->
    <div id="step3" class="hidden bg-gray-100 p-4 rounded shadow">
        <p><strong>AI:</strong> Please paste the answers to the following questions regarding your persona that should have:</p>
        <ol class="list-disc list-inside text-sm text-gray-800">
            <li><strong>Opportunities: </strong> What does the customer see as an opportunity in their market?</li>
            <li><strong>Decision-making drivers: </strong>What factors most influence the customer’s purchasing decisions (e.g., ROI, efficiency, innovation, speed to results)?</li>
            <li><strong>Company Culture & Values: </strong>What are the customer’s ethos and principles guiding their company and might influence buying decisions?</li>
            <li><strong>Risk Tolerance: </strong>What is the customer’s willingness to try new solutions or stick with known vendors?</li>
            <li><strong>Information-Seeking Behaviors: </strong>How does the customer acquire information about their profession or industry (e.g., books, industry journals, attend webinars, or conferences)?</li>
            <li><strong>Trust Factors: </strong>What makes the customer trust a vendor or partner (e.g., case studies, referrals, customer stories, thought-leading content)</li>
            <li><strong>Budgetary Constraints: </strong>How does the budget affect the customer’s decisions and resource allocation challenges?</li>
            <li><strong>Change Readiness: </strong>Is the customer open to change and adopting new technologies or processes?</li>
            <li><strong>Post-purchase Expectations: </strong>: What are the customer’s needs after purchasing, such as customer support, training, or regular check-ins?</li>
        </ol>
        <textarea id="step3Input" class="w-full border p-2 rounded mt-2" rows="6"></textarea>
        <button onclick="submitStep3()" class="bg-green-600 text-white px-4 py-2 rounded mt-2">Submit Psychographic Info</button>
    </div>

    <!-- SUMMARY + ACTIONS -->
    <div id="step4" class="hidden bg-gray-100 p-4 rounded shadow whitespace-pre-wrap">
        <p><strong>AI:</strong> Here are the characteristics I have: (spells out your answers but makes them as if it has them)</p>
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
    if (personaName) {
        document.getElementById('personaLabel').innerText = personaName;
        document.getElementById('step1').classList.remove('hidden');
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
                body: JSON.stringify({
                    name: personaName,
                    details: `${step1}\n\n${step2}\n\n${step3}`
                })
            });


            const data = await res.json();
            if (data.formatted) {
                document.getElementById('formattedPersona').innerText = data.formatted;
                document.getElementById('step4').classList.remove('hidden');
            } else {
                throw new Error('No formatted data received');
            }
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
                    persona_details: `${step1}\n\n${step2}\n\n${step3}`,
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
