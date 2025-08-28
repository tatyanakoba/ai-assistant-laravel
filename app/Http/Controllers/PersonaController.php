<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use OpenAI\Laravel\Facades\OpenAI;

class PersonaController extends Controller
{
    public function step(Request $request)
    {
        $request->validate([
            'step' => 'required|in:1,2,3,summary,action',
            'name' => 'required|string',
            'data' => 'nullable|string',
            'action' => 'nullable|string',
            'specifics' => 'nullable|string',
        ]);

        $step = $request->input('step');
        $name = $request->input('name');
        $data = $request->input('data');
        $action = $request->input('action');
        $specifics = $request->input('specifics');

        $sessionKey = 'persona_' . md5($name);
        $sessionData = Session::get($sessionKey, [
            'name' => $name,
            'part1' => '',
            'part2' => '',
            'part3' => '',
            'history' => [],
        ]);

        try {
            if ($step === '1') {
                $sessionData['part1'] = $data;
                Session::put($sessionKey, $sessionData);
                return response()->json(['message' => 'Part 1 saved. Please proceed to Part 2.']);
            }

            if ($step === '2') {
                $sessionData['part2'] = $data;
                Session::put($sessionKey, $sessionData);
                return response()->json(['message' => 'Part 2 saved. Please proceed to Part 3.']);
            }

            if ($step === '3') {
                $sessionData['part3'] = $data;
                Session::put($sessionKey, $sessionData);
                return response()->json(['message' => 'Part 3 saved. Ready for summary.']);
            }

            if ($step === 'summary') {
                $allDetails = <<<EOT
Part 1:
{$sessionData['part1']}

Part 2:
{$sessionData['part2']}

Part 3:
{$sessionData['part3']}
EOT;

                $prompt = <<<EOT
You are now the persona named "{$name}". Here is the full persona information, broken into 3 parts:

{$allDetails}

Please summarize these characteristics as if you are {$name}, using first person ("I").
Then ask: "What would you like to do next?"
Options: Create ads, Create keywords, Create display URLs, Create callouts, Create extensions, Create sitelinks, Create audience.
EOT;

                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert marketing strategist.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                $summary = $response['choices'][0]['message']['content'];
                $sessionData['summary'] = $summary;
                Session::put($sessionKey, $sessionData);

                return response()->json(['summary' => $summary]);
            }

            if ($step === 'action') {
                $allDetails = <<<EOT
Part 1:
{$sessionData['part1']}

Part 2:
{$sessionData['part2']}

Part 3:
{$sessionData['part3']}
EOT;

                $prompt = "Act as {$name}. Based on this persona:\n{$allDetails}\nGenerate {$action}.";

                if (!empty($specifics)) {
                    $prompt .= "\n\nPlease include the following specifics:\n{$specifics}";
                }

                $response = OpenAI::chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are an expert marketing strategist.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                $result = $response['choices'][0]['message']['content'];

                // Зберігаємо в історії
                $sessionData['history'][] = [
                    'action' => $action,
                    'specifics' => $specifics,
                    'result' => $result,
                ];
                Session::put($sessionKey, $sessionData);

                return response()->json([
                    'result' => $result,
                    'history' => $sessionData['history'],
                    'nextPrompt' => 'Would you like to do anything else? [yes] [no]'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Persona step error: ' . $e->getMessage());
            return response()->json(['error' => 'AI error: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        return view('assistant');
    }
}
